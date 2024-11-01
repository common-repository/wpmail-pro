<?php

namespace WPMP;

use \WPMP\Options;
use \WPMP\Webhooks;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles communications with the SparkPost API.
 */
class API_Sparkpost {
	/**
	 * Request arguments.
	 *
	 * @var object
	 */
	protected $args = null;

	/**
	 * Response object.
	 *
	 * @var object
	 */
	protected $response = null;

	/**
	 * Construct the API object.
	 *
	 * @param string $api_key
	 */
	public function __construct( $api_key = null ) {
		$this->args = (object) [
			'api_key' => $api_key,
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
	 * Get base API URL.
	 *
	 * @return string
	 */
	public function get_api_url() {
		return Config::SPARKPOST_API_BASE_URL;
	}

	/**
	 * Get all argument.
	 *
	 * @return mixed
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
	 * Get the whole API response object, or just a key.
	 *
	 * @param string|null $key
	 * @return mixed
	 */
	public function get_response( $key = null ) {
		if ( $key ) {
			return isset( $this->response->{$key} ) ? $this->response->{$key} : null;
		}

		return $this->response;
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
	 * Send a request to SparkPost API.
	 *
	 * @param string $route
	 * @param array $body
	 * @param string $method
	 * @return object|\WP_Error
	 */
	protected function request( $route = '', $body = [], $method = 'POST' ) {
		if ( $method !== 'GET' ) {
			$body = wp_json_encode( apply_filters( 'wpmp/sparkpost_api/request_body', $body ) );
		}

		$args = [
			'timeout' => 15,
			'body'    => $body,
			'method'  => $method,
			'headers' => [
				'Content-Type'  => 'application/json',
				'Authorization' => $this->get_arg( 'api_key' ),
			],
		];

		$url      = sprintf( '%1$s%2$s', trailingslashit( $this->get_api_url() ), $route );
		$response = wp_remote_request(
			apply_filters( 'wpmp/sparkpost_api/request_url', $url ),
			apply_filters( 'wpmp/sparkpost_api/request_args', $args )
		);

		$decoded_response = json_decode( wp_remote_retrieve_body( $response ) );

		if ( isset( $decoded_response->errors ) ) {
			$error       = $decoded_response->errors[0];
			$code        = isset( $error->code ) ? (int) $error->code : 0;
			$description = isset( $error->description ) ? $error->description : ( isset( $error->message ) ? $error->message : __( 'Something went wrong.', 'wpmp' ) );
			$response    = new \WP_Error( "wpmp_sparkpost_api_error_{$code}", esc_html( $description ), $error );
		}

		do_action( 'wpmp/sparkpost_api/response', $decoded_response, $response );

		return is_wp_error( $response ) ? $response : $decoded_response;
	}

	/**
	 * Send an email using SparkPost API.
	 *
	 * @param array $body
	 * @return void
	 */
	public function send_email( $body = [] ) {
		$this->response = $this->request( 'transmissions', $body );
	}

	/**
	 * List domains.
	 *
	 * @return void
	 */
	public function list_domains() {
		$this->response = $this->request( 'sending-domains', [], 'GET' );
	}

	/**
	 * Create a domain.
	 *
	 * @param string $domain
	 * @param boolean $delete_and_recreate_if_existing
	 * @return void
	 */
	public function create_domain( $domain = '', $delete_and_recreate_if_existing = false ) {
		$this->response = $this->request( 'sending-domains', [
			'domain'        => $domain,
			'generate_dkim' => true,
		] );

		// If domain is already registered, try to delete it and create it again.
		if ( is_wp_error( $this->response ) && $delete_and_recreate_if_existing ) {
			$error_data = $this->response->get_error_data();

			if ( isset( $error_data->code ) && (int) $error_data->code === 1602 ) {
				$this->delete_domain( $domain );
				$this->create_domain( $domain, false );
			}
		}
	}

	/**
	 * Delete a domain.
	 *
	 * @param string $domain
	 * @return void
	 */
	public function delete_domain( $domain = '' ) {
		$this->response = $this->request( "sending-domains/{$domain}", [], 'DELETE' );
	}

	/**
	 * Verify a domain.
	 *
	 * @param string $domain
	 * @return void
	 */
	public function verify_domain( $domain = '' ) {
		$this->response = $this->request( "sending-domains/{$domain}/verify", [
			'dkim_verify' => true,
		] );
	}

	/**
	 * Get metrics summary.
	 *
	 * @param array $dates Array of 2 dates (from/to) in format of YYYY-MM-DDTHH:MM.
	 * @param array $metrics
	 * @return void
	 */
	public function get_metrics( $dates, $metrics = [] ) {
		if ( empty( $metrics ) ) {
			$metrics = [
				'count_unique_clicked'          => 'clicked',
				'count_targeted'                => 'targeted',
				'count_rejected'                => 'rejected',
				'count_accepted'                => 'accepted',
				'count_bounce'                  => 'bounce',
				'count_unique_confirmed_opened' => 'opened',
				'count_admin_bounce'            => 'admin_bounce',
			];
		}

		$domain = Options\get_plugin_option( 'domain' );

		if ( empty( $domain ) ) {
			$domain = Config::DEFAULT_SENDING_DOMAIN;
		}

		$temp_response = $this->request(
			sprintf(
				'metrics/deliverability?from=%1$s&to=%2$s&metrics=%3$s',
				$dates['from'],
				$dates['to'],
				implode( ',', array_keys( $metrics ) ),
				$domain,
				sprintf( '%1$sT23:59', date( 'Y-m-d', time() ) )
			),
			[],
			'GET'
		);

		if ( is_wp_error( $temp_response ) ) {
			$this->response = $temp_response;
			return;
		}

		$response = [];

		foreach ( (array) $temp_response->results[0] as $key => $value ) {
			$response_key              = array_key_exists( $key, $metrics ) ? $metrics[ $key ] : $key;
			$response[ $response_key ] = $value;
		}

		$this->response = $response;
	}

	/**
	 * Get events for a transmission.
	 *
	 * @param integer $transmission_id
	 * @return void
	 */
	public function get_transmission_details( $transmission_id, $events = null ) {
		$url_format = 'events/message?transmissions=%1$s&to=%3$s';

		if ( ! is_null( $events ) ) {
			if ( is_array( $events ) ) {
				$events = implode( ',', $events );
			}

			$url_format = 'events/message?transmissions=%1$s&events=%2$s&to=%3$s';
		}

		$this->response = $this->request(
			sprintf(
				$url_format,
				$transmission_id,
				$events,
				date( 'Y-m-d\TH:i:s\Z', time() )
			),
			[],
			'GET'
		);
	}

	//=========================================================================
	//                                                                         
	//  ##      ##  #####  #####   ##   ##   #####    #####   ##  ##   ####  
	//  ##      ##  ##     ##  ##  ##   ##  ##   ##  ##   ##  ## ##   ##     
	//  ##  ##  ##  #####  #####   #######  ##   ##  ##   ##  ####     ###   
	//  ##  ##  ##  ##     ##  ##  ##   ##  ##   ##  ##   ##  ## ##      ##  
	//   ###  ###   #####  #####   ##   ##   #####    #####   ##  ##  ####   
	//                                                                         
	//=========================================================================

	/**
	 * Get all webhooks.
	 *
	 * @return array
	 */
	public function get_webhooks() {
		$this->response = $this->request( 'webhooks', [], 'GET' );
	}

	/**
	 * Delete an existing webhook.
	 *
	 * @param string $id
	 * @return array
	 */
	public function delete_webhook( $id = '' ) {
		$this->response = $this->request( "webhooks/{$id}", [], 'DELETE' );
	}

	/**
	 * Create the webhook in charge of pinging our site for any transmission delivery info.
	 *
	 * @return array
	 */
	public function create_delivery_webhook() {
		$credentials = Options\get_plugin_option( 'webhook_credentials', null );

		if ( empty ( $credentials ) || is_null( $credentials ) ) {
			$credentials = Webhooks\generate_basic_auth_credentials( \WPMP\Helpers\is_active_on_multisite() );
		}

		$args = [
			'name'             => __( 'Status tracking', 'wpmp' ),
			'target'           => Config::get_webhook_url( 'transmission-status-update' ),
			'events'           => Config::WEBHOOK_EVENTS,
			'auth_type'        => 'basic',
			'auth_credentials' => (object) [
				'username' => $credentials['username'],
				'password' => $credentials['password'],
			],
		];

		$this->response = $this->request(
			'webhooks',
			$args,
			'POST'
		);
	}
}
