<?php
/**
 * Base controller.
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
 * Base controller.
 *
 * @since 0.0.1
 */
class BaseController
{
	/**
	 * Register hooks callback
	 *
	 * @since 0.0.1
	 */
	public function register(): void
	{
	}

	/**
	 * Render view file and pass data to the file.
	 *
	 * @since 0.0.1
	 *
	 * @throws Exception - if file not found throw exception
	 * @throws Exception - if data is not array throw exception
	 */
	public function render(string $file_path, array $data = array(), bool $buffer = false ): int
	{
		if ( ! $buffer) {
			return wp_sites_monitor_render_view_template( $file_path, $data );
		}
		ob_start();
		wp_sites_monitor_render_view_template( $file_path, $data );
		return ob_get_clean();
	}
}
