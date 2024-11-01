<?php

/**
 * Resource service interface.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */
namespace WPSitesMonitor\Interfaces\Services;

/**
 * Exit when accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Resource service interface.
 *
 * @since 1.2.0
 */
interface ResourceServiceInterface extends ServiceInterface {
    public function register_admin_scripts( string $hook_suffix );

    public function render_sites_monitor_block( $block_attributes );

    public function render_sites_monitor_list_block( $block_attributes );

    public function register_block_scripts();

}
