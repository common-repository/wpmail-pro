<?php

namespace WPMP\API_Routes;

use \WPMP\Config;

defined( 'ABSPATH' ) || exit;

class Transmission_Status_Update extends \WPMP\API_Route {
	protected $tracking_events_matrix = [
		'click'            => 'click',
		'amp_click'        => 'click',
		'open'             => 'open',
		'initial_open'     => 'open',
		'amp_open'         => 'open',
		'amp_initial_open' => 'open',
	];

	/**
	 * Validate the /webhook/transmission-status-update/ AJAX request.
	 *
	 * @return true|\WP_Error
	 */
	public function is_valid() {
		return true;
	}

	/**
	 * Process a valid request: try to get metrics from SparkPost API.
	 *
	 * @return array
	 */
	public function process() {
		global $wpdb;
		$events = $this->get_request()->get_json_params();

		if ( ! is_array( $events ) ) {
			return new \WP_Error( 'corrupted_data', __( 'The data provided is not an array.', 'wpmp' ), [ 'status' => 403 ] );
		}

		foreach ( $events as $event ) {
			if ( 
				! isset( $event['msys'], $event['msys']['message_event'] )
				&& ! isset( $event['msys'], $event['msys']['track_event'] )
			) {
				return new \WP_Error( 'missing_msys', __( 'The "msys" and "msys.message_event" or "msys.track_event" properties are required.', 'wpmp' ), [ 'status' => 403 ] );
			}

			/**
			 * Delivery/message events.
			 */
			if ( isset( $event['msys']['message_event'] ) ) {
				$event = $event['msys']['message_event'];

				if ( ! in_array( $event['type'], Config::WEBHOOK_EVENTS ) ) {
					return new \WP_Error( 'unwanted_event_type', __( 'This plugin did not subscribe to this event type.', 'wpmp' ), [ 'status' => 403 ] );
				}

				$data = [
					'last_event_status'  => sanitize_text_field( $event['type'] ),
					'last_event_details' => maybe_serialize( $event ),
					'last_event_date'    => date( 'Y-m-d H:i:s', $event['timestamp'] ),
				];

				if ( in_array( $event['type'], [ 'bounce', 'spam_complaint', 'out_of_band', 'policy_rejection', 'delay', 'generation_failure', 'generation_rejection' ], true ) ) {
					$data['status'] = 'failed';
				} elseif ( $event['type'] === 'delivery' ) {
					$data['status'] = 'delivered';
				}

			/**
			 * Tracking events.
			 */
			} elseif ( isset( $event['msys']['track_event'] ) ) {
				$event = $event['msys']['track_event'];

				if ( ! in_array( $event['type'], Config::WEBHOOK_EVENTS ) ) {
					return new \WP_Error( 'unwanted_event_type', __( 'This plugin did not subscribe to this event type.', 'wpmp' ), [ 'status' => 403 ] );
				}

				$event_type  = $this->tracking_events_matrix[ $event['type'] ];
				$engagements = $this->get_transmission_engagement_details( $event['transmission_id'] );

				// Store the new engagement if we don't already have it, or if we have a more recent one (?).
				if (
					! isset( $engagements[ $event_type ] )
					|| ( isset( $engagements[ $event_type ] ) && $engagements[ $event_type ]->timestamp > $event['timestamp'] )
				) {
					$engagements[ $event_type ] = (object) [
						'timestamp' => $event['timestamp'],
						'date'      => date( 'Y-m-d H:i:s', $event['timestamp'] ),
						'type'      => $event_type,
					];
				}


				$data['engagement_details'] = maybe_serialize( $engagements );
			}

			do_action( 'wpmp/sparkpost_api/response', $event, $data );

			$wpdb->update(
				Config::emails_table(),
				$data,
				[ 'api_id' => $event['transmission_id'] ]
			);
		}

		return [
			'success' => true,
		];
	}

	/**
	 * Get engagement details for a specific transmission.
	 *
	 * @param string $transmission_id
	 * @return array
	 */
	protected function get_transmission_engagement_details( $transmission_id ) {
		global $wpdb;
		$table = Config::emails_table();

		$engagements = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT engagement_details FROM $table WHERE api_id = %s",
				$transmission_id
			)
		);

		if ( ! empty( $engagements ) ) {
			return maybe_unserialize( $engagements );
		}

		return [];
	}
}
