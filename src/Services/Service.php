<?php
/**
 * Register base service.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPSitesMonitor\Interfaces\Services\ServiceInterface;

/**
 * Register base service.
 */
class Service implements ServiceInterface
{
	/**
	 * Register the service.
	 *
	 * @since 1.2.0
	 */
	public function register(): void
	{
	}

	/**
	 * Called when all services are registered.
	 *
	 * @since 1.2.0
	 */
	public function boot(): void
	{
	}
}
