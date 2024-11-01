<?php

namespace WPMP\Hooks;

use WPMP\Config;
use \WPMP\Options;
use \WPMP\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Translations.
 *
 * @return void
 */
function load_translations() {
	load_plugin_textdomain( 'wpmp', false, WPMP_PLUGIN_DIRNAME . '/languages/' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_translations' );

/**
 * Generate a unique License Instance ID on plugin activation.
 *
 * @internal On network plugin activation, is_active_on_multisite() does NOT return true! Hence the hack...
 * @param boolean $network
 * @return void
 */
function generate_license_instance_id( $network ) {
	$instance_id = $network ? get_site_option( 'wpmp_license_instance_id', null ) : get_option( 'wpmp_license_instance_id', null );

	if ( ! is_null( $instance_id ) ) {
		return;
	}

	$instance_id = wp_generate_password( 8, false, false );

	if ( $network ) {
		update_site_option( 'wpmp_license_instance_id', $instance_id );
	} else {
		update_option( 'wpmp_license_instance_id', $instance_id, false );
	}
}
add_action( 'wpmp/plugin_activation', __NAMESPACE__ . '\\generate_license_instance_id', 10, 1 );

/**
 * Maybe inject custom SparkPost HTTP Mailer.
 *
 * @param object $args
 * @return object
 */
function maybe_inject_sparkpost_mailer( $args ) {
	if ( ! wpmp_can_send_mail() ) {
		return $args;
	}

	global $phpmailer;

	if ( ! $phpmailer instanceof \WPMP\Sparkpost_Mailer ) {
		$phpmailer = new \WPMP\Sparkpost_Mailer();
	}

	$phpmailer->wp_mail_args = $args;

	return $args;
}
add_filter( 'wp_mail', __NAMESPACE__ . '\\maybe_inject_sparkpost_mailer' );

/**
 * Change email from.
 *
 * @param string $from
 * @return string
 */
function set_from_email( $from ) {
	return wpmp_get_sender_mail();
}
add_filter( 'wp_mail_from', __NAMESPACE__ . '\\set_from_email', wpmp_sender_email_is_forced() ? 9999999 : 10 );

/**
 * Change email name.
 *
 * @param string $name
 * @return string
 */
function set_from_name( $name ) {
	return Options\get_plugin_option( 'sender_name', $name );
}
add_filter( 'wp_mail_from_name', __NAMESPACE__ . '\\set_from_name', wpmp_sender_email_is_forced() ? 9999999 : 10 );

/**
 * Set HTML content type to sent e-mails.
 *
 * @param string $type
 * @return string
 */
function set_html_content_type( $type ) {
	$type = 'text/html';

	return $type;
}
add_filter( 'wp_mail_content_type', __NAMESPACE__ . '\\set_html_content_type' );

/**
 * Delete all sending domains by accessing an admin page with '?delete_sending_domains' param.
 *
 * @return void
 */
function delete_all_sending_domains() {
	if ( ! isset( $_GET['delete_sending_domains'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$api = wpmp_get_sparkpost_api();
	$api->list_domains();

	if ( ! is_wp_error( $api->get_response() ) ) {
		foreach ( $api->get_response( 'results' ) as $domain ) {
			if ( ! isset( $domain->domain ) ) {
				continue;
			}

			$domain_api = wpmp_get_sparkpost_api();
			$domain_api->delete_domain( $domain->domain );
		}
	}

	wp_safe_redirect( remove_query_arg( 'delete_sending_domains', Helpers\get_current_url() ) );
	exit();
}
add_action( 'admin_init', __NAMESPACE__ . '\\delete_all_sending_domains' );

//=============================================
//                                             
//  ####    #####  #####   ##   ##   ####    
//  ##  ##  ##     ##  ##  ##   ##  ##       
//  ##  ##  #####  #####   ##   ##  ##  ###  
//  ##  ##  ##     ##  ##  ##   ##  ##   ##  
//  ####    #####  #####    #####    ####    
//                                             
//=============================================

/**
 * Debug API args.
 */
function debug_api_args( $args ) {
	if ( defined( 'WPMP_DEBUG_API' ) && WPMP_DEBUG_API ) {
		Helpers\log( $args );
	}

	return $args;
}
add_filter( 'wpmp/sparkpost_api/request_args', __NAMESPACE__ . '\\debug_api_args', 10 );

/**
 * Debug API URL.
 */
function debug_api_url( $url ) {
	if ( defined( 'WPMP_DEBUG_API' ) && WPMP_DEBUG_API ) {
		Helpers\log( $url );
	}

	return $url;
}
add_filter( 'wpmp/sparkpost_api/request_url', __NAMESPACE__ . '\\debug_api_url', 10 );

/**
 * Debug API calls.
 */
function debug_api_calls( $pretty_response, $response ) {
	if ( defined( 'WPMP_DEBUG_API' ) && WPMP_DEBUG_API ) {
		Helpers\log( $pretty_response );
	}
}
add_action( 'wpmp/sparkpost_api/response', __NAMESPACE__ . '\\debug_api_calls', 10, 2 );

/**
 * When validating a domain, update the sender email to replace previous domain.
 */
function update_sender_email_with_new_domain( $new_domain ) {
	Options\update_plugin_option( 'sender_mail', sprintf( '%1$s@%2$s', wpmp_get_sender_mail( true ), $new_domain ) );
}
add_action( 'wpmp/domain_validated', __NAMESPACE__ . '\\update_sender_email_with_new_domain', 10, 1 );

/**
 * When validating a domain, update the sender email to replace default sending domain with new custom domain.
 */
function update_sender_email_with_default_domain() {
	Options\update_plugin_option( 'sender_mail', wpmp_get_default_sender_mail() );
}
//add_action( 'wpmp/domain_unvalidated', __NAMESPACE__ . '\\update_sender_email_with_default_domain', 10 );
