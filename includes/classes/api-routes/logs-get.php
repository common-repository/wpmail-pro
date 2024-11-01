<?php

namespace WPMP\API_Routes;

use \WPMP\Config;

defined( 'ABSPATH' ) || exit;

class Logs_Get extends \WPMP\API_Route {
	/**
	 * Validate the /admin/logs-get/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		if ( ! wp_verify_nonce( $this->get_request()->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error( 'unauthorized_access', __( 'You can\'t do that.', 'wpmp' ), [ 'status' => 403 ] );
		}

		return true;
	}

	/**
	 * Process a valid request: get logs.
	 *
	 * @return array
	 */
	public function process() {
		$filters   = $this->get_param( 'filters' );
		$page      = (int) $this->get_param( 'page' );
		$logs_data = $this->get_logs( $filters, $page );

		return [
			'success' => true,
			'message' => null,
			'fields'  => (object) [
				'logs' => (object) [
					'data'       => $logs_data->logs,
					'pagination' => (object) [
						'page'      => $page,
						'max_pages' => ceil( $logs_data->total / Config::LOGS_PER_PAGE ),
						'total'     => $logs_data->total,
					],
				],
			],
		];
	}

	/**
	 * Get the logs from database.
	 *
	 * @param array $filters
	 * @return array
	 */
	protected function get_logs( $filters = [], $page = 1 ) {
		global $wpdb;
		$table_name = Config::emails_table();
		$limit      = Config::LOGS_PER_PAGE;
		$variables  = [];

		$query = "SELECT * FROM $table_name WHERE 1 = 1";
		$total = "SELECT COUNT( id ) FROM $table_name WHERE 1 = 1";

		if ( ! empty( $filters['status'] ) && $filters['status'] !== 'all' ) {
			$total      .= " AND status = %s";
			$query      .= " AND status = %s";
			$variables[] = sanitize_text_field( $filters['status'] );
		}

		if ( ! empty( $filters['recipient'] ) ) {
			$total      .= " AND email_recipient LIKE %s";
			$query      .= " AND email_recipient LIKE %s";
			$variables[] = sprintf( '%%%1$s%%', sanitize_text_field( $filters['recipient'] ) );
		}

		if ( ! empty( $filters['date_range'] ) ) {
			switch ( $filters['date_range'] ) {
				default:
				case 'last_30days':
					$total .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d DAY )', 30 );
					$query .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d DAY )', 30 );
					break;
				case 'last_7days':
					$total .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d DAY )', 7 );
					$query .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d DAY )', 7 );
					break;
				case 'last_24hours':
					$total .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d HOUR )', 24 );
					$query .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d HOUR )', 24 );
					break;
				case 'last_hour':
					$total .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d HOUR )', 1 );
					$query .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d HOUR )', 1 );
					break;
			}
		} else {
			$total .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d DAY )', 30 );
			$query .= sprintf( ' AND created_at >= DATE_SUB( NOW(), INTERVAL %1$d DAY )', 30 );
		}

		// Get totals now before adding LIMIT and OFFSET variables to avoid issues.
		$totals = $wpdb->get_var( $wpdb->prepare( $total, $variables ) );

		$query      .= ' ORDER BY created_at DESC';
		$query      .= " LIMIT %d OFFSET %d";
		$variables[] = $limit;
		$variables[] = ( $page - 1 ) * $limit;

		return (object) [
			'logs'  => $this->shape( $wpdb->get_results( $wpdb->prepare( $query, $variables ) ) ),
			'total' => (int) $totals,
		];
	}

	/**
	 * Transform a log row from the database to a usable object on the front-end.
	 *
	 * @param array $logs
	 * @return array
	 */
	protected function shape( $logs = [] ) {
		return array_map( function( $log ) {
			$timestamp   = strtotime( $log->created_at );
			$format      = sprintf( '%1$s %2$s', get_option( 'date_format' ), get_option( 'time_format' ) );
			$date        = wp_date( $format, $timestamp );
			$has_content = ! empty( $log->email_content );
			$preview_url = '';

			if ( $has_content ) {
				$nonce       = wp_create_nonce( sprintf( 'wPMp-pr3v|iew_%1$d-m41l', $log->id ) );
				$preview_url = admin_url( 'admin.php?page=wpmailpro-preview-email' );
				$preview_url = add_query_arg( [ 'email_id' => $log->id, 'token' => $nonce ], $preview_url );
			}

			return (object) [
				'id'          => $log->id,
				'sender'      => $log->email_sender,
				'subject'     => esc_html( $log->email_subject ),
				'recipient'   => $log->email_recipient,
				'status'      => $log->status,
				'has_error'   => in_array( $log->status, [ 'error', 'failed' ] ),
				'details'     => $this->get_details( $log ),
				'engagement'  => $this->get_engagement( $log ),
				'has_content' => $has_content,
				'preview_url' => $preview_url,
				'date'        => $date,
			];
		}, $logs );
	}

	/**
	 * Get details for a specific log, depending on its status.
	 *
	 * @param object $log
	 * @return string
	 */
	protected function get_details( $log ) {
		if ( in_array( $log->status, [ 'pending', 'delivered' ], true ) ) {
			return sprintf( '%1$s %2$s', __( 'Transmission ID:', 'wpmp' ), sanitize_text_field( $log->api_id ) );
		} elseif ( in_array( $log->status, [ 'error' ], true ) ) {
			$details = maybe_unserialize( $log->api_details );

			if ( is_scalar( $details ) ) {
				return sprintf( '%1$s %2$s', __( 'Error:', 'wpmp' ), sanitize_text_field( html_entity_decode( str_replace( [ '&lt;', '&gt;' ], [ '', '' ], $details ) ) ) );
			} elseif ( isset( $details->errors ) ) {
				$error = is_array( $details->errors ) ? $details->errors[0] : $details->errors;
				$info  = '';

				if ( isset( $error->message ) ) {
					$info .= sanitize_text_field( $error->message );
				}

				if ( isset( $error->code ) ) {
					$info .= sprintf( '[%1$s %2$s]', __( 'code', 'wpmp' ), sanitize_text_field( $error->code ) );
				}

				return sprintf( '%1$s %2$s', __( 'Error:', 'wpmp' ), sanitize_text_field( html_entity_decode( $info ) ) );
			}
		} elseif ( in_array( $log->status, [ 'failed' ], true ) ) {
			$details = maybe_unserialize( $log->last_event_details );

			if ( isset( $details['reason'] ) ) {
				return sprintf( '%1$s %2$s', __( 'Error:', 'wpmp' ), sanitize_text_field( html_entity_decode( str_replace( [ '&lt;', '&gt;' ], [ '', '' ], $details['reason'] ) ) ) );
			}
		}

		return __( 'No info', 'wpmp' );
	}

	/**
	 * Get the engagement details (click/open status).
	 *
	 * @param object $log
	 * @return object
	 */
	protected function get_engagement( $log ) {
		if ( ! isset( $log->engagement_details ) || empty( $log->engagement_details ) ) {
			if ( in_array( $log->status, [ 'pending', 'delivered' ], true ) ) {
				return (object) [
					'opened'  => false,
					'clicked' => false,
				];
			}

			return null;
		}

		$details = maybe_unserialize( $log->engagement_details );

		return (object) [
			'opened'  => isset( $details['open'] ),
			'clicked' => isset( $details['click'] ),
		];
	}
}
