<?php

namespace WPMP\API_Routes;

use \WPMP\Options;

defined( 'ABSPATH' ) || exit;

class Fields_Save extends \WPMP\API_Route {
	/**
	 * Validate the /admin/fields-save/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'wpmp' ), [ 'status' => 403 ] );
		}

		if ( empty( $this->get_param( 'fields' ) ) ) {
			return new \WP_Error( 'missing_fields', __( 'You need to provide some fields.', 'wpmp' ), [ 'status' => 401 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: save fields.
	 *
	 * @return array
	 */
	public function process() {
		$fields = $this->get_param( 'fields' );

		foreach ( $fields as $name => $value ) {
			$this->update_field( $name, sanitize_text_field( $value ), $fields );
		}

		return [
			'success' => true,
			'message' => __( 'Settings saved.', 'wpmp' ),
		];
	}

	/**
	 * Update a single field.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @param array $all_fields
	 * @return void
	 */
	protected function update_field( $name, $value, $all_fields ) {
		switch ( $name ) {
			default:
				Options\update_plugin_option( $name, $value );
				break;

			case 'sender_mail_prefix':
				$prefix = $value;
				$prefix = ! empty( $value ) ? $prefix : 'contact';
				$suffix = isset( $all_fields['sender_mail_suffix'] ) ? $all_fields['sender_mail_suffix'] : null;
				$suffix = ! is_null( $suffix ) ? $suffix : Options\get_plugin_option( 'domain' );

				Options\update_plugin_option( $name, $prefix );
				Options\update_plugin_option( 'sender_mail', sprintf( '%1$s@%2$s', $prefix, $suffix ) );
				break;
		}
	}
}
