<?php
/**
 * Register wp_schedule_event service.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   0.0.1
 */

namespace WPSitesMonitor\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPSitesMonitor\Interfaces\Services\EventServiceInterface;

/**
 * Register wp_schedule_event service.
 *
 * @since 0.0.1
 */
class EventService extends Service implements EventServiceInterface
{
	/**
	 * Schedule a new wp event unless it already exists.
	 *
	 * @since 0.0.1
	 *
	 * @param string $interval | hourly, twicedaily, daily, weekly
	 */
	public static function schedule( string $interval, string $event_name ): void
	{
		$args = array( $event_name );

		if ( ! wp_next_scheduled( 'wpsm_push_event', $args )) {
			wp_schedule_event( time(), $interval, 'wpsm_push_event', $args );
		}
	}

	/**
	 * Reschedule event when the settings have changed.
	 *
	 * @since 0.0.1
	 *
	 * @param string $interval | hourly, twicedaily, daily, weekly
	 */
	public static function reschedule( string $interval, string $event_name ): void
	{
		self::unschedule( $event_name );
		self::schedule( $interval, $event_name );
	}

	/**
	 * Clear scheduled wp event.
	 *
	 * @since 0.0.1
	 */
	public static function unschedule( string $event_name ): void
	{
		$args = array( $event_name );

		if ( wp_next_scheduled( 'wpsm_push_event', $args ) ) {
			wp_clear_scheduled_hook( 'wpsm_push_event', $args );
		}
	}
}
