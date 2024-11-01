<?php
/**
 * Sites table migration.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   0.0.1
 */

namespace WPSitesMonitor\Database\Migrations;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPSitesMonitor\Interfaces\Database\Migrations\SitesTableMigrationInterface;
/**
 * Sites table migration.
 *
 * @since 0.0.1
 */
class SitesTableMigration extends Migration implements SitesTableMigrationInterface
{
	private static $instance;

	public static function up()
	{
		if ( ! isset( self::$instance )) {
			self::$instance = new self();
		}

		global $wpdb;

		$table_name = $wpdb->prefix . 'wpsm_sites';

		self::$instance->migrate(
			$table_name,
			"CREATE TABLE $table_name (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `site_id` int(11) NOT NULL,
                `record_type` varchar(255) NOT NULL,
                `record_data` longtext NOT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                PRIMARY KEY (`id`)
            )"
		);
	}

	public static function down()
	{
		if ( ! isset( self::$instance )) {
			self::$instance = new self();
		}

		global $wpdb;

		$table_name = $wpdb->prefix . 'wpsm_sites';

		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->prepare( "DROP TABLE IF EXISTS %s", $table_name ) // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
		);
	}
}
