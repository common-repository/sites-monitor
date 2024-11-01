<?php
/**
 * Life cycle service interface.
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
 * Life cycle service interface.
 *
 * @since 1.2.0
 */
interface LifeCycleServiceInterface extends ServiceInterface
{
	public function install();
	public function deactivate();
	public static function uninstall();
}
