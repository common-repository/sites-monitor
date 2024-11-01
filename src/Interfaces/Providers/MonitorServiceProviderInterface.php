<?php
/**
 * Monitor service provider interface.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Interfaces\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Monitor service provider interface.
 *
 * @since 1.2.0
 */
interface MonitorServiceProviderInterface extends ServiceProviderInterface
{
}
