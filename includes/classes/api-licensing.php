<?php

namespace WPMP;

use \WPMP\Options;
use \WPMP\Config;

defined( 'ABSPATH' ) || exit;

/**
 * Main class in charge of communicating with Woo Software Addon API.
 */
class API_Licensing {
	protected $product_id = 11;

	/**
	 * Request arguments.
	 *
	 * @var array
	 */
	protected $args = null;

	/**
	 * Construct the API object.
	 */
	public function __construct() {
		$this->args = [
			'product_id'  => $this->product_id,
			'instance'    => Options\get_plugin_option( 'license_instance_id' ),
			'object'      => str_ireplace( array( 'http://', 'https://' ), '', home_url() ),
			'webhook_url' => Config::get_webhook_url( 'sending-limit-update' ),
		];
	}

	//===========================================================
	//                                                           
	//   ####    #####  ######  ######  #####  #####     ####  
	//  ##       ##       ##      ##    ##     ##  ##   ##     
	//  ##  ###  #####    ##      ##    #####  #####     ###   
	//  ##   ##  ##       ##      ##    ##     ##  ##      ##  
	//   ####    #####    ##      ##    #####  ##   ##  ####   
	//                                                           
	//===========================================================

	/**
	 * Get the base URL for API calls.
	 *
	 * @return string
	 */
	public function get_base_url() {
		return 'https://www.wpmailpro.com/?wc-api=wc-am-api';
	}

	/**
	 * Get a specific argument.
	 *
	 * @return array
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * Get a specific argument.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get_arg( $key ) {
		return isset( $this->args->{$key} ) ? $this->args->{$key} : null;
	}

	/**
	 * Get current license stored in options table.
	 *
	 * @return void
	 */
	public static function get_current_license() {
		return Options\get_plugin_option( 'license' );
	}

	/**
	 * Is license activated?
	 *
	 * @return boolean
	 */
	public static function is_activated() {
		return (int) Options\get_plugin_option( 'license_activated', 0 ) > 0;
	}

	//=========================
	//                         
	//    ###    #####   ##  
	//   ## ##   ##  ##  ##  
	//  ##   ##  #####   ##  
	//  #######  ##      ##  
	//  ##   ##  ##      ##  
	//                         
	//=========================

	/**
	* Activate a license.
	*
	* @return object|\WP_Error
	*/
	public function activate( $license = '' ) {
		$body = wp_parse_args( [
			'wc_am_action' => 'activate',
			'api_key'      => $license,
		], $this->get_args() );

		$request  = wp_remote_post( add_query_arg( urlencode_deep( $body ), $this->get_base_url() ), [ 'timeout' => 15 ] );
		$response = json_decode( wp_remote_retrieve_body( $request ) );

		do_action( 'wpmp/licensing_api/response', $response );

		if ( isset( $response->activated, $response->success ) &&
			(int) $response->success === 1 &&
			(int) $response->activated === 1
		) {
			return (object) [
				'success'           => true,
				'sparkpost_api_key' => isset( $response->data->user_account_key ) ? $response->data->user_account_key : '',
			];
		}

		return new \WP_Error(
			'license_activation_api_error',
			sprintf( __( 'An error occured while trying to activate your license key: "%1$s".', 'wpmp' ), isset( $response->error ) ? esc_html( $response->error ) : __( 'error', 'wpmp' ) ),
			[ 'response' => $response, 'body' => $body ]
		);
	}

	/**
	* Deactivate a license.
	*
	* @return object|\WP_Error
	*/
	public function deactivate( $license = '' ) {
		$body = wp_parse_args( [
			'wc_am_action' => 'deactivate',
			'api_key'      => $license,
		], $this->get_args() );

		$request  = wp_remote_post( add_query_arg( urlencode_deep( $body ), $this->get_base_url() ), [ 'timeout' => 15 ] );
		$response = json_decode( wp_remote_retrieve_body( $request ) );

		do_action( 'wpmp/licensing_api/response', $response );

		if ( isset( $response->deactivated, $response->success ) &&
			(int) $response->success === 1 &&
			(int) $response->deactivated === 1
		) {
			return (object) [
				'success' => true,
			];
		}

		return new \WP_Error(
			'license_deactivation_api_error',
			sprintf( __( 'An error occured while trying to deactivate your license key: "%1$s".', 'wpmp' ), isset( $response->error ) ? esc_html( $response->error ) : __( 'error', 'wpmp' ) ),
			[ 'response' => $response, 'body' => $body ]
		);
	}

	/**
	* Check the validity of a license.
	*
	* @return object|\WP_Error
	*/
	public function is_valid( $license = '' ) {
		$body = wp_parse_args( [
			'wc_am_action' => 'status',
			'api_key'      => $license,
		], $this->get_args() );

		$request  = wp_remote_post( add_query_arg( urlencode_deep( $body ), $this->get_base_url() ), [ 'timeout' => 15 ] );
		$response = json_decode( wp_remote_retrieve_body( $request ) );

		do_action( 'wpmp/licensing_api/response', $response );

		if ( isset( $response->status_check ) &&
			$response->status_check === 'active'
		) {
			return true;
		}

		return false;
	}

	/**
	* Getting sending limit data.
	*
	* @return object|\WP_Error
	*/
	public function get_sending_limit_data( $license = '' ) {
		$body = wp_parse_args( [
			'wc_am_action' => 'status',
			'api_key'      => $license,
		], $this->get_args() );

		$request  = wp_remote_post( add_query_arg( urlencode_deep( $body ), $this->get_base_url() ), [ 'timeout' => 15 ] );
		$response = json_decode( wp_remote_retrieve_body( $request ) );

		do_action( 'wpmp/licensing_api/response', $response );

		return (object) [
			'plan'    => isset( $response->data->plan ) ? sanitize_text_field( $response->data->plan ) : null,
			'current' => isset( $response->data->user_sending_count ) ? (int) $response->data->user_sending_count : null,
			'max'     => isset( $response->data->user_sending_limit ) ? (int) $response->data->user_sending_limit : null,
		];
	}
}
