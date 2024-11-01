<?php

/**
 * Register life cycle service.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */
namespace WPSitesMonitor\Services;

/**
 * Exit when accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
use WPSitesMonitor\Interfaces\Services\LifeCycleServiceInterface;
use WPSitesMonitor\Database\Migrations\SitesTableMigration;
/**
 * Register life cycle service.
 *
 * @since 1.2.0
 */
class LifeCycleService extends Service implements LifeCycleServiceInterface {
    /**
     * @inheritDoc
     */
    public function register() : void {
        register_activation_hook( WP_SITES_MONITOR_FILE, array($this, 'install') );
        register_deactivation_hook( WP_SITES_MONITOR_FILE, array($this, 'deactivate') );
        register_uninstall_hook( WP_SITES_MONITOR_FILE, array(__CLASS__, 'uninstall') );
    }

    /**
     * Plugin install callback.
     *
     * @since 1.2.0
     */
    public function install() : void {
        SitesTableMigration::up();
    }

    /**
     * Plugin deactivation callback.
     *
     * @since 0.0.1
     */
    public function deactivate() : void {
        EventService::unschedule( 'wpsm_site_cron' );
    }

    /**
     * Plugin uninstall callback.
     *
     * @since 0.0.1
     */
    public static function uninstall() : void {
        // Remove options from `options` table.
        delete_option( 'wpsm_license_key' );
        // removed in version 1.0.1
        delete_option( 'wpsm_site_ap' );
        delete_option( 'wpsm_site_id' );
        delete_option( 'wpsm_site_interval' );
        delete_option( 'wpsm_site_url' );
        delete_option( 'wpsm_site_username' );
        delete_option( 'wpsm_type_of_website' );
        // Remove the custom `wpsm_sites` table.
        SitesTableMigration::down();
        sm_fs()->add_action( 'after_uninstall', 'sm_fs_uninstall_cleanup' );
    }

}
