<?php
/**
 * External trait.
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

/**
 * External trait.
 *
 * @since 1.2.0
 */
trait ExternalTrait
{
	/**
	 * Get the latest stable WP version.
	 *
	 * This call is made from the monitor so that a site does not have to call an extra api.
	 *
	 * @since 0.0.1
	 */
	public function get_latest_wp_version()
	{
		// Attempt to retrieve the cached version.
		$cached_version = get_transient( 'latest_wp_version' );

		if (false !== $cached_version) {
			// If the cached version exists, return it.
			return $cached_version;
		} else {
			// If the cached version doesn't exist, fetch and cache the latest version.
			$url      = 'https://api.wordpress.org/core/stable-check/1.0/';
			$response = wp_remote_get( $url );

			$latest_version = 'unknown'; // Default value if retrieval fails.

			if ( ! is_wp_error( $response )) {
				$response = wp_remote_retrieve_body( $response );
				$response = (array) json_decode( $response );

				if (is_array( $response )) {
					$response = array_keys( $response, 'latest', true );
					$response = array_pop( $response );

					$latest_version = $response;
				}
			}

			// Cache the result for a specific period (e.g., 1 hour)
			set_transient( 'latest_wp_version', $latest_version, 3600 );

			return $latest_version;
		}
	}
}
