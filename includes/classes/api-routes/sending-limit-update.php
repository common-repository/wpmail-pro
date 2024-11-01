<?php

namespace WPMP\API_Routes;

use \WPMP\Config;
use \WPMP\Options;

defined( 'ABSPATH' ) || exit;

class Sending_Limit_Update extends \WPMP\API_Route {
	/**
	 * Validate the /webhook/sending-limit-update/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		$data = $this->get_request()->get_json_params();

		if ( ! isset( $data['instance_id'] ) || $data['instance_id'] !== Options\get_plugin_option( 'license_instance_id' ) ) {
			return new \WP_Error( 'invalid_instance_id', __( 'You need to provide a valid instance ID.', 'wpmp' ), [ 'status' => 401 ] );
		}

		if ( ! isset( $data['license'] ) || $data['license'] !== Options\get_plugin_option( 'license' ) ) {
			return new \WP_Error( 'invalid_license', __( 'You need to provide a valid license.', 'wpmp' ), [ 'status' => 401 ] );
		}

		if ( ! isset( $data['free_plan'], $data['flag_action'] ) || ! in_array( $data['flag_action'], [ 'flag', 'unflag' ], true ) ) {
			return new \WP_Error( 'missing_parameters', __( 'Some parameters are missing or invalid.', 'wpmp' ), [ 'status' => 401 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: try to get metrics from SparkPost API.
	 *
	 * @return array
	 */
	public function process() {
		$data = $this->get_request()->get_json_params();

		$plan_type = (bool) $data['free_plan'] ? 'free' : 'paid';
		$action    = sanitize_text_field( $data['flag_action'] );

		if ( $action === 'flag' ) {
			Options\update_plugin_option( 'sending_limit_is_flagged', time() );
			Options\update_plugin_option( 'sending_limit_plan', $plan_type );
		} elseif ( $action === 'unflag' ) {
			Options\delete_plugin_option( 'sending_limit_is_flagged' );
			Options\delete_plugin_option( 'sending_limit_plan' );
		}

		return [
			'success' => true,
		];
	}
}
