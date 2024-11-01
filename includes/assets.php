<?php

namespace WPMP\Assets;

use \WPMP\Config;
use \WPMP\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue styles and script.
 *
 * @return void
 */
function enqueue_assets() {
	if ( false
	|| ! isset( $_GET['page'] )
	|| ! in_array( $_GET['page'], [ Config::ADMIN_PAGE_SLUG, Config::ADMIN_EMAIL_PREVIEW_PAGE_SLUG ], true ) ) {
		return;
	}

	$enqueuer = new \WPackio\Enqueue( 'wpMailPro', 'dist', WPMP_VERSION, 'plugin', WPMP_DIR . WPMP_PLUGIN_DIRNAME );

	// Enqueue JS file.
	$js = $enqueuer->enqueue( 'admin_js', 'admin', [
		'js'        => true,
		'css'       => false,
		'js_dep'    => [],
		'css_dep'   => [],
		'in_footer' => true,
	] );

	// Enqueue CSS.
	$css = $enqueuer->enqueue( 'admin_css', 'admin', [
		'js'        => true,
		'css'       => true,
		'js_dep'    => [],
		'css_dep'   => [],
		'in_footer' => true,
	] );

	wp_localize_script(
		$js['js'][2]['handle'],
		'WPMailPro',
		get_javascript_data()
	);
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );

/**
 * Construct the JS object used by front-end client.
 *
 * @return array
 */
function get_javascript_data() {
	$domain                 = Options\get_plugin_option( 'domain' );
	$domain_record          = Options\get_plugin_option( "domain_dkim_{$domain}_record", null );
	$domain_record_validity = (bool) Options\get_plugin_option( "domain_dkim_record_{$domain}_is_valid", 0 );
	$domain_record_verified = (int) Options\get_plugin_option( "domain_dkim_record_{$domain}_verified", 0 );

	$data = [
		'data' => [
			'api' => [
				'rest_url' => esc_url_raw( rest_url() ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
			],
			'current_tab' => isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'metrics',
			'fields'      => [
				'default_sending_domain'         => Config::DEFAULT_SENDING_DOMAIN,
				'sending_domain'                 => ! is_null( $domain ) ? $domain : Config::DEFAULT_SENDING_DOMAIN,
				'current_sending_domain'         => $domain,
				'sending_domain_record_hostname' => $domain_record ? $domain_record->hostname : null,
				'sending_domain_record_value'    => $domain_record ? $domain_record->value : null,
				'domain_dkim_record_is_valid'    => $domain_record_validity,
				'domain_dkim_record_verified'    => $domain_record_verified > 0 ? wp_date( 'Y/m/d H:i', $domain_record_verified ) : null,
				'sender_mail_prefix'             => wpmp_get_sender_mail( true ),
				'sender_mail_suffix'             => Options\get_plugin_option( 'sender_mail_suffix', Config::DEFAULT_SENDING_DOMAIN ),
				'default_sender_mail'            => wpmp_get_default_sender_mail(),
				'sender_mail'                    => wpmp_get_sender_mail( false ),
				'sender_name'                    => Options\get_plugin_option( 'sender_name', '' ),
				'force_sender_email'             => (bool) Options\get_plugin_option( 'force_sender_email', false ),
				'reply_to_enabled'               => (bool) Options\get_plugin_option( 'reply_to_enabled', false ),
				'reply_to_mail'                  => Options\get_plugin_option( 'reply_to_mail', '' ),
				'reply_to_name'                  => Options\get_plugin_option( 'reply_to_name', '' ),
				'license'                        => Options\get_plugin_option( 'license', '' ),
				'license_activated'              => (int) Options\get_plugin_option( 'license_activated', 0 ) > 0,
			],
		],
		'strings' => [
			// Top-level, global strings.
			'Global.PageTitle'         => esc_html__( 'WP Mail Pro', 'wpmp' ),
			'Button.Save'              => esc_html__( 'Save', 'wpmp' ),
			'Button.CreateDomain'      => esc_html__( 'Create', 'wpmp' ),
			'Button.LicenseActivate'   => esc_html__( 'Activate', 'wpmp' ),
			'Button.LicenseDeactivate' => esc_html__( 'Deactivate', 'wpmp' ),
			'Button.Refresh'           => esc_html__( 'Refresh', 'wpmp' ),
			'Button.Filter'            => esc_html__( 'Filter', 'wpmp' ),
			'Button.Verify'            => esc_html__( 'Verify', 'wpmp' ),
			'Button.Send'              => esc_html__( 'Send', 'wpmp' ),
			'Text.NoMetricsReason'     => esc_html__( 'Please activate your license in the Settings tab and configure your sending domain first.', 'wpmp' ),
			'Text.NoDNSReason'         => esc_html__( 'Please activate your license in the Settings tab first.', 'wpmp' ),
			'Text.NeedFreeLicense'     => esc_html__( 'Need a free license?', 'wpmp' ),
			'Text.DkimValid'           => esc_html__( 'Valid', 'wpmp' ),
			'Text.DkimInvalid'         => esc_html__( 'Invalid', 'wpmp' ),
			'Text.Since'               => esc_html__( 'Since', 'wpmp' ),
			'Text.NoLogFound'          => esc_html__( 'No log found...', 'wpmp' ),
			'Text.XEmailsFound'        => esc_html__( 'e-mails found', 'wpmp' ),
			'Text.Date'                => esc_html__( 'Date', 'wpmp' ),
			'Text.Sender'              => esc_html__( 'Sender', 'wpmp' ),
			'Text.Subject'             => esc_html__( 'Subject', 'wpmp' ),
			'Text.Recipient'           => esc_html__( 'Recipient', 'wpmp' ),
			'Text.Status'              => esc_html__( 'Status', 'wpmp' ),
			'Text.Hostname'            => esc_html__( 'Hostname', 'wpmp' ),
			'Text.Value'               => esc_html__( 'Value', 'wpmp' ),
			'Text.Copy'                => esc_html__( 'Copy', 'wpmp' ),
			'Text.Upgrade'             => esc_html__( 'Upgrade', 'wpmp' ),
			'Text.TextCopied'          => esc_html__( 'Text copied!', 'wpmp' ),
			'Text.ViewEmail'           => esc_html__( 'View E-mail', 'wpmp' ),
			'Text.EngagementOpened'    => esc_html__( 'Opened', 'wpmp' ),
			'Text.EngagementClicked'   => esc_html__( 'Clicked', 'wpmp' ),
			'Text.ErrorMessage'        => esc_html__( 'Error message', 'wpmp' ),
			'Text.NeedHelp'            => esc_html__( 'Need help?', 'wpmp' ),
			'Text.SendingLimit'        => esc_html__( 'Sending limit', 'wpmp' ),

			// Tab names.
			'Tabs.Metrics'  => esc_html__( 'Metrics', 'wpmp' ),
			'Tabs.Logs'     => esc_html__( 'Logs', 'wpmp' ),
			'Tabs.DNS'      => esc_html__( 'DNS Configuration', 'wpmp' ),
			'Tabs.Settings' => esc_html__( 'Settings', 'wpmp' ),

			// Panels.
			'Panels.DNS.SendingDomainField'   => esc_html__( 'Sending domain configuration', 'wpmp' ),
			'Panels.DNS.SendingDomainRecords' => esc_html__( 'DNS records (TXT)', 'wpmp' ),
			'Panels.DNS.SendingDomainVerify'  => esc_html__( 'Verify DNS records', 'wpmp' ),
			'Panels.DNS.TestEmail'            => esc_html__( 'Test e-mail', 'wpmp' ),
			'Panels.Settings.EmailSender'     => esc_html__( 'Sender e-mail', 'wpmp' ),
			'Panels.Settings.License'         => esc_html__( 'Plugin license', 'wpmp' ),

			// Fields properties.
			'Fields.Logs.FilterByStatus'            => esc_html__( 'Filter by status', 'wpmp' ),
			'Fields.Logs.StatusAll'                 => esc_html__( 'Any status', 'wpmp' ),
			'Fields.Logs.StatusDelivered'           => esc_html__( 'Delivered', 'wpmp' ),
			'Fields.Logs.StatusRejected'            => esc_html__( 'Rejected', 'wpmp' ),
			'Fields.Logs.StatusFailed'              => esc_html__( 'Failed', 'wpmp' ),
			'Fields.Logs.StatusError'               => esc_html__( 'Error', 'wpmp' ),
			'Fields.Logs.StatusPending'             => esc_html__( 'Pending', 'wpmp' ),
			'Fields.Logs.FilterByRecipient'         => esc_html__( 'Filter by recipient', 'wpmp' ),
			'Fields.Logs.LastHour'                  => esc_html__( 'Last hour', 'wpmp' ),
			'Fields.Logs.Last24Hours'               => esc_html__( 'Last 24 hours', 'wpmp' ),
			'Fields.Logs.Last7Days'                 => esc_html__( 'Last 7 days', 'wpmp' ),
			'Fields.Logs.Last30Days'                => esc_html__( 'Last 30 days', 'wpmp' ),
			'Fields.DNS.SendingDomain'              => esc_html__( 'Sending Domain', 'wpmp' ),
			'Fields.DNS.TestEmailRecipient'         => esc_html__( 'Recipient', 'wpmp' ),
			'Fields.Verify.PropagationWarning'      => esc_html__( 'DNS propagation can take up to 24 hours to propagate, please try again later.', 'wpmp' ),
			'Fields.Settings.PluginLicense'         => esc_html__( 'Plugin License', 'wpmp' ),
			'Fields.Settings.EmailSenderMail'       => esc_html__( 'E-mail address', 'wpmp' ),
			'Fields.Settings.EmailSenderName'       => esc_html__( 'Name', 'wpmp' ),
			'Fields.Settings.EmailSenderMailDesc'   => esc_html__( 'Important: Domain must match with one of your verified sending domains from the DNS tab.', 'wpmp' ),
			'Fields.Settings.ForceSenderEmail'      => esc_html__( 'Force sender e-mail', 'wpmp' ),
			'Fields.Settings.ForceSenderEmailDesc'  => esc_html__( 'If checked, the sender e-mail above will be used for all e-mails, ignoring values set by other plugins.', 'wpmp' ),
			'Fields.Settings.ReplyToEnabled'        => esc_html__( 'Reply-to e-mail', 'wpmp' ),
			'Fields.Settings.ReplyToEnabledLabel'   => esc_html__( 'Enable the Reply-to e-mail', 'wpmp' ),
			'Fields.Settings.WarningDefaultDomain1' => esc_html__( 'To improve security and reliability please configure your own sending domain on the DNS Configuration tab.', 'wpmp' ),
			'Fields.Settings.WarningDefaultDomain2' => esc_html__( 'For more information on configuring your DNS click here.', 'wpmp' ),

			// Metrics.
			'Metrics.RangeLast24Hours' => esc_html__( 'Last 24 hours', 'wpmp' ),
			'Metrics.RangeLast7Days'   => esc_html__( 'Last 7 days', 'wpmp' ),
			'Metrics.RangeThisMonth'   => esc_html__( 'This month', 'wpmp' ),
			'Metrics.RangeLastMonth'   => esc_html__( 'Last month', 'wpmp' ),
			'Metrics.Sent'             => esc_html__( 'Sent', 'wpmp' ),
			'Metrics.Delivered'        => esc_html__( 'Delivered', 'wpmp' ),
			'Metrics.Delivered'        => esc_html__( 'Delivered', 'wpmp' ),
			'Metrics.Injected'         => esc_html__( 'Injected', 'wpmp' ),
			'Metrics.Rejected'         => esc_html__( 'Rejected', 'wpmp' ),
			'Metrics.Clicked'          => esc_html__( 'Clicked', 'wpmp' ),
			'Metrics.SpamComplaint'    => esc_html__( 'Complained', 'wpmp' ),
			'Metrics.Targeted'         => esc_html__( 'Targeted', 'wpmp' ),
			'Metrics.Bounced'          => esc_html__( 'Bounced', 'wpmp' ),
			'Metrics.EngagementRates'  => esc_html__( 'Engagement rates', 'wpmp' ),
			'Metrics.OpenRate'         => esc_html__( 'Open rate', 'wpmp' ),
			'Metrics.ClickRate'        => esc_html__( 'Click rate', 'wpmp' ),
			'Metrics.DeliveredRate'    => esc_html__( 'Delivered rate', 'wpmp' ),
			'Metrics.BounceRate'       => esc_html__( 'Bounce rate', 'wpmp' ),
			'Metrics.RejectedRate'     => esc_html__( 'Rejected rate', 'wpmp' ),
		],
	];

	return $data;
}
