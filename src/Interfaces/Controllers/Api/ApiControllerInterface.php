<?php
/**
 * Api controller interface.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Interfaces\Controllers\Api;

use WP_Error;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Api controller interface.
 *
 * @since 1.2.0
 */
interface ApiControllerInterface
{
	public function register();
	public function get_options_permission();
}
