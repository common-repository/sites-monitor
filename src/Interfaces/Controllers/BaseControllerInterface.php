<?php
/**
 * Base Controller Interface.
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
 * Base Controller Interface.
 *
 * @since 1.2.0
 */
interface BaseControllerInterface
{
	public function register();
	public function render( $file_path, $data, $buffer );
}
