<?php
/**
 * Site metrics trait.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Traits;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WP_Debug_Data;
use WP_Error;

/**
 * Site metrics trait.
 *
 * @since 1.2.0
 */
trait SiteMetricsTrait
{
	/**
	 * Get a brief summary of the site health stats.
	 *
	 * @since 0.0.1
	 */
	public function get_site_health_summary()
	{
		$site_health_summary = get_transient( 'health-check-site-status-result' );

		// If the transient returns false, handle it appropriately.
		if (false === $site_health_summary) {
			return '';
		}

		// Convert the result to a string if necessary.
		return is_string( $site_health_summary ) ? $site_health_summary : wp_json_encode( $site_health_summary );
	}

	/**
	 * Get the current WP version installed on the site.
	 *
	 * @since 0.0.1
	 */
	public function get_current_wp_version(): ?string
	{
		return get_bloginfo( 'version' );
	}

	/**
	 * Get a brief summary of the plugin update stats.
	 *
	 * @since 0.0.1
	 */
	public function get_plugin_updates()
	{
		if ( ! function_exists( 'wp_update_plugins' )) {
			require_once ABSPATH . WPINC . '/update.php';
		}

		wp_update_plugins();
		$update_plugins = get_site_transient( 'update_plugins' );

		$count_outdated = 0;
		$count_updated  = 0;

		if (isset( $update_plugins->no_update )) {
			foreach ($update_plugins->no_update as $no_update_plugin) {
				++$count_updated;
			}
		}

		if (isset( $update_plugins->response )) {
			foreach ($update_plugins->response as $update_plugin) {
				++$count_outdated;
			}
		}

		return wp_json_encode(
			array(
				'outdated' => $count_outdated,
				'updated'  => $count_updated,
			)
		);
	}

	/**
	 * Get information about plugins.
	 *
	 * @since 0.0.1
	 */
	public function get_plugin_info()
	{
		$plugin_updates = array();

		if ( ! function_exists( 'wp_update_plugins' )) {
			require_once ABSPATH . WPINC . '/update.php';
		}

		if ( ! function_exists( 'get_plugin_data' )) {
			require_once ABSPATH . WPINC . '/plugin.php';
		}

		wp_update_plugins();
		$update_plugins = get_site_transient( 'update_plugins' );

		if ($update_plugins && ! empty( $update_plugins->response )) {
			// plugins with updates
			foreach ($update_plugins->response as $plugin) {
				$plugin_file = trailingslashit( WP_PLUGIN_DIR ) . $plugin->plugin;

				if (file_exists( $plugin_file )) {
					$plugin_data = get_plugin_data( $plugin_file );
					$plugin_name = $plugin_data['Name'];

					$plugin_updates[] = array(
						'name'              => $plugin_name,
						'update_available'  => $plugin_data['Version'] !== $plugin->new_version,
						'version_current'   => $plugin_data['Version'],
						'version_available' => $plugin->new_version,
					);
				}
			}
		}

		if ($update_plugins && ! empty( $update_plugins->no_update )) {
			// plugins without updates
			foreach ($update_plugins->no_update as $plugin) {
				$plugin_file = trailingslashit( WP_PLUGIN_DIR ) . $plugin->plugin;

				if (file_exists( $plugin_file )) {
					$plugin_data = get_plugin_data( $plugin_file );
					$plugin_name = $plugin_data['Name'];

					$plugin_updates[] = array(
						'name'              => $plugin_name,
						'update_available'  => $plugin_data['Version'] !== $plugin->new_version,
						'version_current'   => $plugin_data['Version'],
						'version_available' => $plugin->new_version,
					);
				}
			}
		}

		return wp_json_encode( $plugin_updates );
	}

	/**
	 * Get information about directory sizes.
	 *
	 * @since 0.0.1
	 */
	public function get_directory_sizes()
	{
		if ( ! class_exists( 'WP_Debug_Data' )) {
			require_once ABSPATH . '/wp-admin/includes/class-wp-debug-data.php';
		}

		$sizes_data = WP_Debug_Data::get_sizes();
		$all_sizes  = array( 'raw' => 0 );

		foreach ($sizes_data as $name => $value) {
			$name = sanitize_text_field( $name );
			$data = array();

			if (isset( $value['size'] )) {
				if (is_string( $value['size'] )) {
					$data['size'] = sanitize_text_field( $value['size'] );
				} else {
					$data['size'] = (int) $value['size'];
				}
			}

			if (isset( $value['debug'] )) {
				if (is_string( $value['debug'] )) {
					$data['debug'] = sanitize_text_field( $value['debug'] );
				} else {
					$data['debug'] = (int) $value['debug'];
				}
			}

			if ( ! empty( $value['raw'] )) {
				$data['raw'] = (int) $value['raw'];
			}

			$all_sizes[ $name ] = $data;
		}

		if (isset( $all_sizes['total_size']['debug'] ) && 'not available' === $all_sizes['total_size']['debug']) {
			return new WP_Error( 'not_available', __( 'Directory sizes could not be returned.' ), array( 'status' => 500 ) );
		}

		return wp_json_encode( $all_sizes );
	}

	/**
	 * Get general information about the site.
	 *
	 * @since 1.1.1
	 */
	public function get_general_info()
	{
		$site_url   = get_site_url() ?? '';
		$user_count = count_users() ?? array();

		return wp_json_encode(
			array(
				'site_url'   => $site_url,
				'user_count' => $user_count,
			)
		);
	}
}
