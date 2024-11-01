<?php
/**
 * Service interface.
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
 * Service interface.
 *
 * @since 1.2.0
 */
interface ServiceInterface
{
	public function register();
	public function boot();
}
