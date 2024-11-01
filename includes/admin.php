<?php

namespace WPMP\Admin;

use \WPMP\Config;

defined( 'ABSPATH' ) || exit;

//=======================================================================================
//                                                                                       
//    ###    ####    ###    ###  ##  ##     ##        #####     ###     ####    #####  
//   ## ##   ##  ##  ## #  # ##  ##  ####   ##        ##  ##   ## ##   ##       ##     
//  ##   ##  ##  ##  ##  ##  ##  ##  ##  ## ##        #####   ##   ##  ##  ###  #####  
//  #######  ##  ##  ##      ##  ##  ##    ###        ##      #######  ##   ##  ##     
//  ##   ##  ####    ##      ##  ##  ##     ##        ##      ##   ##   ####    #####  
//                                                                                       
//=======================================================================================

/**
 * Register a new WP Mail Pro admin menu page.
 *
 * @return void
 */
function register_admin_menu_item() {
	if ( \WPMP\Helpers\is_active_on_multisite() ) {
		return;
	}

	add_menu_page(
		null,
		__( 'WP Mail Pro', 'wpmp' ),
		'manage_options',
		Config::ADMIN_PAGE_SLUG,
		__NAMESPACE__ . '\\output_admin_page',
		'dashicons-email-alt2',
		76
	);

	add_submenu_page(
		Config::ADMIN_PAGE_SLUG,
		null,
		__( 'Metrics', 'wpmp' ),
		'manage_options',
		Config::ADMIN_PAGE_SLUG . '&tab=metrics',
		__NAMESPACE__ . '\\output_admin_page'
	);

	add_submenu_page(
		Config::ADMIN_PAGE_SLUG,
		null,
		__( 'Logs', 'wpmp' ),
		'manage_options',
		Config::ADMIN_PAGE_SLUG . '&tab=logs',
		__NAMESPACE__ . '\\output_admin_page'
	);

	add_submenu_page(
		Config::ADMIN_PAGE_SLUG,
		null,
		__( 'DNS Configuration', 'wpmp' ),
		'manage_options',
		Config::ADMIN_PAGE_SLUG . '&tab=dns',
		__NAMESPACE__ . '\\output_admin_page'
	);

	add_submenu_page(
		Config::ADMIN_PAGE_SLUG,
		null,
		__( 'Settings', 'wpmp' ),
		'manage_options',
		Config::ADMIN_PAGE_SLUG . '&tab=settings',
		__NAMESPACE__ . '\\output_admin_page'
	);

	// Fake sub-menu page for the email preview page.
	add_submenu_page(
		null,
		null,
		null,
		'manage_options',
		Config::ADMIN_EMAIL_PREVIEW_PAGE_SLUG,
		__NAMESPACE__ . '\\output_admin_preview_email_page'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\\register_admin_menu_item' );

/**
 * Register a new WP Mail Pro admin menu page on the parent network site for multisites install.
 *
 * @return void
 */
function register_admin_menu_item_for_multisite() {
	if ( ! \WPMP\Helpers\is_active_on_multisite() ) {
		return;
	}

	add_menu_page(
		__( 'WP Mail Pro', 'wpmp' ),
		__( 'WP Mail Pro', 'wpmp' ),
		'manage_options',
		Config::ADMIN_PAGE_SLUG,
		__NAMESPACE__ . '\\output_admin_page',
		'dashicons-email-alt2',
		76
	);
}
add_action( 'network_admin_menu', __NAMESPACE__ . '\\register_admin_menu_item_for_multisite' );

/**
 * Output the admin page content.
 *
 * @return void
 */
function output_admin_page() {
	echo '<div id="wpmp-admin-page-container"><span class="spinner is-active"></span></div>';
}

/**
 * Output the content of the Preview E-mail admin page.
 *
 * @return void
 */
function output_admin_preview_email_page() {
	$id     = isset( $_GET['email_id'] ) ? (int) $_GET['email_id'] : null;
	$token  = isset( $_GET['token'] ) ? sanitize_text_field( $_GET['token'] ) : null;
	$iframe = isset( $_GET['iframe'] ) && (int) $_GET['iframe'] === 1;

	if ( ! $iframe ) {
		do_action( 'wpmp/admin/preview_email_content', $id, $token );
	}
}

//================================================================================
//                                                                                
//    ###    ####    ###    ###  ##  ##     ##        #####     ###    #####    
//   ## ##   ##  ##  ## #  # ##  ##  ####   ##        ##  ##   ## ##   ##  ##   
//  ##   ##  ##  ##  ##  ##  ##  ##  ##  ## ##        #####   ##   ##  #####    
//  #######  ##  ##  ##      ##  ##  ##    ###        ##  ##  #######  ##  ##   
//  ##   ##  ####    ##      ##  ##  ##     ##        #####   ##   ##  ##   ##  
//                                                                                
//================================================================================

/**
 * Add a top admin bar item for our plugin.
 *
 * @param \WP_Admin_Bar $wp_menu 
 * @return void
 */
function add_toolbar_items( $wp_menu ) {
	if ( ! current_user_can( 'manage_options' ) || \WPMP\Helpers\is_active_on_multisite() ) {
		return;
	}

	$url = admin_url( 'admin.php?page=' . Config::ADMIN_PAGE_SLUG );

	$wp_menu->add_menu(
		array(
			'id'    => 'wp-mail-pro',
			'title' => __( 'WP Mail Pro', 'wpmp' ),
			'href'  => $url,
		)
	);

	$wp_menu->add_node(
		array(
			'parent' => 'wp-mail-pro',
			'id'     => 'wpmp-metrics',
			'title'  => __( 'Metrics', 'wpmp' ),
			'href'   => add_query_arg( [ 'tab' => 'metrics' ], $url ),
		)
	);

	$wp_menu->add_node(
		array(
			'parent' => 'wp-mail-pro',
			'id'     => 'wpmp-logs',
			'title'  => __( 'Logs', 'wpmp' ),
			'href'   => add_query_arg( [ 'tab' => 'logs' ], $url ),
		)
	);

	$wp_menu->add_node(
		array(
			'parent' => 'wp-mail-pro',
			'id'     => 'wpmp-dns',
			'title'  => __( 'DNS Configuration', 'wpmp' ),
			'href'   => add_query_arg( [ 'tab' => 'dns' ], $url ),
		)
	);

	$wp_menu->add_node(
		array(
			'parent' => 'wp-mail-pro',
			'id'     => 'wpmp-settings',
			'title'  => __( 'Settings', 'wpmp' ),
			'href'   => add_query_arg( [ 'tab' => 'settings' ], $url ),
		)
	);
}
add_action( 'admin_bar_menu', __NAMESPACE__ . '\\add_toolbar_items', 100 );
