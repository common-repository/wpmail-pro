<?php

namespace WPMP\Crons;

use \WPMP\API_Licensing;
use \WPMP\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Register new CRON jobs.
 *
 * @return void
 */
function register_cron_jobs() {
	if ( ! wp_next_scheduled( 'wpmp/cron/check_license' ) ) {
		wp_schedule_event( strtotime( 'tomorrow 00:01' ), 'daily', 'wpmp/cron/check_license' );
	}
}
add_action( 'init', __NAMESPACE__ . '\\register_cron_jobs' );

/**
 * De-register our CRON hooks when de-activating this plugin.
 *
 * @return void
 */
function deregister_cron_jobs() {
	wp_clear_scheduled_hook( 'wpmp/cron/check_license' );
}
add_action( 'wpmp/plugin_deactivation', __NAMESPACE__ . '\\deregister_cron_jobs' );

/**
 * Check plugin license.
 *
 * @return void
 */
function check_license() {
	$license = API_Licensing::get_current_license();

	if ( empty( $license ) || ! API_Licensing::is_activated() ) {
		return;
	}

	$api = new API_Licensing();

	if ( $api->is_valid( $license ) ) {
		return;
	}

	Options\update_plugin_option( 'license_expired', current_time( 'timestamp', true ) );
	Options\delete_plugin_option( 'license_activated' );
	Options\delete_plugin_option( 'license_deactivated' );
	Options\delete_plugin_option( 'sparkpost_api_key' );
}
add_action( 'wpmp/cron/check_license', __NAMESPACE__ . '\\check_license' );
