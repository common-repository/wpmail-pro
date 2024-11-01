<?php

namespace WPMP\Admin_Preview_Email;

defined( 'ABSPATH' ) || exit;

use WPMP\Config;

/**
 * Output the HTML content of the e-mail preview page.
 *
 * @return void
 */
function output_preview_email_iframe_container( $email_id, $token ) {
	if ( ! wp_verify_nonce( $token, sprintf( 'wPMp-pr3v|iew_%1$d-m41l', $email_id ) ) ) {
		return;
	}

	global $wpdb;

	$table   = Config::emails_table();
	$content = $wpdb->get_var( $wpdb->prepare( "SELECT email_content FROM $table WHERE id = %d", $email_id ) );

	?>
	<div id="wpmp-email-content-preview">
		<div class="email-preview-wrapper"><?php echo $content; ?></div>
	</div>
	<?php
}
add_action( 'wpmp/admin/preview_email_content', __NAMESPACE__ . '\\output_preview_email_iframe_container', 10, 2 );

/**
 * Get a list of HTML allowed tags.
 *
 * @return array
 */
function get_kses_allowed_html() {
	$allowed_tags         = wp_kses_allowed_html( 'post' );
	$allowed_tags['link'] = array(
		'rel'   => true,
		'href'  => true,
		'type'  => true,
		'media' => true,
	);

	return $allowed_tags;
}
