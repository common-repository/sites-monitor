<?php

/**
 * Register resource service.
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
use Error;
use WPSitesMonitor\Interfaces\Services\ResourceServiceInterface;
/**
 * Register resource service.
 *
 * @since 1.2.0
 */
class ResourceService extends Service implements ResourceServiceInterface {
    /**
     * @inheritDoc
     */
    public function register() : void {
        $type_of_website = get_option( 'wpsm_type_of_website' );
        $type_of_website = maybe_unserialize( $type_of_website );
        if ( is_array( $type_of_website ) && in_array( 'is_monitor', $type_of_website, true ) ) {
            // Enqueue the blocks.
            add_action( 'init', array($this, 'register_block_scripts') );
        }
        // Enqueue the admin scripts: `admin.js`.
        add_action( 'admin_enqueue_scripts', array($this, 'register_admin_scripts') );
    }

    /**
     * Register the admin scripts.
     *
     * @since 0.0.1
     *
     * @throws Error Run npm build;
     */
    public function register_admin_scripts( string $hook_suffix ) : void {
        // only load the scripts on the plugin settings page
        if ( 'settings_page_wp-sites-monitor' !== $hook_suffix ) {
            return;
        }
        $script_asset_path = WP_SITES_MONITOR_DIR_PATH . 'dist/admin.asset.php';
        if ( !file_exists( $script_asset_path ) ) {
            throw new Error('You need to run `npm run watch` or `npm run build` to be able to use this plugin first.');
        }
        $script_asset = (require $script_asset_path);
        wp_enqueue_style(
            wp_sites_monitor_prefix( 'admin-css' ),
            wp_sites_monitor_asset_url( 'admin.css' ),
            array('wp-components'),
            $script_asset['version']
        );
        wp_register_script(
            wp_sites_monitor_prefix( 'admin-js' ),
            wp_sites_monitor_asset_url( 'admin.js' ),
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );
        wp_localize_script( wp_sites_monitor_prefix( 'admin-js' ), 'wpsmSettings', array(
            'nonce'          => wp_create_nonce( 'wp_rest' ),
            'wpsm_ajax_base' => esc_url_raw( rest_url( 'wpsm/v1' ) ),
        ) );
        wp_enqueue_script( wp_sites_monitor_prefix( 'admin-js' ) );
    }

    /**
     * Render the sites monitor div on the frontend.
     *
     * @since 0.0.1
     */
    public function render_sites_monitor_block( $block_attributes ) : ?string {
        $id = get_the_ID();
        // Encode the attributes as a JSON string using wp_json_encode.
        $data_attributes = wp_json_encode( $block_attributes );
        if ( $id ) {
            return '<div class="wp-sites-monitor-front" data-attributes="' . esc_attr( $data_attributes ) . '" data-id="' . esc_attr( $id ) . '"></div>';
        }
        return null;
    }

    /**
     * Render the sites monitor list div on the frontend.
     *
     * @since 0.0.1
     */
    public function render_sites_monitor_list_block( $block_attributes ) : string {
        // Encode the attributes as a JSON string using wp_json_encode.
        $data_attributes = wp_json_encode( $block_attributes );
        return '<div class="wp-sites-monitor-list-front" data-attributes="' . esc_attr( $data_attributes ) . '"></div>';
    }

    /**
     * Register blocks.
     *
     * @since 0.0.1
     */
    public function register_block_scripts() : void {
        // Block editor is not available.
        if ( !function_exists( 'register_block_type' ) ) {
            return;
        }
        register_block_type( WP_SITES_MONITOR_DIR_PATH . 'dist/sites-monitor/block.json', array(
            'render_callback' => array($this, 'render_sites_monitor_block'),
        ) );
        register_block_type( WP_SITES_MONITOR_DIR_PATH . 'dist/sites-monitor-list/block.json', array(
            'render_callback' => array($this, 'render_sites_monitor_list_block'),
        ) );
        $localize = array(
            'isPremium'      => sm_fs()->can_use_premium_code(),
            'nonce'          => wp_create_nonce( 'wp_rest' ),
            'wpsm_ajax_base' => esc_url_raw( rest_url( 'wpsm/v1' ) ),
        );
        // Pass the wpsmSettings variable to sites-monitor-list editor script.
        wp_localize_script( 'wp-sites-monitor-sites-monitor-list-editor-script', 'wpsmSettings', $localize );
        // Pass the wpsmSettings variable to sites-monitor-list view script.
        wp_localize_script( 'wp-sites-monitor-sites-monitor-list-view-script', 'wpsmSettings', $localize );
        // Pass the wpsmSettings variable to sites-monitor view script.
        wp_localize_script( 'wp-sites-monitor-sites-monitor-view-script', 'wpsmSettings', $localize );
    }

}
