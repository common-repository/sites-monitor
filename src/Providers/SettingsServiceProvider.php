<?php

/**
 * Register settings service provider.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */
namespace WPSitesMonitor\Providers;

/**
 * Exit when accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
use WPSitesMonitor\Controllers\SettingsController;
use WPSitesMonitor\Interfaces\Providers\SettingsServiceProviderInterface;
/**
 * Register settings service provider.
 *
 * @since 1.2.0
 */
class SettingsServiceProvider extends ServiceProvider implements SettingsServiceProviderInterface {
    /**
     * @var SettingsController;
     */
    private $controller;

    public function __construct( SettingsController $controller ) {
        $this->controller = $controller;
    }

    /**
     * @inheritDoc
     */
    public function register() : void {
        add_action( 'admin_menu', array($this, 'register_settings_page') );
        add_action( 'admin_init', array($this, 'register_settings_options') );
    }

    /**
     * Add a settings page to the wp-admin.
     *
     * @since 0.0.1
     */
    public function register_settings_page() : void {
        add_options_page(
            __( 'Sites Monitor', 'sites-monitor' ),
            __( 'Sites Monitor', 'sites-monitor' ),
            'manage_options',
            'wp-sites-monitor',
            array($this->controller, 'render_page')
        );
    }

    /**
     * Initialize the options for the settings page.
     *
     * @since 0.0.1
     */
    public function register_settings_options() : void {
        add_option( 'wpsm_site_url', '' );
        add_option( 'wpsm_site_username', '' );
        add_option( 'wpsm_site_ap', '' );
        add_option( 'wpsm_site_interval', '' );
        add_option( 'wpsm_type_of_website', array() );
        // This is the site ID on the remote monitor so that we know what site to update.
        add_option( 'wpsm_site_id', '' );
    }

}
