<?php
/**
 * Monitor Api Controller Interface.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Interfaces\Controllers\Api;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Monitor Api Controller Interface.
 *
 * @since 1.2.0
 */
interface MonitorApiControllerInterface extends ApiControllerInterface
{
	public function register_routes();
	public function register_monitor_routes( string $context );
}
