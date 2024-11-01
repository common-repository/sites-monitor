<?php
/**
 * Register service provider.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPSitesMonitor\Interfaces\Providers\ServiceProviderInterface;
use WPSitesMonitor\Interfaces\Services\ServiceInterface;

/**
 * Register service provider.
 *
 * @since 1.2.0
 */
class ServiceProvider implements ServiceProviderInterface
{
	protected $services = array();

	/**
	 * Registers the services
	 */
	public function register(): void
	{
		foreach ( $this->services as $service ) {
			$service->register();
		}
	}

	/**
	 * Boots the services
	 */
	public function boot(): void
	{
		foreach ( $this->services as $service ) {
			if ( false === $service instanceof ServiceInterface ) {
				continue;
			}
			$service->boot();
		}
	}
}
