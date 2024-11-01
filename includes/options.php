<?php

namespace WPMP\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Get a specific option value.
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function get_plugin_option( $key, $default = null ) {
	if ( \WPMP\Helpers\is_active_on_multisite() ) {
		return get_site_option( "wpmp_{$key}", $default );
	}

	return get_option( "wpmp_{$key}", $default );
}

/**
 * Update a specific option.
 *
 * @param string $key
 * @param mixed $value
 * @param boolean $autoload
 * @return void
 */
function update_plugin_option( $key, $value, $autoload = false ) {
	if ( \WPMP\Helpers\is_active_on_multisite() ) {
		update_site_option( "wpmp_{$key}", $value, $autoload );
	}

	update_option( "wpmp_{$key}", $value, $autoload );
}

/**
 * Delete a specific option.
 *
 * @param string $key
 * @return void
 */
function delete_plugin_option( $key ) {
	if ( \WPMP\Helpers\is_active_on_multisite() ) {
		delete_site_option( "wpmp_{$key}" );
	}

	delete_option( "wpmp_{$key}" );
}

/**
 * Do we already have a value for a specific option?
 *
 * @param string $key
 * @return boolean
 */
function has_plugin_option( $key ) {
	return ! empty( get_plugin_option( $key ) );
}
