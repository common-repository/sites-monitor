<?php

/**
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 *
 * Plugin Name: Sites Monitor
 * Plugin URI: https://www.verdant.studio/plugins/sites-monitor
 * Description: Monitor your websites and be in charge of the data.
 * Version: 1.7.4
 * Author: Verdant Studio
 * Author URI: https://www.verdant.studio
 * License: GPLv2 or later
 * Text Domain: sites-monitor
 * Domain Path: /languages
 * Requires at least: 6.0
 *
 */
/**
 * Exit when accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( !function_exists( 'sm_fs' ) ) {
    /**
     * Create a helper function for easy SDK access.
     */
    function sm_fs() {
        global $sm_fs;
        if ( !isset( $sm_fs ) ) {
            // Include Freemius SDK.
            require_once __DIR__ . '/freemius/start.php';
            $sm_fs = fs_dynamic_init( array(
                'id'             => '13021',
                'slug'           => 'sites-monitor',
                'type'           => 'plugin',
                'public_key'     => 'pk_6c9a96ab61cc92cb757a02826cae4',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                    'slug'    => 'wp-sites-monitor',
                    'contact' => false,
                    'support' => false,
                    'parent'  => array(
                        'slug' => 'options-general.php',
                    ),
                ),
                'is_live'        => true,
            ) );
        }
        return $sm_fs;
    }

    // Init Freemius.
    sm_fs();
    // Signal that SDK was initiated.
    do_action( 'sm_fs_loaded' );
}
define( 'WP_SITES_MONITOR_VERSION', '1.7.4' );
define( 'WP_SITES_MONITOR_REQUIRED_WP_VERSION', '6.0' );
define( 'WP_SITES_MONITOR_FILE', __FILE__ );
define( 'WP_SITES_MONITOR_DIR_PATH', plugin_dir_path( WP_SITES_MONITOR_FILE ) );
define( 'WP_SITES_MONITOR_PLUGIN_URL', plugins_url( '/', WP_SITES_MONITOR_FILE ) );
// Require Composer autoloader if it exists.
if ( file_exists( __DIR__ . '/vendor-prefixed/autoload.php' ) ) {
    require_once __DIR__ . '/vendor-prefixed/autoload.php';
}
require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/src/Bootstrap.php';
$init = new WPSitesMonitor\Bootstrap();