<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb, $wp_version;

require_once dirname( __FILE__ ) . '/wp-mail-pro.php';

\WPMP\init();

/**
 * Deactivate key.
 */
$license = \WPMP\Options\get_plugin_option( 'license' );

if ( ! is_null( $license ) ) {
	$api          = new \WPMP\API_Licensing();
	$deactivation = $api->deactivate( $license );

	do_action( 'wpmp/license_deactivated', $license );
}

/**
 * Delete logs table.
 */
$logs_table = \WPMP\Config::emails_table();
$wpdb->query( "DROP TABLE IF EXISTS $logs_table" );

/**
 * Delete plugin options.
 */
if ( \WPMP\Helpers\is_active_on_multisite() ) {
	$table  = $wpdb->sitemeta;
	$column = 'meta_key';
} else {
	$table  = $wpdb->options;
	$column = 'option_name';
}

$wpdb->query( "DELETE FROM $table WHERE $column LIKE 'wpmp_%' OR $column LIKE '_transient_wpmp%';" );
