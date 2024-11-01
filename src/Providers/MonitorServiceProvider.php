<?php

/**
 * Monitor service provider.
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
use WPSitesMonitor\Interfaces\Providers\MonitorServiceProviderInterface;
/**
 * Monitor service provider.
 *
 * @since 1.2.0
 */
class MonitorServiceProvider extends ServiceProvider implements MonitorServiceProviderInterface {
    public function __construct() {
        $this->register_hooks();
    }

    /**
     * Register hooks.
     *
     * @since 1.2.0
     * @return void
     */
    public function register_hooks() {
        $type = get_option( 'wpsm_type_of_website' );
        if ( $type && in_array( 'is_monitor', $type, true ) ) {
            add_action( 'init', array($this, 'register_sites_cpt') );
            add_filter( 'manage_sites_posts_columns', array($this, 'filter_sites_columns') );
            add_action(
                'manage_sites_posts_custom_column',
                array($this, 'populate_sites_column'),
                10,
                2
            );
        }
        // Triggers when the post is permanently removed.
        add_action( 'before_delete_post', array($this, 'before_site_deleted') );
    }

    /**
     * Register a "Sites" custom post type.
     *
     * @since 0.0.1
     * @return void
     */
    public function register_sites_cpt() {
        register_post_type( 'sites', array(
            'has_archive'   => false,
            'labels'        => array(
                'name'               => __( 'Sites', 'sites-monitor' ),
                'singular_name'      => __( 'Site', 'sites-monitor' ),
                'add_new'            => __( 'New Site', 'sites-monitor' ),
                'add_new_item'       => __( 'Add New Site', 'sites-monitor' ),
                'edit_item'          => __( 'Edit Site', 'sites-monitor' ),
                'new_item'           => __( 'New Site', 'sites-monitor' ),
                'view_item'          => __( 'View Site', 'sites-monitor' ),
                'search_items'       => __( 'Search Sites', 'sites-monitor' ),
                'not_found'          => __( 'No Sites Found', 'sites-monitor' ),
                'not_found_in_trash' => __( 'No Sites found in Trash', 'sites-monitor' ),
            ),
            'menu_icon'     => 'dashicons-book',
            'public'        => true,
            'rewrite'       => array(
                'slug' => 'sites',
            ),
            'show_in_rest'  => true,
            'template'      => array(array('wp-sites-monitor/sites-monitor')),
            'template_lock' => 'all',
        ) );
    }

    /**
     * Change the admin columns for the custom post type.
     *
     * @since 0.0.1
     */
    public function filter_sites_columns( array $columns ) : array {
        return array(
            'cb'        => $columns['cb'],
            'title'     => __( 'Title' ),
            'last_sync' => __( 'Last sync', 'sites-monitor' ),
            'date'      => __( 'Date' ),
        );
    }

    /**
     * Populate the newly added admin columns.
     *
     * @since 0.0.1
     */
    public function populate_sites_column( $column, $post_id ) : void {
        global $wpdb;
        $site = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d", $post_id ) );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
        if ( 'last_sync' === $column ) {
            if ( $site ) {
                echo esc_html( $site->updated_at );
            } else {
                esc_html_e( 'Unknown', 'sites-monitor' );
            }
        }
    }

    /**
     * Cleanup the sites shadow table and remove any related events.
     * The transients do not need to be deleted because they expire themselves.
     *
     * @since 0.0.1
     */
    public function before_site_deleted( $post_id ) : void {
        global $post_type, $wpdb;
        if ( 'sites' !== $post_type ) {
            return;
        }
        $table_name = $wpdb->prefix . 'wpsm_sites';
        $wpdb->delete( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $table_name,
            array(
                'site_id' => $post_id,
            )
         );
    }

}
