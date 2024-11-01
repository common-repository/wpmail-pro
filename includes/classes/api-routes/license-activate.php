<?php

namespace WPMP\API_Routes;

use WPMP\API_Licensing;
use WPMP\Options;
use WPMP\Helpers;

defined( 'ABSPATH' ) || exit;

class License_Activate extends \WPMP\API_Route {
	/**
	 * Validate the /admin/license-activate/ AJAX request.
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
	 * Process a valid request: try to activate the license.
	 *
	 * @return array
	 */
	public function process() {
		$license    = sanitize_text_field( $this->get_param( 'license' ) );
		$api        = new API_Licensing();
		$activation = $api->activate( $license );

		if ( is_wp_error( $activation ) ) {
			return $activation;
		}

		Options\update_plugin_option( 'license', $license );
		Options\update_plugin_option( 'license_activated', current_time( 'timestamp', true ) );
		Options\update_plugin_option( 'sparkpost_api_key', Helpers\crypt( $activation->sparkpost_api_key ) );
		Options\delete_plugin_option( 'license_deactivated' );

		if ( ! Options\has_plugin_option( 'license_first_activation' ) ) {
			Options\update_plugin_option( 'license_first_activation', current_time( 'timestamp', true ) );
		}

		do_action( 'wpmp/license_activated', $license );

		return [
			'success' => true,
			'message' => __( 'License successfully activated.', 'wpmp' ),
		];
	}
}
