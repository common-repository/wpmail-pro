<?php

namespace WPMP\API_Routes;

use WPMP\Options;

defined( 'ABSPATH' ) || exit;

class Domain_Create extends \WPMP\API_Route {
	/**
	 * Validate the /admin/domain-create/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'wpmp' ), [ 'status' => 403 ] );
		}

		if ( empty( $this->get_param( 'domain' ) ) ) {
			return new \WP_Error( 'missing_domain', __( 'You need to provide a domain.', 'wpmp' ), [ 'status' => 401 ] );
		}

		if ( ! preg_match( "/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $this->get_param( 'domain' ) ) ||
			! preg_match( "/^.{1,253}$/", $this->get_param( 'domain' ) ) ||
			! preg_match( "/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $this->get_param( 'domain' ) ) ) {
				return new \WP_Error( 'invalid_domain', __( 'Please provide a valid domain name.', 'wpmp' ), [ 'status' => 401 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: try to register the domain.
	 *
	 * @return array
	 */
	public function process() {
		$domain = sanitize_text_field( $this->get_param( 'domain' ) );
		$api    = wpmp_get_sparkpost_api();

		$api->create_domain( $domain, true );

		if ( is_wp_error( $api->get_response() ) ) {
			return $api->get_response();
		}

		$dkim        = $api->get_response()->results->dkim;
		$domain      = sanitize_text_field( $dkim->signing_domain );
		$dkim_record = (object) [
			'hostname' => sprintf( '%1$s._domainkey.%2$s', sanitize_text_field( $dkim->selector ), sanitize_text_field( $domain ) ),
			'value'    => sprintf( 'v=DKIM1; k=rsa; h=sha256; p=%1$s', sanitize_text_field( $dkim->public ) ),
		];

		Options\update_plugin_option( 'domain', $domain );
		Options\update_plugin_option( "domain_dkim_{$domain}_raw", $dkim );
		Options\update_plugin_option( "domain_dkim_{$domain}_record", $dkim_record );
		Options\update_plugin_option( 'domain_created', current_time( 'timestamp', true ) );
		Options\delete_plugin_option( "domain_dkim_record_{$domain}_is_valid" );
		Options\delete_plugin_option( "domain_dkim_record_{$domain}_verified" );

		return [
			'success' => true,
			'message' => __( 'Domain successfully created.', 'wpmp' ),
			'fields'  => (object) [
				'sending_domain'                 => $domain,
				'current_sending_domain'         => $domain,
				'sending_domain_record_hostname' => $dkim_record->hostname,
				'sending_domain_record_value'    => $dkim_record->value,
			]
		];
	}
}
