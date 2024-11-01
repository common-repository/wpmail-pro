<?php

namespace WPMP;

defined( 'ABSPATH' ) || exit;

use \WPMP\Webhooks;

class Public_API {
	/**
	 * Store our admin routes slugs and classes.
	 *
	 * @var array
	 */
	protected $admin_routes = [
		'license-activate'   => \WPMP\API_Routes\License_Activate::class,
		'license-deactivate' => \WPMP\API_Routes\License_Deactivate::class,
		'domain-create'      => \WPMP\API_Routes\Domain_Create::class,
		'domain-verify'      => \WPMP\API_Routes\Domain_Verify::class,
		'metrics-get'        => \WPMP\API_Routes\Metrics_Get::class,
		'logs-get'           => \WPMP\API_Routes\Logs_Get::class,
		'fields-save'        => \WPMP\API_Routes\Fields_Save::class,
		'send-test-email'    => \WPMP\API_Routes\Test_Email_Send::class,
		'email-content-get'  => \WPMP\API_Routes\Email_Content_Get::class,
	];

	/**
	 * Store our webhook routes slugs and classes.
	 *
	 * @var array
	 */
	protected $webhook_routes = [
		'transmission-status-update' => \WPMP\API_Routes\Transmission_Status_Update::class,
		'sending-limit-update'       => \WPMP\API_Routes\Sending_Limit_Update::class,
	];

	/**
	 * List of webhook actions requiring basic auth.
	 *
	 * @var array
	 */
	protected $webhook_required_auth = [ 'transmission-status-update' ];

	/**
	 * Initialize the public API routes.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Get the slugs/classes for admin routes.
	 *
	 * @return array
	 */
	protected function get_admin_routes() {
		return (array) $this->admin_routes;
	}

	/**
	 * Get the slugs/classes for webhook routes.
	 *
	 * @return array
	 */
	protected function get_webhook_routes() {
		return (array) $this->webhook_routes;
	}

	/**
	 * Instantiate the API class in charge of validating and processing the action.
	 *
	 * @param string $type
	 * @param \WP_REST_Request $request
	 * @return void
	 */
	protected function get_route_corresponding_class( $type, $request ) {
		switch ( $type ) {
			default:
			case 'admin':
				$class = $this->get_admin_routes()[ $request->get_param( 'action' ) ];
				break;

			case 'webhook':
				$class = $this->get_webhook_routes()[ $request->get_param( 'action' ) ];
				break;
		}

		return new $class( $request );
	}

	/**
	 * Register the API routes (admin + webhooks).
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			'wpmailpro/v1',
			'admin/(?P<action>[a-zA-Z0-9-]+)',
			[
				'methods'             => 'POST',
				'permission_callback' => [ $this, 'protect_admin_routes' ],
				'validate_callback'   => [ $this, 'validate_admin_routes_args' ],
				'callback'            => [ $this, 'process_admin_routes' ],
			]
		);

		register_rest_route(
			'wpmailpro/v1',
			'webhook/(?P<action>[a-zA-Z0-9-]+)',
			[
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'validate_callback'   => [ $this, 'validate_webhook_routes_args' ],
				'callback'            => [ $this, 'process_webhook_routes' ],
			]
		);
	}

	/**
	 * Protect the admin route.
	 *
	 * @return boolean
	 */
	public function protect_admin_routes() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Validate admin routes arguments.
	 *
	 * @param \WP_REST_Request $request
	 * @return boolean
	 */
	public function validate_admin_routes_args( \WP_REST_Request $request ) {
		return ! in_array( false, [
			'action' => in_array( $request->get_param( 'action' ), array_keys( $this->get_admin_routes() ), true ),
		], true );
	}

	/**
	 * Validate webhook routes arguments.
	 *
	 * @param \WP_REST_Request $request
	 * @return boolean
	 */
	public function validate_webhook_routes_args( \WP_REST_Request $request ) {
		$valid_action = ! in_array( false, [
			'action' => in_array( $request->get_param( 'action' ), array_keys( $this->get_webhook_routes() ), true ),
		], true );

		$valid_credentials = true;

		if ( in_array( $request->get_param( 'action' ), $this->webhook_required_auth, true ) ) {
			if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) || ! $_SERVER['PHP_AUTH_PW'] ) {
				return false;
			}

			$credentials       = Webhooks\get_credentials();
			$valid_credentials = ( $_SERVER['PHP_AUTH_USER'] === $credentials['username'] && $_SERVER['PHP_AUTH_PW'] === $credentials['password'] );
		}

		return $valid_action && $valid_credentials;
	}

	/**
	 * Process the /admin/<action> requests.
	 *
	 * @param \WP_REST_Request $request
	 * @return void
	 */
	public function process_admin_routes( \WP_REST_Request $request ) {
		$api_class = $this->get_route_corresponding_class( 'admin', $request );
		$valid     = $api_class->is_valid();

		if ( is_wp_error( $valid ) ) {
			$response = $valid;
		} else {
			$response = $api_class->process();
		}

		return new \WP_REST_Response( $this->shape_response( $response ) );
	}

	/**
	 * Process the /webhook/<action> requests.
	 *
	 * @param \WP_REST_Request $request
	 * @return void
	 */
	public function process_webhook_routes( \WP_REST_Request $request ) {
		$api_class = $this->get_route_corresponding_class( 'webhook', $request );
		$valid     = $api_class->is_valid();

		if ( is_wp_error( $valid ) ) {
			$response = $valid;
		} else {
			$response = $api_class->process();
		}

		return new \WP_REST_Response( $this->shape_response( $response ) );
	}

	/**
	 * Shape a response: flatten a WP_Error, or send the success object as is.
	 *
	 * @param mixed $response
	 * @return array
	 */
	protected function shape_response( $response ) {
		if ( is_wp_error( $response ) ) {
			$response = [
				'success' => false,
				'error'   => true,
				'message' => $response->get_error_message(),
			];
		}

		if ( isset( $response['message'] ) ) {
			$response['message'] = html_entity_decode( $response['message'] );
		}

		return $response;
	}
}
