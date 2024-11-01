<?php
/**
 * Event service interface.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Interfaces\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Event service interface.
 *
 * @since 1.2.0
 */
interface EventServiceInterface extends ServiceInterface
{
	public static function schedule( string $interval, string $event_name );
	public static function reschedule( string $interval, string $event_name );
	public static function unschedule( string $event_name );
}
