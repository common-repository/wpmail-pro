<?php

namespace WPMP\API_Routes;

use WPMP\API_Licensing;
use WPMP\Options;
use WPMP\Helpers;

defined( 'ABSPATH' ) || exit;

class Metrics_Get extends \WPMP\API_Route {
	protected $ranges = [ 'last24Hours', 'last7Days', 'thisMonth', 'lastMonth' ];

	/**
	 * Validate the /admin/metrics-get/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'wpmp' ), [ 'status' => 403 ] );
		}

		if ( empty( $this->get_param( 'range' ) ) || ! in_array( $this->get_param( 'range' ), $this->ranges, true ) ) {
			return new \WP_Error( 'invalid_parameter', __( 'Please provide a valid time range.', 'wpmp' ), [ 'status' => 401 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: try to get metrics from SparkPost API.
	 *
	 * @return array
	 */
	public function process() {
		$force = (bool) $this->get_param( 'force' );
		$range = sanitize_text_field( $this->get_param( 'range' ) );
		$dates = $this->get_range_from( $range );
		$api   = wpmp_get_sparkpost_api();

		// Get metrics.
		$api->get_metrics( $dates );

		if ( is_wp_error( $api->get_response() ) ) {
			return $api->get_response();
		}

		$metrics = $api->get_response();

		return [
			'success' => true,
			'message' => null,
			'fields'  => (object) [
				'metrics' => (object) [
					'data' => $metrics,
					'from' => $dates['from'],
					'to'   => $dates['to'],
				],
				'sending_limit' => wpmp_get_sending_limit_data(),
			],
		];
	}

	/**
	 * Calculate the FROM and TO dates from the filter range string.
	 *
	 * @param string $range
	 * @return array
	 */
	protected function get_range_from( $range = 'thisMonth' ) {
		$timestamp_first_activation = (int) Options\get_plugin_option( 'license_first_activation' );

		switch ( $range ) {
			default:
			case 'last24Hours':
				$from = date( 'Y-m-d\TH:i', strtotime( '24 hours ago' ) );
				$to   = date( 'Y-m-d\TH:i', time() );
				break;

			case 'last7Days':
				$from = date( 'Y-m-d\TH:i', strtotime( '7 days ago' ) );
				$to   = date( 'Y-m-d\TH:i', time() );
				break;

			case 'thisMonth':
				$from = date( 'Y-m-d\T00:00', strtotime( 'First day of this month' ) );
				$to   = date( 'Y-m-d\TH:i', time() );
				break;

			case 'lastMonth':
				$from = date( 'Y-m-d\T00:00', strtotime( 'First day of last month' ) );
				$to   = date( 'Y-m-d\T23:59', strtotime( 'Last day of last month' ) );
				break;
		}

		return [ 'from' => $from, 'to' => $to ];
	}
}
