<?php

namespace WPMP\API_Routes;

use WPMP\API_Licensing;
use WPMP\Options;
use WPMP\Helpers;

defined( 'ABSPATH' ) || exit;

class License_Deactivate extends \WPMP\API_Route {
	/**
	 * Validate the /admin/license-deactivate/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'wpmp' ), [ 'status' => 403 ] );
		}

		if ( empty( $this->get_param( 'license' ) ) ) {
			return new \WP_Error( 'missing_license', __( 'You need to provide a license.', 'wpmp' ), [ 'status' => 401 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: try to deactivate the license.
	 *
	 * @return array
	 */
	public function process() {
		$license      = sanitize_text_field( $this->get_param( 'license' ) );
		$api          = new API_Licensing();
		$deactivation = $api->deactivate( $license );

		if ( is_wp_error( $deactivation ) ) {
			return $deactivation;
		}

		Options\update_plugin_option( 'license_deactivated', current_time( 'timestamp', true ) );
		Options\delete_plugin_option( 'license_activated' );

		do_action( 'wpmp/license_deactivated', $license );

		Options\delete_plugin_option( 'sparkpost_api_key' );

		return [
			'success' => true,
			'message' => __( 'License successfully deactivated.', 'wpmp' ),
		];
	}
}
