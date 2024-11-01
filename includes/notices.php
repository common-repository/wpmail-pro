<?php

namespace WPMP\Notices;

use WPMP\Config;
use \WPMP\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Display a notice if requirements are not met.
 *
 * @return void
 */
function notice_for_missing_requirements() {
	printf(
		'<div class="notice notice-error"><p>%1$s</p></div>',
		__( 'The WP Mail Pro plugin minimum requirements are not met (PHP 5.6 and WordPress 5.5).', 'wpmp' )
	);
}

/**
 * Display a notice if License Key is not set.
 *
 * @return void
 */
function notice_if_license_key_is_missing() {
	$license_activated = (int) Options\get_plugin_option( 'license_activated', 0 ) > 0;

	if ( $license_activated ) {
		return;
	}

	printf(
		'<div class="notice notice-error license-activation"><p>%1$s</p></div>',
		sprintf(
			__( 'Please visit <a href="%1$s">the Settings tab</a> to activate your WP Mail Pro plugin license. If you need a free license, <a href="%2$s" target="_blank">click here</a>.', 'wpmp' ),
			admin_url( 'admin.php?tab=settings&page=' . Config::ADMIN_PAGE_SLUG ),
			'https://wpmailpro.com/pricing'
		)
	);
}
add_action( 'admin_notices', __NAMESPACE__ . '\\notice_if_license_key_is_missing' );

/**
 * Display a notice if site is flagged (sending limit).
 *
 * @return void
 */
function notice_if_sending_limit_site_is_flagged() {
	$flagged = (int) Options\get_plugin_option( 'sending_limit_is_flagged', 0 ) > 0;

	if ( ! $flagged ) {
		return;
	}

	$free_plan = ( Options\get_plugin_option( 'sending_limit_plan' ) === 'free' );

	if ( $free_plan ) {
		$message = sprintf(
			__( 'You have reached your sending limit and your site will no longer send emails. <a href="%1$s" target="_blank">Upgrade your license</a> to increase your sending limit.', 'wpmp' ),
			'https://wpmailpro.com/pricing?upgrade=true'
		);
	} else {
		$message = sprintf(
			__( 'You have reached your sending limit and your site will now incur overage cost. <a href="%1$s" target="_blank">Upgrade your license</a> to increase your sending limit.', 'wpmp' ),
			'https://wpmailpro.com/pricing?upgrade=true'
		);
	}

	printf(
		'<div class="notice notice-error sending-limit-flag"><p>%1$s</p></div>',
		$message
	);
}
add_action( 'admin_notices', __NAMESPACE__ . '\\notice_if_sending_limit_site_is_flagged' );
