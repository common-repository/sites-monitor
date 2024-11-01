<?php
/**
 * Sorting trait.
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
 * Sorting trait.
 *
 * @since 1.2.0
 */
trait SortingTrait
{
	/**
	 * Define custom sorting functions.
	 *
	 * @since 1.0.0
	 */
	public function custom_sort($a, $b, $sort_by ) {
		switch ($sort_by) {
			case 'plugin_updates:ASC':
				return $this->compare_plugin_updates( $a, $b, true );
			case 'plugin_updates:DESC':
				return $this->compare_plugin_updates( $a, $b, false );
			case 'site:ASC':
				return $this->compare_post_title( $a, $b, true );
			case 'site:DESC':
				return $this->compare_post_title( $a, $b, false );
			case 'site_health:ASC':
				return $this->compare_site_health( $a, $b, true );
			case 'site_health:DESC':
				return $this->compare_site_health( $a, $b, false );
			case 'wp_version:ASC':
				return $this->compare_wp_version( $a, $b, true );
			case 'wp_version:DESC':
				return $this->compare_wp_version( $a, $b, false );
			default:
				return 0;
		}
	}

	/**
	 * Comparison function for plugin updates.
	 *
	 * @since 1.0.0
	 */
	public function compare_plugin_updates( $a, $b, $asc ) {
		$a_has_outdated = isset( $a['plugin_updates']['outdated'] );
		$b_has_outdated = isset( $b['plugin_updates']['outdated'] );

		if ($a_has_outdated && $b_has_outdated) {
			return ( $asc ? 1 : -1 ) * ( $b['plugin_updates']['outdated'] - $a['plugin_updates']['outdated'] );
		} elseif ($a_has_outdated) {
			return $asc ? -1 : 1;
		} elseif ($b_has_outdated) {
			return $asc ? 1 : -1;
		} else {
			return 0;
		}
	}

	/**
	 * Comparison function for post titles.
	 *
	 * @since 1.0.0
	 */
	public function compare_post_title( $a, $b, $asc ) {
		$a_has_title = isset( $a['post_title'] );
		$b_has_title = isset( $b['post_title'] );

		if ($a_has_title && $b_has_title) {
			return ( $asc ? 1 : -1 ) * strcmp( $a['post_title'], $b['post_title'] );
		} elseif ($a_has_title) {
			return $asc ? -1 : 1;
		} elseif ($b_has_title) {
			return $asc ? 1 : -1;
		} else {
			return 0;
		}
	}

	/**
	 * Comparison function for site health.
	 *
	 * @since 1.0.0
	 */
	public function compare_site_health( $a, $b, $asc ) {
		$a_has_health = isset( $a['site_health'] ) && is_array( $a['site_health'] );
		$b_has_health = isset( $b['site_health'] ) && is_array( $b['site_health'] );

		if ($a_has_health && $b_has_health) {
			$a_total = array_sum( $a['site_health'] );
			$b_total = array_sum( $b['site_health'] );
			return ( $asc ? 1 : -1 ) * ( $b_total - $a_total );
		} elseif ($a_has_health) {
			return $asc ? -1 : 1;
		} elseif ($b_has_health) {
			return $asc ? 1 : -1;
		} else {
			return 0;
		}
	}

	/**
	 * Comparison function for WP versions.
	 *
	 * @since 1.0.0
	 */
	public function compare_wp_version( $a, $b, $asc ) {
		$a_has_version = isset( $a['wp_version']['current'] );
		$b_has_version = isset( $b['wp_version']['current'] );

		if ($a_has_version && $b_has_version) {
			return ( $asc ? 1 : -1 ) * version_compare( $a['wp_version']['current'], $b['wp_version']['current'] );
		} elseif ($a_has_version) {
			return $asc ? -1 : 1;
		} elseif ($b_has_version) {
			return $asc ? 1 : -1;
		} else {
			return 0;
		}
	}
}
