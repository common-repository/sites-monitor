<?php
/**
 * Settings controller.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   0.0.1
 */

namespace WPSitesMonitor\Controllers;

use Exception;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Settings controller.
 *
 * @since 0.0.1
 */
class SettingsController extends BaseController
{
	/**
	 * Render the settings page.
	 *
	 * @since 0.0.1
	 *
	 * @throws Exception - if file not found throw exception
	 * @throws Exception - if data is not array throw exception
	 */
	public function render_page(): void
	{
		$this->render( 'admin/settings-page.php' );
	}
}
