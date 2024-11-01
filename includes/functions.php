<?php

// No namespace for this file, to directly access helper functions.

use WPMP\Helpers;
use WPMP\Options;
use WPMP\Config;
use WPMP\API_Licensing;

defined( 'ABSPATH' ) || exit;

/**
 * Get an instance of the SparkPost API manager.
 *
 * @return \WPMP\API_Sparkpost
 */
function wpmp_get_sparkpost_api() {
	return new \WPMP\API_Sparkpost( wpmp_get_sparkpost_api_key() );
}

/**
 * Get SparkPost API key.
 *
 * @return string
 */
function wpmp_get_sparkpost_api_key() {
	if ( defined( 'WPMAILPRO_SPARKPOST_API_KEY' ) ) {
		return WPMAILPRO_SPARKPOST_API_KEY;
	}

	$crypted_api_key = Options\get_plugin_option( 'sparkpost_api_key' );

	if ( ! is_null( $crypted_api_key ) ) {
		return Helpers\crypt( $crypted_api_key, true );
	}

	return null;
}

/**
 * Can we send mails using SparkPost API?
 *
 * @return boolean
 */
function wpmp_can_send_mail() {
	return API_Licensing::is_activated() && ! empty( wpmp_get_sparkpost_api_key() );
}

/**
 * Create a bunch of fake emails in the logs table.
 *
 * @param integer $limit
 * @return void
 */
function wpmp_create_fake_emails( $limit = 100 ) {
	if ( ! WP_DEBUG || ! class_exists( 'Faker' ) ) {
		return;
	}

	global $wpdb;
	$faker = \Faker\Factory::create();

	for ( $i = 0; $i < $limit; $i++ ) {
		$wpdb->insert(
			Config::emails_table(),
			[
				'email_sender'    => $faker->email(),
				'email_recipient' => $faker->email(),
				'email_subject'   => $faker->sentence( rand( 3, 6 ) ),
				'api_provider'    => 'sparkpost',
				'api_id'          => sprintf( '%1$d%2$d', $faker->randomNumber( rand( 4, 8 ), true ), $faker->randomNumber( rand( 4, 8 ), true ) ),
				'status'          => $faker->randomElement( [ 'error', 'rejected', 'accepted', 'accepted', 'accepted', 'accepted', 'accepted', 'accepted' ] ),
				'created_at'      => $faker->dateTimeBetween( '-2 months', 'now' )->format( 'Y-m-d H:i:s' ),
			]
		);
	}
}

/**
 * Send a few e-mails to test the API and fill the logs table.
 *
 * @param integer $limit
 * @return void
 */
function wpmp_send_fake_emails( $limit = 10 ) {
	if ( ! WP_DEBUG || ! class_exists( 'Faker' ) ) {
		return;
	}

	$email_admin = get_option( 'admin_email' );
	$faker       = \Faker\Factory::create();
	$emails      = [ $faker->email(), $faker->email(), $faker->email(), $faker->email(), $email_admin, $email_admin, $email_admin, $email_admin ];

	for ( $i = 0; $i < $limit; $i++ ) {
		wp_mail(
			$faker->randomElement( $emails ),
			$faker->sentence( rand( 3, 6 ) ),
			$faker->paragraphs( rand( 3, 6 ), true )
		);

		sleep( 1 );
	}
}

/**
 * Default sender mail (used if no custom domain is configured).
 *
 * @return string
 */
function wpmp_get_default_sender_mail( $prefix_only = false ) {
	if ( $prefix_only ) {
		return Config::DEFAULT_SENDING_DOMAIN;
	}

	$site_name = sanitize_title( get_option( 'blogname' ) );

	if ( empty( $site_name ) ) {
		$site_name = wp_parse_url( home_url(), PHP_URL_HOST );
		$site_name = str_replace( 'www.', '', $site_name );
		$site_name = str_replace( '.', '-', $site_name );
	}

	return sprintf(
		'%1$s@%2$s',
		$site_name,
		Config::DEFAULT_SENDING_DOMAIN
	);
}

/**
 * Get the sender mail address (or prefix before @).
 *
 * @param boolean $prefix_only
 * @return string
 */
function wpmp_get_sender_mail( $prefix_only = false ) {
	if ( ! wpmp_current_domain_is_valid() ) {
		$email = wpmp_get_default_sender_mail();
	} else {
		$email = Options\get_plugin_option( 'sender_mail', wpmp_get_default_sender_mail() );
	}

	if ( ! $prefix_only ) {
		return $email;
	}

	$exploded = explode( '@', $email );

	return $exploded[0];
}

/**
 * Is current sending domain valid (verified)?
 *
 * @return boolean
 */
function wpmp_current_domain_is_valid() {
	$domain = Options\get_plugin_option( 'domain' );

	if ( empty( $domain ) ) {
		return false;
	}

	return (bool) Options\get_plugin_option( "domain_dkim_record_{$domain}_is_valid", 0 );
}

/**
 * Should we force the sender e-mail?
 *
 * @return boolean
 */
function wpmp_sender_email_is_forced() {
	return (bool) Options\get_plugin_option( 'force_sender_email', 0 );
}

/**
 * Get sending limit data.
 *
 * @return object
 */
function wpmp_get_sending_limit_data() {
	$license = API_Licensing::get_current_license();

	if ( empty( $license ) || ! API_Licensing::is_activated() ) {
		return null;
	}

	$api = new API_Licensing();
	return $api->get_sending_limit_data( $license );
}

/**
 * Is this site over the sending limit?
 *
 * @return boolean
 */
function wpmp_free_plan_site_is_flagged() {
	return (
		true
		&& (int) Options\get_plugin_option( 'sending_limit_is_flagged', 0 ) > 0
		&& ( Options\get_plugin_option( 'sending_limit_plan' ) === 'free' )
	);
}
