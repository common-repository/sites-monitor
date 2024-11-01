<?php
/**
 * Site Api Controller Interface.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Interfaces\Controllers\Api;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Site Api Controller Interface.
 *
 * @since 1.2.0
 */
interface SiteApiControllerInterface extends ApiControllerInterface
{
	public function register_routes();
	public function get_settings();
	public function update_settings( \WP_REST_Request $request );
}
