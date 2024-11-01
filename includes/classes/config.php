<?php

namespace WPMP;

defined( 'ABSPATH' ) || exit;

class Config {
	/**
	 * Admin page slug.
	 */
	const ADMIN_PAGE_SLUG = 'wpmailpro';

	/**
	 * Admin preview page slug.
	 */
	const ADMIN_EMAIL_PREVIEW_PAGE_SLUG = 'wpmailpro-preview-email';

	/**
	 * E-mails table name
	 */
	const EMAILS_TABLE_NAME = 'wpmp_emails';

	/**
	 * Number of logs to display per page.
	 */
	const LOGS_PER_PAGE = 25;

	/**
	 * SparkPost API base URL.
	 */
	const SPARKPOST_API_BASE_URL = 'https://api.sparkpost.com/api/v1';

	/**
	 * Default sending domain.
	 */
	const DEFAULT_SENDING_DOMAIN = 'wordsent.com';

	/**
	 * Webhook events to subscribe to.
	 */
	const WEBHOOK_EVENTS = [
		'delivery', 'bounce', 'spam_complaint', 'out_of_band',
		'policy_rejection', 'delay', 'generation_failure', 'generation_rejection',
		'click', 'amp_click',
		'open', 'initial_open', 'amp_open', 'amp_initial_open',
	];

	/**
	 * Get the full E-mails SQL table name, prefixed.
	 *
	 * @return string
	 */
	public static function emails_table() {
		global $wpdb;

		$prefix = \WPMP\Helpers\is_active_on_multisite() ? $wpdb->base_prefix : $wpdb->prefix;

		return $prefix . self::EMAILS_TABLE_NAME;
	}

	/**
	 * Get an incoming webhook URL.
	 *
	 * @param string $type
	 * @return string
	 */
	public static function get_webhook_url( $type = '' ) {
		if ( defined( 'WPMP_WEBHOOKS_URL' ) && is_array( WPMP_WEBHOOKS_URL ) && isset( WPMP_WEBHOOKS_URL[ $type ] ) ) {
			return WPMP_WEBHOOKS_URL[ $type ];
		}

		return trailingslashit( rest_url() ) . "wpmailpro/v1/webhook/{$type}";
	}
}
