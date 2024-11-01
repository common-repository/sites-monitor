<?php
/**
 * Migration interface.
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
 * Migration interface.
 *
 * @since 1.2.0
 */
interface MigrationInterface
{
	public function migrate( string $table_name, string $sql );
}
