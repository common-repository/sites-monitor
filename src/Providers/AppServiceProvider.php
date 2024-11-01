<?php
/**
 * App service provider (registers general plugins functionality).
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

use WPSitesMonitor\Interfaces\Providers\AppServiceProviderInterface;
use WPSitesMonitor\Interfaces\Services\EventServiceInterface;
use WPSitesMonitor\Interfaces\Services\LifeCycleServiceInterface;
use WPSitesMonitor\Interfaces\Services\ResourceServiceInterface;

/**
 * App service provider (registers general plugins functionality).
 *
 * @since 1.2.0
 */
class AppServiceProvider extends ServiceProvider implements AppServiceProviderInterface
{
	public function __construct(
		EventServiceInterface $event_service,
		LifeCycleServiceInterface $life_cycle_service,
		ResourceServiceInterface $resource_service
	) {
		$this->services = array(
			'event'      => $event_service,
			'resource'   => $resource_service,
			'life_cycle' => $life_cycle_service,
		);

		$this->register_hooks();
	}

	protected function register_hooks() {
		sm_fs()->add_filter( 'is_submenu_visible', array( $this, 'sm_fs_is_submenu_visible' ), 10, 2 );
		sm_fs()->add_filter( 'show_deactivation_feedback_form', '__return_false' );
	}

	/**
	 * Only show the submenu when site is a monitor.
	 * This is because normal sites do not need upgrades.
	 *
	 * @since 1.1.1
	 */
	public function sm_fs_is_submenu_visible( $is_visible, $id ): bool
	{
		if ( 'pricing' !== $id ) {
			return $is_visible;
		}

		$type       = get_option( 'wpsm_type_of_website' );
		$is_site    = $type && in_array( 'is_site', $type, true );
		$is_monitor = $type && in_array( 'is_monitor', $type, true );

		if ($is_site && ! $is_monitor || empty( $type )) {
			$is_visible = false;
		}

		return $is_visible;
	}
}
