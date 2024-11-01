<?php

namespace WPMP\Schema\Emails_Table;

use WPMP\Config;
use WPMP\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Create the E-mails Logs SQL table on plugin activation, if it does not exist yet.
 *
 * @return void
 */
function create() {
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name      = Config::emails_table();

	$table = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
		email_sender varchar(200) NOT NULL,
		email_recipient varchar(200) NOT NULL,
		email_subject varchar(255) NOT NULL,
		api_provider varchar(255),
		api_id varchar(255),
		api_details longtext,
		last_event_status varchar(255),
		last_event_details longtext,
		last_event_date datetime,
		status varchar(200) NOT NULL default '',
		created_at datetime NOT NULL DEFAULT NOW(),
		PRIMARY KEY  (id),
		KEY status (status)
	) $charset_collate;";

	\dbDelta( $table );

	Options\update_plugin_option( 'db_version', WPMP_VERSION );
}
add_action( 'wpmp/plugin_activation', __NAMESPACE__ . '\\create' );

/**
 * 2.0.0 Upgrade : add extra columns to the e-mails table (email_content + engagement_details).
 *
 * @return void
 */
function upgrade_table_to_v200() {
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	global $wpdb;
	$table_name = Config::emails_table();

	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}';" ) ) {
		// Add the "email_content" column.
		if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `{$table_name}` LIKE 'email_content';" ) ) {
			$wpdb->query(
				"ALTER TABLE $table_name
				ADD COLUMN email_content longtext DEFAULT NULL
				AFTER email_subject;"
			);
		}

		// Add the "engagement_details" column.
		if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `{$table_name}` LIKE 'engagement_details';" ) ) {
			$wpdb->query(
				"ALTER TABLE $table_name
				ADD COLUMN engagement_details longtext DEFAULT NULL
				AFTER api_details;"
			);
		}
	}
}
add_action( 'admin_init', __NAMESPACE__ . '\\upgrade_table_to_v200' );
