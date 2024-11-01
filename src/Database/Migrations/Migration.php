<?php
/**
 * Base migration.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Database\Migrations;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPSitesMonitor\Interfaces\Database\Migrations\MigrationInterface;

/**
 * Base migration.
 *
 * @since 1.2.0
 */
class Migration implements MigrationInterface
{
	/**
	 * Initialize migration.
	 *
	 * @since 1.2.0
	 */
	public function migrate( string $table_name, string $sql ): void
	{
		$this->create( $table_name, $sql );
	}

	/**
	 * Create database table.
	 *
	 * @since 1.2.0
	 */
	protected function create( string $table_name, string $sql ): void
	{
		global $wpdb;

		if ( ! $wpdb) {
			return;
		}

		$charset_collate = $wpdb->get_charset_collate();
		$sql_query       = $sql . ' ' . $charset_collate;

		if ($wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name )
		) !== $table_name) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql_query );
		}
	}
}
