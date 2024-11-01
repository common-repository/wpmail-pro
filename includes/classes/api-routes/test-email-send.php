<?php

namespace WPMP\API_Routes;

use \WPMP\Options;

defined( 'ABSPATH' ) || exit;

class Test_Email_Send extends \WPMP\API_Route {
	/**
	 * Validate the /admin/send-test-email/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'wpmp' ), [ 'status' => 403 ] );
		}

		if ( empty( $this->get_param( 'email' ) ) ) {
			return new \WP_Error( 'missing_email', __( 'You need to provide an email address.', 'wpmp' ), [ 'status' => 401 ] );
		}

		if ( ! is_email( $this->get_param( 'email' ) ) ) {
			return new \WP_Error( 'invalid_email', __( 'Please provide a valid email address.', 'wpmp' ), [ 'status' => 401 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: send e-mail.
	 *
	 * @return array
	 */
	public function process() {
		$email = $this->get_param( 'email' );

		if ( ! wpmp_can_send_mail() ) {
			return [
				'success' => false,
				'message' => __( 'Please activate your license in the Settings tab and configure your Sending Domain in the DNS tab first.', 'wpmp' ),
			];	
		}

		$mail_sent = wp_mail(
			$email,
			__( 'This is your WP Mail Pro test e-mail', 'wpmp' ),
			sprintf( __( 'This is your WP Mail Pro test e-mail sent from <a href="%1$s">the %2$s website</a>.', 'wpmp' ), esc_url( home_url() ), get_bloginfo( 'name' ) ),
			[ 'Content-Type: text/html; charset=UTF-8' ]
		);

		if ( ! $mail_sent ) {
			return [
				'success' => false,
				'message' => __( 'Unable to send a test e-mail. Check the Logs tab to get more information.', 'wpmp' ),
			];
		}

		return [
			'success' => true,
			'message' => __( 'Test e-mail sent.', 'wpmp' ),
		];
	}

	/**
	 * Set test e-mail content type to HTML.
	 *
	 * @return string
	 */
	public function set_html_content_type() {
		return 'text/html';
	}
}
