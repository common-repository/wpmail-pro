<?php

namespace WPMP\Webhooks;

use \WPMP\Options;
use \WPMP\Scheduler;

defined( 'ABSPATH' ) || exit;

//===================================
//                                   
//   ####  #####    #####  ####    
//  ##     ##  ##   ##     ##  ##  
//  ##     #####    #####  ##  ##  
//  ##     ##  ##   ##     ##  ##  
//   ####  ##   ##  #####  ####    
//                                   
//===================================

/**
 * Get webhook (generated) credentials.
 *
 * @return array
 */
function get_credentials() {
	return Options\get_plugin_option( 'webhook_credentials', null );
}

/**
 * Generate basic auth credentials and save them in an option.
 *
 * @param boolean $network
 * @return void
 */
function generate_basic_auth_credentials( $network = false ) {
	$webhook_credentials = $network ? get_site_option( 'wpmp_webhook_credentials', null ) : get_option( 'wpmp_webhook_credentials', null );

	$webhook_credentials = [
		'username' => sprintf( '%1$s_%2$s', sanitize_title( wp_parse_url( home_url(), PHP_URL_HOST ) ), wp_generate_password( 5, false, false ) ),
		'password' => wp_generate_password( 16, false, false ),
	];

	if ( $network ) {
		update_site_option( 'wpmp_webhook_credentials', $webhook_credentials );
	} else {
		update_option( 'wpmp_webhook_credentials', $webhook_credentials, false );
	}

	return $webhook_credentials;
}
add_action( 'wpmp/plugin_activation', __NAMESPACE__ . '\\generate_basic_auth_credentials', 10, 1 );

//===================================================
//                                                   
//   ####  #####    #####    ###    ######  #####  
//  ##     ##  ##   ##      ## ##     ##    ##     
//  ##     #####    #####  ##   ##    ##    #####  
//  ##     ##  ##   ##     #######    ##    ##     
//   ####  ##   ##  #####  ##   ##    ##    #####  
//                                                   
//===================================================

/**
 * Create webhook.
 *
 * @return void
 */
function create_webhook() {
	if ( defined( 'WPMP_WEBHOOK_CREATION' ) && WPMP_WEBHOOK_CREATION ) {
		return;
	}

	define( 'WPMP_WEBHOOK_CREATION', true );

	// Delete old webhooks.
	$deletion_api = wpmp_get_sparkpost_api();
	$deletion_api->get_webhooks();

	if ( ! is_wp_error( $deletion_api->get_response() ) ) {
		foreach ( $deletion_api->get_response( 'results' ) as $webhook ) {
			if ( ! isset( $webhook->id ) ) {
				continue;
			}

			$webhook_deletion_api = wpmp_get_sparkpost_api();
			$webhook_deletion_api->delete_webhook( $webhook->id );
		}
	}

	// Create new webhook.
	$creation_api = wpmp_get_sparkpost_api();
	$creation_api->create_delivery_webhook();

	if ( is_wp_error( $creation_api->get_response() ) ) {
		Scheduler\schedule( 'wpmp/retry_sparkpost_webhook_creation' );
		return;
	}

	$response = $creation_api->get_response( 'results' );

	if ( ! isset( $response, $response->id ) ) {
		Scheduler\schedule( 'wpmp/retry_sparkpost_webhook_creation' );
		return;
	}

	Options\update_plugin_option( 'webhook_id', sanitize_text_field( $response->id ) );
}

/**
 * Delete webhooks when plugin license OR plugin is deactivated.
 *
 * @return void
 */
function delete_webhooks_on_license_or_plugin_deactivation() {
	// Delete old webhooks.
	$deletion_api = wpmp_get_sparkpost_api();
	$deletion_api->get_webhooks();

	if ( ! is_wp_error( $deletion_api->get_response() ) ) {
		foreach ( $deletion_api->get_response( 'results' ) as $webhook ) {
			if ( ! isset( $webhook->id ) ) {
				continue;
			}

			$webhook_deletion_api = wpmp_get_sparkpost_api();
			$webhook_deletion_api->delete_webhook( $webhook->id );
		}
	}

	Options\delete_plugin_option( 'webhook_id' );
	Options\delete_plugin_option( 'webhook_version' );
	Options\delete_plugin_option( 'webhook_credentials' );

	// Delete site sending limit flag metadata.
	Options\delete_plugin_option( 'sending_limit_is_flagged' );
	Options\delete_plugin_option( 'sending_limit_plan' );
}
add_action( 'wpmp/license_deactivated', __NAMESPACE__ . '\\delete_webhooks_on_license_or_plugin_deactivation', 10 );
add_action( 'wpmp/plugin_deactivation', __NAMESPACE__ . '\\delete_webhooks_on_license_or_plugin_deactivation', 10 );

//===============================================================
//                                                               
//  ##   ##  #####    ####    #####      ###    ####    #####  
//  ##   ##  ##  ##  ##       ##  ##    ## ##   ##  ##  ##     
//  ##   ##  #####   ##  ###  #####    ##   ##  ##  ##  #####  
//  ##   ##  ##      ##   ##  ##  ##   #######  ##  ##  ##     
//   #####   ##       ####    ##   ##  ##   ##  ####    #####  
//                                                               
//===============================================================

/**
 * Create webhook when license is activated.
 *
 * @return void
 */
add_action( 'wpmp/license_activated', __NAMESPACE__ . '\\create_webhook', 10 );

/**
 * Re-create the webhook when updating the plugin to 2.0.0, as we're listening to more events.
 *
 * @return void
 */
function create_new_webhook_for_v200() {
	if (
		false
		|| defined( 'WP_UNINSTALL_PLUGIN' )
		|| (int) Options\get_plugin_option( 'webhook_version' ) === 200
	) {
		return;
	}

	create_webhook();

	Options\update_plugin_option( 'webhook_version', 200 );
}
add_action( 'admin_init', __NAMESPACE__ . '\\create_new_webhook_for_v200', 10 );

