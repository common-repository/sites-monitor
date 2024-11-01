<?php

/**
 * Site api controller.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */
namespace WPSitesMonitor\Controllers\Api;

/**
 * Exit when accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
use WPSitesMonitor\Interfaces\Controllers\Api\SiteApiControllerInterface;
use WPSitesMonitor\Services\EventService;
use WPSitesMonitor\Traits\EncryptionTrait;
/**
 * Site api controller.
 *
 * @since 1.2.0
 */
class SiteApiController extends ApiController implements SiteApiControllerInterface {
    use EncryptionTrait;
    /**
     * @inheritDoc
     */
    public function register() : void {
        parent::register();
        $this->initialize_encryption();
    }

    /**
     * Register api routes for sites of both types.
     *
     * @since 0.0.1
     */
    public function register_routes() : void {
        $context = $this->context . $this->version;
        register_rest_route( $context, '/settings', array(array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'get_settings'),
            'permission_callback' => array($this, 'get_options_permission'),
        ), array(
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => array($this, 'update_settings'),
            'permission_callback' => array($this, 'get_options_permission'),
        )) );
        register_rest_route( $context, '/cron-status', array(array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'get_cron_status'),
            'permission_callback' => array($this, 'get_options_permission'),
        )) );
    }

    /**
     * Get settings.
     *
     * @since 0.0.1
     */
    public function get_settings() : \WP_REST_Response {
        $site_url = get_option( 'wpsm_site_url' );
        $site_username = get_option( 'wpsm_site_username' );
        $site_ap = get_option( 'wpsm_site_ap' );
        $site_interval = get_option( 'wpsm_site_interval' );
        $type_of_website = get_option( 'wpsm_type_of_website' );
        $type_of_website = maybe_unserialize( $type_of_website );
        if ( !$type_of_website || !is_array( $type_of_website ) ) {
            $type_of_website = array();
        }
        if ( !empty( $site_ap ) ) {
            $site_ap = $this->decrypt( $site_ap );
        }
        $value = array(
            'site_url'        => $site_url,
            'site_username'   => $site_username,
            'site_ap'         => $site_ap,
            'site_interval'   => $site_interval,
            'type_of_website' => $type_of_website,
        );
        return new \WP_REST_Response(array(
            'success' => true,
            'value'   => $value,
        ), 200);
    }

    /**
     * Update or create a site in the `Sites CPT` on a monitor site.
     *
     * @since 0.0.1
     */
    private function update_or_create_site( $site_url, $site_username, $site_ap ) : \WP_REST_Response {
        $api_url = $site_url . '/wp-json/wp/v2/sites';
        $site_id = get_option( 'wpsm_site_id' );
        $site_interval = get_option( 'wpsm_site_interval' );
        // we have a record so verify if it really still exists on the monitor.
        if ( $site_id ) {
            $exists = wp_remote_get( esc_url_raw( $api_url . '/' . $site_id ), array(
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode( "{$site_username}:{$site_ap}" ),
                    'Content-Type'  => 'application/json',
                ),
            ) );
            $exists_code = wp_remote_retrieve_response_code( $exists );
            // if the site doesn't exist remove our site_id record of it else set the api url to the update endpoint.
            if ( 404 === $exists_code ) {
                update_option( 'wpsm_site_id', '' );
            } else {
                $api_url = $site_url . '/wp-json/wp/v2/sites/' . $site_id;
            }
        }
        $response = wp_remote_post( esc_url_raw( $api_url ), array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( "{$site_username}:{$site_ap}" ),
                'Content-Type'  => 'application/json',
            ),
            'body'    => wp_json_encode( array(
                'title'   => get_bloginfo( 'name' ) ?? __( 'Nameless', 'sites-monitor' ),
                'content' => '<!-- wp:wp-sites-monitor/sites-monitor /-->',
                'status'  => 'publish',
            ) ),
        ) );
        $code = wp_remote_retrieve_response_code( $response );
        $message = wp_remote_retrieve_response_message( $response );
        if ( is_wp_error( $response ) ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => $response->get_error_message(),
            ), 400);
        }
        if ( 200 !== $code && 201 !== $code ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => $message,
            ), $code);
        }
        // set the `wpsm_site_id` option, so we know which site to update.
        $data = json_decode( wp_remote_retrieve_body( $response ) );
        // an IP restricted redirect would make the response 200 but still invalid.
        if ( !is_object( $data ) ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'The response was invalid is the monitor site IP restricted?',
            ), 403);
        }
        update_option( 'wpsm_site_id', $data->id );
        EventService::reschedule( $site_interval, 'wpsm_site_cron' );
        return new \WP_REST_Response(array(
            'success' => true,
        ), 200);
    }

    /**
     * Update settings.
     *
     * @since 0.0.1
     */
    public function update_settings( \WP_REST_Request $request ) : \WP_REST_Response {
        $site_url = rtrim( $request->get_param( 'site_url' ), '/' );
        $site_username = $request->get_param( 'site_username' );
        $site_ap = $request->get_param( 'site_ap' );
        $site_interval = $request->get_param( 'site_interval' );
        $encrypt_site_ap = '';
        if ( !empty( $site_ap ) ) {
            $encrypt_site_ap = $this->encrypt( $site_ap );
        }
        $set = array();
        if ( is_array( $request->get_param( 'type_of_website' ) ) ) {
            foreach ( $request->get_param( 'type_of_website' ) as $index => $type ) {
                $set[$index] = $type;
            }
        }
        update_option( 'wpsm_site_url', $site_url );
        update_option( 'wpsm_site_username', $site_username );
        update_option( 'wpsm_site_ap', $encrypt_site_ap );
        update_option( 'wpsm_site_interval', $site_interval );
        update_option( 'wpsm_type_of_website', $set );
        // We want to monitor this site so go ahead and update or create it.
        if ( in_array( 'is_site', $set, true ) ) {
            return $this->update_or_create_site( $site_url, $site_username, $site_ap );
        }
        // If the site is a monitor (and not a site) unschedule any previous events.
        if ( in_array( 'is_monitor', $set, true ) ) {
            EventService::unschedule( 'wpsm_site_cron' );
        }
        // The site is just a monitor we don't need to update or create it.
        return new \WP_REST_Response(array(
            'success' => true,
        ), 200);
    }

    /**
     * Get cron status
     *
     * @since 1.4.0
     */
    public function get_cron_status() : \WP_REST_Response {
        $cron_event_name = 'wpsm_push_event';
        $next_run_timestamp = wp_next_scheduled( $cron_event_name, array('wpsm_site_cron') );
        $next_run_date = gmdate( 'Y-m-d H:i:s', $next_run_timestamp );
        if ( $next_run_timestamp ) {
            return new \WP_REST_Response(array(
                'success' => true,
                'value'   => array(
                    'name'     => $cron_event_name,
                    'next_run' => $next_run_date,
                ),
            ), 200);
        }
        return new \WP_REST_Response(array(
            'success' => false,
            'value'   => array(),
        ), 200);
    }

}
