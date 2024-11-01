<?php
/**
 * Sites table migration interface.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Interfaces\Database\Migrations;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Sites table migration interface.
 *
 * @since 1.2.0
 */
interface SitesTableMigrationInterface extends MigrationInterface
{
	public static function up();
	public static function down();
}
