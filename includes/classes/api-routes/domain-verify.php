<?php

namespace WPMP\API_Routes;

use WPMP\Options;

defined( 'ABSPATH' ) || exit;

class Domain_Verify extends \WPMP\API_Route {
	/**
	 * Validate the /admin/domain-verify/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'wpmp' ), [ 'status' => 403 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: try to verify a domain via SparkPost API.
	 *
	 * @return array
	 */
	public function process() {
		$domain = sanitize_text_field( $this->get_param( 'domain' ) );
		$api    = wpmp_get_sparkpost_api();
		$domain = Options\get_plugin_option( 'domain' );

		$api->verify_domain( $domain );

		$response = $api->get_response();

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$dkim_record_is_valid = ( isset( $response->results->dkim_status ) && $response->results->dkim_status === 'valid' );
		$error                = isset( $response->results->dns->dkim_error ) ? $response->results->dns->dkim_error : 'error';

		if ( $dkim_record_is_valid ) {
			Options\update_plugin_option( "domain_dkim_record_{$domain}_is_valid", 1 );
			$message = sprintf( __( 'The DNS records for %1$s were successfully verified. It may take a few minutes before being able to send e-mails with this domain.', 'wpmp' ), $domain );

			do_action( 'wpmp/domain_validated', $domain );
		} else {
			Options\update_plugin_option( "domain_dkim_record_{$domain}_is_valid", 0 );
			$message = sprintf( __( 'The DNS records for %1$s are invalid (%2$s).', 'wpmp' ), $domain, $error );

			do_action( 'wpmp/domain_unvalidated', $domain );
		}

		Options\update_plugin_option( "domain_dkim_record_{$domain}_verified", current_time( 'timestamp', true ) );

		$fields = [
			'sending_domain'              => $domain,
			'domain_dkim_record_is_valid' => $dkim_record_is_valid,
			'domain_dkim_record_verified' => wp_date( 'Y/m/d H:i', current_time( 'timestamp', true ) ),
			'domain_dkim_record_error'    => $error,
		];

		if ( $dkim_record_is_valid ) {
			$fields['sender_mail']        = wpmp_get_sender_mail();
			$fields['sender_mail_prefix'] = wpmp_get_sender_mail( true );
		}

		return [
			'success' => true,
			'message' => $message,
			'fields'  => (object) $fields,
		];
	}
}
