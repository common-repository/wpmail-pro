<?php

namespace WPMP;

use \WPMP\Config;

defined( 'ABSPATH' ) || exit;

require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

class Sparkpost_Mailer extends \PHPMailer\PHPMailer\PHPMailer {
	public $wp_mail_args;

	/**
	 * Constructor.
	 * @param boolean $exceptions Should we throw external exceptions?
	 */
	public function __construct( $exceptions = false ) {
		parent::__construct( $exceptions );

		do_action( 'wpmp/init_mailer', $this );
	}

	/**
	 * Send mail using SparkPost
	 * @param string $header The message headers
	 * @param string $body The message body
	 * @throws SparkPostException
	 * @access protected
	 * @return boolean
	 */
	protected function mailSend( $header, $body ) {
		return $this->sparkpost_send();
	}

	/**
	 * Send the mail.
	 *
	 * @return void
	 */
	protected function sparkpost_send() {
		$body = $this->get_request_body();

		if ( ! $body ) {
			$this->error( 'Failed to prepare transmission request body' );

			$this->write_to_log_table( [
				'status'      => 'error',
				'api_details' => 'Empty request body.',
			] );

			return false;
		}

		if ( wpmp_free_plan_site_is_flagged() ) {
			$this->error( 'Free plan sending limit is reached, cannot send e-mail' );

			$this->write_to_log_table( [
				'status'      => 'error',
				'api_details' => 'Your sending limit has been reached.',
			], null, $body );

			return false;
		}

		$api = wpmp_get_sparkpost_api();

		$api->send_email( $body );

		$result = $api->get_response();
		$result = apply_filters( 'wpmp/handle_response', $result, $body );

		if ( is_bool( $result ) ) {
			return $result;
		}

		return $this->handle_response( $result, $body );
	}

	/**
	 * Build the request body to be sent to the SparkPost API.
	 *
	 * @return array
	 */
	protected function get_request_body() {
		$sender  = $this->get_sender();
		$replyTo = $this->get_reply_to();
		$body    = array();

		// Add recipients.
		$body['recipients'] = $this->get_recipients();

		// Enable engagement tracking.
		$body['options'] = array(
			'open_tracking'  => (bool) apply_filters( 'wpmp/open_tracking', true ),
			'click_tracking' => (bool) apply_filters( 'wpmp/click_tracking', true ),
			'transactional'  => (bool) apply_filters( 'wpmp/transactional', true ),
		);

		$attachments     = $this->get_attachments();
		$content_headers = $this->get_headers();

		// Inline content.
		$body['content'] = array(
			'from'    => $sender,
			'subject' => $this->Subject
		);

		if ( ! empty( $content_headers ) ) {
			$body['content']['headers'] = $content_headers;
		}

		if ( $replyTo ) {
			$body['content']['reply_to'] = $replyTo;
		}

		switch ( $this->ContentType ) {
			case 'multipart/alternative':
				$body['content']['html'] = $this->Body;
				$body['content']['text'] = $this->AltBody;
				break;
			case 'text/plain':
				$body['content']['text'] = $this->Body;
				break;
			default:
				$body['content']['html'] = $this->Body;
				break;
		}

		if ( ! empty( $attachments ) ) {
			$body['content']['attachments'] = $attachments;
		}

		$is_sandbox = false;

		if ( isset( $body['content']['from']['email'] ) && $is_sandbox ) {
			$body['content']['from']['email'] = 'anything@sparkpostbox.com';
			$body['options']['sandbox']       = true;
		}

		$body = apply_filters( 'wpmp/request_body', $body );

		return $body;
	}

	/**
	 * Get the sender.
	 *
	 * @return array
	 */
	protected function get_sender() {
		$from = array(
			'email' => $this->From,
		);

		if ( ! empty( $this->FromName ) ) {
			$from['name'] = $this->FromName;
		}

		return $from;
	}

	/**
	 * Get attachments.
	 *
	 * @return array
	 */
	protected function get_attachments() {
		$attachments = array();

		foreach ( $this->getAttachments() as $attachment ) {
			$attachments[] = array(
				'name' => $attachment[2],
				'type' => $attachment[4],
				'data' => base64_encode( $this->read_attachment( $attachment[0] ) ),
			);
		}

		return $attachments;
	}

	/**
	 * Read attachment.
	 *
	 * @param resource $data
	 * @return string
	 */
	protected function read_attachment($data) {
		if ( is_file( $data ) ) {
			return file_get_contents($data);
		}

		return $data;
	}

	/**
	 * Set mail provider.
	 *
	 * @return void
	 */
	public function isMail() {
		$this->Mailer = 'sparkpost';
	}

	/**
	 * Save transmission request data + result into our log table.
	 *
	 * @param array $data
	 * @param object $response
	 * @param object $body
	 * @return void
	 */
	protected function write_to_log_table( $data = [], $response = null, $body = null ) {
		global $wpdb;

		if ( isset( $body['content']['html'] ) ) {
			$email_content = $body['content']['html'];
		} elseif ( isset( $body['content']['text'] ) ) {
			$email_content = $body['content']['text'];
		} else {
			$email_content = null;
		}

		$full_data = array_merge(
			[
				'email_sender'    => sanitize_text_field( $this->get_sender()['email'] ),
				'email_recipient' => implode( ',', wp_list_pluck( wp_list_pluck( $this->get_recipients(), 'address' ), 'email' ) ),
				'email_subject'   => sanitize_text_field( $this->Subject ),
				'email_content'   => $email_content,
				'api_provider'    => 'sparkpost',
				'created_at'      => current_time( 'mysql', true ),
			],
			$data
		);

		$wpdb->insert(
			Config::emails_table(),
			$full_data
		);

		do_action( 'wpmp/email_logged', $full_data, $body );
	}

	/**
	 * Handle API response.
	 *
	 * @param array $response
	 * @param array $body
	 * @return void
	 */
	protected function handle_response( $response, $request_body = null ) {
		if ( is_wp_error( $response ) ) {
			$this->write_to_log_table( [
				'status'      => 'error',
				'api_details' => $response->get_error_message(),
			], $response, $request_body );

			return false;
		}

		$body = $response;

		do_action( 'wpmp/response_body', $body, $this );

		/**
		 * Errors = failure.
		 */
		if ( property_exists( $body, 'errors' ) ) {
			$this->debug( 'Error in transmission' );
			$this->error( $body->errors );

			$this->write_to_log_table( [
				'status'      => 'error',
				'api_details' => maybe_serialize( $body ),
			], $response, $request_body );

			return false;
		}

		if ( property_exists( $body, 'results' ) ) {
			$data = $body->results;
		} else {
			$this->debug( 'API response is unknown' );
			$this->error( 'Unknown response' );

			$this->write_to_log_table( [
				'status'      => 'error',
				'api_details' => maybe_serialize( $body ),
			], $data, $request_body );

			return false;
		}

		/**
		 * Rejected = failure.
		 */
		if ( isset( $data->total_rejected_recipients ) && (int) $data->total_rejected_recipients > 0 ) {
			$this->debug( sprintf( 'Sending to %d recipient(s) failed', $data->total_rejected_recipients ) );
			$this->error( $data );

			$this->write_to_log_table( [
				'status'      => 'error',
				'api_id'      => isset( $data->id ) ? sanitize_text_field( $data->id ) : null,
				'api_details' => maybe_serialize( $data ),
			], $data, $request_body );

			return false;
		}

		/**
		 * Accepted = success!
		 */
		if ( isset( $data->total_accepted_recipients ) && (int) $data->total_accepted_recipients > 0 ) {
			$this->debug( sprintf( 'Successfully sent to %d recipient(s)', $data->total_accepted_recipients ) );
			$this->debug( sprintf( 'Transmission ID is %s', $data->id ) );

			$this->write_to_log_table( [
				'status'      => 'pending',
				'api_id'      => isset( $data->id ) ? sanitize_text_field( $data->id ) : null,
				'api_details' => maybe_serialize( $data ),
			], $data, $request_body );

			return true;
		}

		return false;
	}

	/**
	 * Get recipients, formatted for SparkPost API.
	 *
	 * @return array
	 */
	protected function get_recipients() {
		$recipients = array();
		$recipients_header_to = array();

		//prepare header_to
		foreach ($this->to as $to) {
			if (empty($to[1])) { // if name is empty use only address
				$recipients_header_to[] = $to[0];
			} else { // otherwise, use name and email
				$recipients_header_to[] = sprintf('%s <%s>', $to[1], $to[0]);
			}
		}
		$recipients_header_to = implode(', ', $recipients_header_to);

		foreach ($this->to as $to) {
			$recipients[] = $this->build_recipient($to[0], $to[1], $recipients_header_to);
		}

		// include bcc to recipients
		$recipients = array_merge($recipients, $this->get_bcc( $recipients_header_to ) );

		// include cc to recipients, they need to included in recipients and in headers (refer to get_headers method)
		$recipients = array_merge($recipients, $this->get_cc( $recipients_header_to ) );

		return apply_filters( 'wpmp/recipients', $recipients );
	}

	/**
	 * Get request headers.
	 *
	 * @return array
	 */
	public function get_request_headers() {
		return apply_filters( 'wpmp/request_headers', array(
			'Content-Type'  => 'application/json',
			'Authorization' => wpmp_get_sparkpost_api_key(),
		) );
	}

	/**
	 * Returns the list of Reply-To recipients
	 * For WordPress version below 4.6
	 * @return array
	 * TODO Remove this when wordpress does not support version below 4.6
	 */
	protected function parse_reply_to_from_custom_header() {
		$replyTos = array();
		foreach ($this->getCustomHeaders() as $header) { // wp_mail sets Reply-To as custom header (does not use phpmailer->addReplyTo)
			list($name, $value) = $header;
			if ($name === 'Reply-To' && !empty($value)) {
				$replyTos[] = trim($value);
			}
		}

		return implode(',', $replyTos);
	}

	/**
	 * Returns list of Reply-To recipients
	 * For WordPress 4.6 and above
	 * @return array Formatted list of reply tos
	 */
	protected function parse_reply_to() {
		$replyTos = array();
		foreach ($this->ReplyTo as $reply_to) {
			$name = $reply_to[1];
			$email = $reply_to[0];
			if (empty($name)) {
				$replyTos[] = $email;
			} else {
				$replyTos[] = sprintf('%s <%s>', $name, $email);
			}
		}

		return apply_filters('wpmp/reply_to', implode(',', $replyTos));
	}

	protected function get_reply_to() {
		if ( (bool) Options\get_plugin_option( 'reply_to_enabled', false ) ) {
			$reply_to_mail = Options\get_plugin_option( 'reply_to_mail', '' );
			$reply_to_name = Options\get_plugin_option( 'reply_to_name', '' );

			if ( ! empty( $reply_to_mail ) ) {
				return sprintf( '%2$s <%1$s>', $reply_to_mail, $reply_to_name );
			}
		}

		$wp_version = get_bloginfo('version');

		if (version_compare($wp_version, '4.6') == -1) { // if lower than 4.6
			return $this->parse_reply_to_from_custom_header();
		} else {
			return $this->parse_reply_to();
		}
	}

	/**
	 * Build a SparkPost API recipient object.
	 *
	 * @param string $email
	 * @param string $name
	 * @param string $header_to
	 * @return void
	 */
	protected function build_recipient($email, $name = '', $header_to = '') {
		$recipient = array(
			'address' => array(
				'email' => $email,
				'name' => $name,
			)
		);

		if (!empty($header_to)) {
			$recipient['address']['header_to'] = $header_to;
			/* if header_to is like 'Name <email>', then having name attribute causes
			showing weird display of name in the delivered mail. So, let's remove it
			when header_to is set.
			*/
			unset($recipient['address']['name']);
		}

		return $recipient;
	}

	/**
	 * Returns the list of BCC recipients
	 * @return array
	 */
	protected function get_bcc($header_to) {
		$bcc = array();
		foreach ($this->getBccAddresses() as $bccAddress) {
			$bcc[] = $this->build_recipient($bccAddress[0], $bccAddress[1], $header_to);
		}
		return $bcc;
	}

	/**
	 * Returns the list of CC recipients
	 * @header_to string Optional, shouldn't be used for setting CC in headers
	 * @return array
	 */
	protected function get_cc($header_to = '') {
		$cc = array();
		foreach ($this->getCcAddresses() as $ccAddress) {
			$cc[] = $this->build_recipient($ccAddress[0], $ccAddress[1], $header_to);
		}
		return $cc;
	}

	protected function stringify_recipients($recipients) {
		$recipients_list = array();

		foreach ($recipients as $recipient) {
			if (!empty($recipient['address']['name'])) {
				$recipients_list[] = sprintf('%s <%s>', $recipient['address']['name'], $recipient['address']['email']);
			} else {
				$recipients_list[] = $recipient['address']['email'];
			}
		}

		return implode(',', $recipients_list);
	}

	/**
	 * Returns a collection that can be sent as headers in body
	 * @return array
	 */
	protected function get_headers() {
		$unsupported_headers = array(
			'From', 'Subject', 'To', 'Reply-To', 'Cc',
			'Content-Type', 'Content-Transfer-Encoding', 'MIME-Version'
		);
		$headers = $this->createHeader();


		$formatted_headers = array();
		// split by line separator
		foreach (explode($this::$LE, $headers) as $line) {

			$splitted_line = explode(': ', $line);
			$key = trim($splitted_line[0]);

			if (!in_array($key, $unsupported_headers) && !empty($key) && !empty($splitted_line[1])) {
				$formatted_headers[$key] = trim($splitted_line[1]);
			}
		}

		// include cc in header
		$cc = $this->get_cc();
		if (!empty($cc)) {
			$formatted_headers['CC'] = $this->stringify_recipients($cc);
		}

		return apply_filters('wpmp/body_headers', $formatted_headers);
	}

	function check_permission_error($response, $permission) {
		$response = (array)$response;
		if (!empty($response['response']) && $response['response']['code'] === 403) {
			$this->debug("API Key might not have {$permission} permission. Actual Error: " . print_r($response['response'], true));
			$this->error("API Key might not have {$permission} permission");
			return true;
		}
		return false;
	}

	public function debug($msg) {
		$this->edebug($msg);
	}

	public function error($msg) {
		$this->setError($msg);
	}
}
