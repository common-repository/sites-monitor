<?php
/**
 * Settings Controller Interface.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Interfaces\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Settings Controller Interface.
 *
 * @since 1.2.0
 */
interface SettingsControllerInterface extends BaseControllerInterface
{
	public function render_page();
}
