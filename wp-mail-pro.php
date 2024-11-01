<?php
/**
 * Plugin Name: WP Mail Pro
 * Description: All in one plugin for sending emails with WordPress. Improve deliverability and troubleshoot faster.
 * Author: WP Mail Pro
 * Author URI: https://www.wpmailpro.com/
 * Text Domain: wpmp
 * Domain Path: /languages/
 * Version: 2.0.0
 * Requires at least: 5.5
 * Requires PHP:      5.6
 */

namespace WPMP;

defined( 'ABSPATH' ) || exit;

/**
 * Define plugin constants
 */
define( 'WPMP_VERSION', '2.0.0' );
define( 'WPMP_URL', plugin_dir_url( __FILE__ ) );
define( 'WPMP_ROOT_FILE', __FILE__ );
define( 'WPMP_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPMP_PLUGIN_DIRNAME', basename( rtrim( dirname( __FILE__ ), '/' ) ) );
define( 'WPMP_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Check for requirements and (maybe) load the plugin vital files.
 *
 * @return void
 */
function init() {
	if ( file_exists( WPMP_DIR . '/vendor/autoload.php' ) ) {
		require_once WPMP_DIR . '/vendor/autoload.php';
	}

	if ( ! function_exists( 'as_schedule_single_action' ) ) {
		require_once WPMP_DIR . '/includes/libraries/action-scheduler/action-scheduler.php';
	}

	/**
	 * Register required files.
	 */
	require_once WPMP_DIR . '/includes/classes/_abstract/api-route.php';
	require_once WPMP_DIR . '/includes/classes/config.php';
	require_once WPMP_DIR . '/includes/classes/public-api.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/license-activate.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/license-deactivate.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/metrics-get.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/logs-get.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/fields-save.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/test-email-send.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/domain-create.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/domain-verify.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/transmission-status-update.php';
	require_once WPMP_DIR . '/includes/classes/api-routes/sending-limit-update.php';
	require_once WPMP_DIR . '/includes/classes/api-licensing.php';
	require_once WPMP_DIR . '/includes/classes/api-sparkpost.php';
	require_once WPMP_DIR . '/includes/classes/sparkpost-mailer.php';
	require_once WPMP_DIR . '/includes/schema/emails-table.php';
	require_once WPMP_DIR . '/includes/admin.php';
	require_once WPMP_DIR . '/includes/admin-preview-email.php';
	require_once WPMP_DIR . '/includes/assets.php';
	require_once WPMP_DIR . '/includes/crons.php';
	require_once WPMP_DIR . '/includes/functions.php';
	require_once WPMP_DIR . '/includes/helpers.php';
	require_once WPMP_DIR . '/includes/options.php';
	require_once WPMP_DIR . '/includes/hooks.php';
	require_once WPMP_DIR . '/includes/notices.php';
	require_once WPMP_DIR . '/includes/scheduler.php';
	require_once WPMP_DIR . '/includes/webhooks.php';

	if ( ! meets_requirements() && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		add_action( 'admin_notices', 'WPMP\Notices\notice_for_missing_requirements' );
		add_action( 'network_admin_notices', 'WPMP\Notices\notice_for_missing_requirements' );
		return;
	}

	/**
	 * Register vital hooks.
	 */
	$api = new Public_API();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\init' );

/**
 * Does this WP install meet minimum requirements?
 *
 * @return boolean
 */
function meets_requirements() {
	global $wp_version;

	return (
		version_compare( PHP_VERSION, '5.6', '>=' ) &&
		version_compare( $wp_version, '5.5', '>=' )
	);
}

/**
 * Trigger a custom action when activating the plugin.
 *
 * @param string $plugin
 * @param boolean $network
 * @return void
 */
function wpmp_activation( $plugin, $network ) {
	if ( $plugin !== WPMP_BASENAME ) {
		return;
	}

	init();
	do_action( 'wpmp/plugin_activation', (bool) $network );
}
add_action( 'activate_plugin', __NAMESPACE__ . '\\wpmp_activation', 10, 2 );

/**
 * Trigger a custom action when de-activating the plugin.
 *
 * @return void
 */
function wpmp_deactivation( $plugin, $network ) {
	if ( $plugin !== WPMP_BASENAME ) {
		return;
	}

	init();
	do_action( 'wpmp/plugin_deactivation', (bool) $network );
}
add_action( 'deactivate_plugin', __NAMESPACE__ . '\\wpmp_deactivation', 10, 2 );
