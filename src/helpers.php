<?php
/**
 * Plugin helpers.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   0.0.1
 */

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Add prefix for the given string.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   0.0.1
 */
if ( ! function_exists( 'wp_sites_monitor_prefix' )) {
	function wp_sites_monitor_prefix( $name ): string
	{
		return 'wp-sites-monitor-' . $name;
	}
}

/**
 * Add prefix for the given string.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   0.0.1
 */
if ( ! function_exists( 'wp_sites_monitor_url' )) {
	function wp_sites_monitor_url( string $path ): string
	{
		return WP_SITES_MONITOR_PLUGIN_URL . $path;
	}
}

/**
 * Add prefix for the given string.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   0.0.1
 */
if ( ! function_exists( 'wp_sites_monitor_asset_url' )) {
	function wp_sites_monitor_asset_url( string $path ): string
	{
		return wp_sites_monitor_url( 'dist/' . $path );
	}
}

/**
 * Require a template file.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   0.0.1
 *
 * @throws Exception - if file not found throw exception
 * @throws Exception - if data is not array throw exception
 */
if ( ! function_exists( 'wp_sites_monitor_render_template' )) {
	function wp_sites_monitor_render_template( string $file_path, $data = array() )
	{
		$file = WP_SITES_MONITOR_DIR_PATH . 'src/' . $file_path;
		if ( ! file_exists( $file )) {
			throw new Exception( 'File not found' );
		}
		if ( ! is_array( $data )) {
			throw new Exception( 'Expected array as data' );
		}
        extract($data, EXTR_PREFIX_SAME, 'todo');	// @phpcs:ignore

		return require_once $file;
	}
}

/**
 * Require a view template file.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   0.0.1
 */
if ( ! function_exists( 'wp_sites_monitor_render_view_template' )) {
	/**
	 * @throws Exception - if file not found throw exception
	 * @throws Exception - if data is not array throw exception
	 */
	function wp_sites_monitor_render_view_template( string $file_path, $data = array() )
	{
		return wp_sites_monitor_render_template( 'Views/' . $file_path, $data );
	}
}
