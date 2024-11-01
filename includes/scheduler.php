<?php

namespace WPMP\Scheduler;

use \WPMP\Options;
use \WPMP\Webhooks;

defined( 'ABSPATH' ) || exit;

/**
 * Schedule an asynchronous job.
 *
 * @param string $hook
 * @param array $args
 * @return void
 */
function schedule( $hook, $args = [], $time = null ) {
	$existing_actions = as_get_scheduled_actions(
		[
			'hook'     => $hook,
			'args'     => $args,
			'status'   => \ActionScheduler_Store::STATUS_PENDING,
			'per_page' => -1,
		]
	);

	$time = is_null( $time ) ? time() + 60 : $time;

	if ( empty( $existing_actions ) ) {
		as_schedule_single_action( $time, $hook, $args );
	}
}

//============================
//                            
//    ###     ####  ######  
//   ## ##   ##       ##    
//  ##   ##  ##       ##    
//  #######  ##       ##    
//  ##   ##   ####    ##    
//                            
//============================

/**
 * Retry SparkPost webhook creation if failed.
 *
 * @return void
 */
function retry_sparkpost_webhook_creation() {
	Webhooks\create_webhook();
}
add_action( 'wpmp/retry_sparkpost_webhook_creation', __NAMESPACE__ . '\\retry_sparkpost_webhook_creation' );
