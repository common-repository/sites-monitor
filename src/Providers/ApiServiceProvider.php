<?php
/**
 * Register api service provider.
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

use WPSitesMonitor\Interfaces\Providers\ApiServiceProviderInterface;
use WPSitesMonitor\Interfaces\Controllers\Api\MonitorApiControllerInterface;
use WPSitesMonitor\Interfaces\Controllers\Api\SiteApiControllerInterface;

/**
 * Register api service provider.
 *
 * @since 1.2.0
 */
class ApiServiceProvider extends ServiceProvider implements ApiServiceProviderInterface
{
	public function __construct(
		MonitorApiControllerInterface $api_monitor,
		SiteApiControllerInterface $api_site
	) {
		$this->services = array(
			'api_monitor' => $api_monitor,
			'api_site'    => $api_site,
		);
	}
}
