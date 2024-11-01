<?php

/**
 * Monitor api controller.
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
use WP_REST_Response;
use WPSitesMonitor\Interfaces\Controllers\Api\MonitorApiControllerInterface;
use WPSitesMonitor\Traits\ExternalTrait;
use WPSitesMonitor\Traits\SortingTrait;
/**
 * Monitor api controller.
 *
 * @since 1.2.0
 */
class MonitorApiController extends ApiController implements MonitorApiControllerInterface {
    use ExternalTrait;
    use SortingTrait;
    /**
     * Register api routes for sites of both types.
     *
     * @since 0.0.1
     */
    public function register_routes() : void {
        $context = $this->context . $this->version;
        $type_of_website = get_option( 'wpsm_type_of_website' );
        $type_of_website = maybe_unserialize( $type_of_website );
        // Enable routes based on type of site.
        if ( is_array( $type_of_website ) && in_array( 'is_monitor', $type_of_website, true ) ) {
            $this->register_monitor_routes( $context . '/monitor' );
        }
    }

    /**
     * Register api routes for sites of type monitor.
     *
     * @since 0.0.1
     */
    public function register_monitor_routes( string $context ) : void {
        register_rest_route( $context, '/(?P<site_id>(.*)+)/verify', array(array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'monitor_verify_records_exist'),
            'permission_callback' => '__return_true',
        )) );
        register_rest_route( $context, '/sites', array(array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'monitor_get_sites'),
            'permission_callback' => '__return_true',
        )) );
        register_rest_route( $context, '/(?P<site_id>(.*)+)/site-health', array(array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'monitor_add_site_health'),
            'permission_callback' => array($this, 'get_options_permission'),
        ), array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'monitor_get_site_health'),
            'permission_callback' => '__return_true',
        ), array(
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => array($this, 'monitor_update_site_health'),
            'permission_callback' => array($this, 'get_options_permission'),
        )) );
        register_rest_route( $context, '/(?P<site_id>(.*)+)/wp-version', array(array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'monitor_add_wp_version'),
            'permission_callback' => array($this, 'get_options_permission'),
        ), array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'monitor_get_wp_version'),
            'permission_callback' => '__return_true',
        ), array(
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => array($this, 'monitor_update_wp_version'),
            'permission_callback' => array($this, 'get_options_permission'),
        )) );
        register_rest_route( $context, '/(?P<site_id>(.*)+)/plugin-updates', array(array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'monitor_add_plugin_updates'),
            'permission_callback' => array($this, 'get_options_permission'),
        ), array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'monitor_get_plugin_updates'),
            'permission_callback' => '__return_true',
        ), array(
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => array($this, 'monitor_update_plugin_updates'),
            'permission_callback' => array($this, 'get_options_permission'),
        )) );
        register_rest_route( $context, '/(?P<site_id>(.*)+)/plugin-info', array(array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'monitor_add_plugin_info'),
            'permission_callback' => array($this, 'get_options_permission'),
        ), array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'monitor_get_plugin_info'),
            'permission_callback' => '__return_true',
        ), array(
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => array($this, 'monitor_update_plugin_info'),
            'permission_callback' => array($this, 'get_options_permission'),
        )) );
        register_rest_route( $context, '/(?P<site_id>(.*)+)/directory-sizes', array(array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'monitor_add_directory_sizes'),
            'permission_callback' => array($this, 'get_options_permission'),
        ), array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'monitor_get_directory_sizes'),
            'permission_callback' => '__return_true',
        ), array(
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => array($this, 'monitor_update_directory_sizes'),
            'permission_callback' => array($this, 'get_options_permission'),
        )) );
        register_rest_route( $context, '/(?P<site_id>(.*)+)/general-info', array(array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'monitor_add_general_info'),
            'permission_callback' => array($this, 'get_options_permission'),
        ), array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array($this, 'monitor_get_general_info'),
            'permission_callback' => '__return_true',
        ), array(
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => array($this, 'monitor_update_general_info'),
            'permission_callback' => array($this, 'get_options_permission'),
        )) );
    }

    /**
     * Verify if there are at least some records for the site in the database.
     *
     * @since 0.0.1
     */
    public function monitor_verify_records_exist( \WP_REST_Request $request ) : WP_REST_Response {
        global $wpdb;
        $site_id = $request->get_param( 'site_id' );
        $site = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d", $site_id )
         );
        if ( !$site ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Records for this site not found',
            ), 404);
        }
        return new \WP_REST_Response(array(
            'success' => true,
            'value'   => 'Records for this site exist',
        ), 200);
    }

    /**
     * Add site health.
     *
     * @since 0.0.1
     */
    public function monitor_add_site_health( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $site_exists = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
         );
        // If the record already exists update instead.
        if ( $site_exists ) {
            return $this->monitor_update_site_health( $request );
        }
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'created_at'  => gmdate( 'Y-m-d H:i:s' ),
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->insert( $wpdb->prefix . 'wpsm_sites', $data );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch site health',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to create site health',
            ), 401);
        }
    }

    /**
     * Get site health.
     *
     * @since 0.0.1
     */
    public function monitor_get_site_health( \WP_REST_Request $request ) : WP_REST_Response {
        global $wpdb;
        $site_id = $request->get_param( 'site_id' );
        $site = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, 'site-health' )
         );
        if ( !$site ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Cannot find site health',
            ), 404);
        }
        return new \WP_REST_Response(array(
            'success' => true,
            'value'   => $site->record_data,
        ), 200);
    }

    /**
     * Update site health.
     *
     * @since 0.0.1
     */
    public function monitor_update_site_health( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->update( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prefix . 'wpsm_sites',
            $data,
            array(
                'site_id'     => $site_id,
                'record_type' => $record_type,
            )
         );
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch site health',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to update site health',
            ), 401);
        }
    }

    /**
     * Add WP version.
     *
     * @since 0.0.1
     */
    public function monitor_add_wp_version( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $site_exists = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
         );
        // If the record already exists update instead.
        if ( $site_exists ) {
            return $this->monitor_update_wp_version( $request );
        }
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'created_at'  => gmdate( 'Y-m-d H:i:s' ),
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->insert( $wpdb->prefix . 'wpsm_sites', $data );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch WP version',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to create WP version',
            ), 401);
        }
    }

    /**
     * Get WP version.
     *
     * @since 0.0.1
     */
    public function monitor_get_wp_version( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $site_id = $request->get_param( 'site_id' );
        $site = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, 'wp-version' )
         );
        $latest = $this->get_latest_wp_version();
        if ( !$site ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Cannot find WP version',
            ), 404);
        }
        return new \WP_REST_Response(array(
            'success' => true,
            'value'   => array(
                'current' => $site->record_data,
                'latest'  => $latest,
            ),
        ), 200);
    }

    /**
     * Update WP version.
     *
     * @since 0.0.1
     */
    public function monitor_update_wp_version( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->update( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prefix . 'wpsm_sites',
            $data,
            array(
                'site_id'     => $site_id,
                'record_type' => $record_type,
            )
         );
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch WP version',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to update WP version',
            ), 401);
        }
    }

    /**
     * Add plugin updates.
     *
     * @since 0.0.1
     */
    public function monitor_add_plugin_updates( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $site_exists = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
         );
        // If the record already exists update instead.
        if ( $site_exists ) {
            return $this->monitor_update_plugin_updates( $request );
        }
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'created_at'  => gmdate( 'Y-m-d H:i:s' ),
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->insert( $wpdb->prefix . 'wpsm_sites', $data );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch plugin updates',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to create plugin updates',
            ), 401);
        }
    }

    /**
     * Get plugin updates.
     *
     * @since 0.0.1
     */
    public function monitor_get_plugin_updates( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $site_id = $request->get_param( 'site_id' );
        $site = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, 'plugin-updates' )
         );
        if ( !$site ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Cannot find plugin updates',
            ), 404);
        }
        return new \WP_REST_Response(array(
            'success' => true,
            'value'   => $site->record_data,
        ), 200);
    }

    /**
     * Update plugin updates.
     *
     * @since 0.0.1
     */
    public function monitor_update_plugin_updates( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->update( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prefix . 'wpsm_sites',
            $data,
            array(
                'site_id'     => $site_id,
                'record_type' => $record_type,
            )
         );
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch plugin updates',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to update plugin updates',
            ), 401);
        }
    }

    /**
     * Add plugin info.
     *
     * @since 0.0.1
     */
    public function monitor_add_plugin_info( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $site_exists = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
         );
        // If the record already exists update instead.
        if ( $site_exists ) {
            return $this->monitor_update_plugin_info( $request );
        }
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'created_at'  => gmdate( 'Y-m-d H:i:s' ),
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->insert( $wpdb->prefix . 'wpsm_sites', $data );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch plugin info',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to create plugin info',
            ), 401);
        }
    }

    /**
     * Get plugin info.
     *
     * @since 0.0.1
     */
    public function monitor_get_plugin_info( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $site_id = $request->get_param( 'site_id' );
        $site = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, 'plugin-info' )
         );
        if ( !$site ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'No plugin info found',
            ), 404);
        }
        return new \WP_REST_Response(array(
            'success' => true,
            'value'   => $site->record_data,
        ), 200);
    }

    /**
     * Update plugin info.
     *
     * @since 0.0.1
     */
    public function monitor_update_plugin_info( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->update( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prefix . 'wpsm_sites',
            $data,
            array(
                'site_id'     => $site_id,
                'record_type' => $record_type,
            )
         );
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch plugin info',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to update plugin info',
            ), 401);
        }
    }

    /**
     * Add directory sizes.
     *
     * @since 0.0.1
     */
    public function monitor_add_directory_sizes( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $site_exists = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
         );
        // If the record already exists update instead.
        if ( $site_exists ) {
            return $this->monitor_update_directory_sizes( $request );
        }
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'created_at'  => gmdate( 'Y-m-d H:i:s' ),
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->insert( $wpdb->prefix . 'wpsm_sites', $data );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch directory sizes',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to create directory sizes',
            ), 401);
        }
    }

    /**
     * Get directory sizes.
     *
     * @since 0.0.1
     */
    public function monitor_get_directory_sizes( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $site_id = $request->get_param( 'site_id' );
        $site = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, 'directory-sizes' )
         );
        if ( !$site ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'No directory sizes found',
            ), 404);
        }
        return new \WP_REST_Response(array(
            'success' => true,
            'value'   => $site->record_data,
        ), 200);
    }

    /**
     * Update directory sizes.
     *
     * @since 0.0.1
     */
    public function monitor_update_directory_sizes( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->update( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prefix . 'wpsm_sites',
            $data,
            array(
                'site_id'     => $site_id,
                'record_type' => $record_type,
            )
         );
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch directory sizes',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to update directory sizes',
            ), 401);
        }
    }

    /**
     * Add general info.
     *
     * @since 1.1.1
     */
    public function monitor_add_general_info( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $site_exists = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
         );
        // If the record already exists update instead.
        if ( $site_exists ) {
            return $this->monitor_update_general_info( $request );
        }
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'created_at'  => gmdate( 'Y-m-d H:i:s' ),
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->insert( $wpdb->prefix . 'wpsm_sites', $data );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch general info',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to create general info',
            ), 401);
        }
    }

    /**
     * Get general info
     *
     * @since 1.1.1
     */
    public function monitor_get_general_info( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $site_id = $request->get_param( 'site_id' );
        $site = $wpdb->get_row( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, 'general-info' )
         );
        if ( !$site ) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Cannot find general information',
            ), 404);
        }
        return new \WP_REST_Response(array(
            'success' => true,
            'value'   => $site->record_data,
        ), 200);
    }

    /**
     * Update general info.
     *
     * @since 1.1.1
     */
    public function monitor_update_general_info( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $record_data = $request->get_param( 'record_data' ) ?? '';
        $record_type = $request->get_param( 'record_type' ) ?? '';
        $site_id = $request->get_param( 'site_id' );
        $data = array(
            'site_id'     => $site_id,
            'record_type' => $record_type,
            'record_data' => $record_data,
            'updated_at'  => gmdate( 'Y-m-d H:i:s' ),
        );
        $result = $wpdb->update( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prefix . 'wpsm_sites',
            $data,
            array(
                'site_id'     => $site_id,
                'record_type' => $record_type,
            )
         );
        if ( $result ) {
            $site = $wpdb->get_row( 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpsm_sites WHERE `site_id` = %d AND `record_type` = %s", $site_id, $record_type )
             );
            if ( $site ) {
                return new \WP_REST_Response(array(
                    'success' => true,
                    'value'   => $site,
                ), 200);
            } else {
                return new \WP_REST_Response(array(
                    'success' => false,
                    'message' => 'Unable to fetch general info',
                ), 401);
            }
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'Unable to update general info',
            ), 401);
        }
    }

    /**
     * Get sites.
     *
     * @since 0.0.9
     */
    public function monitor_get_sites( \WP_REST_Request $request ) : \WP_REST_Response {
        global $wpdb;
        $page = ( $request->get_param( 'search' ) ? 1 : $request->get_param( 'page' ) ?? 1 );
        $per_page = ( $request->get_param( 'search' ) ? 9999 : $request->get_param( 'per_page' ) ?? 10 );
        $offset = ($page - 1) * $per_page;
        $search_term = $request->get_param( 'search' ) ?? '';
        $sort_by = $request->get_param( 'sort_by' ) ?? '';
        // Fetch sites and related data in a single query.
        $query = "\n            SELECT p.ID, p.post_title,\n                (SELECT record_data FROM {$wpdb->prefix}wpsm_sites WHERE site_id = p.ID AND record_type = 'site-health') AS site_health,\n                (SELECT record_data FROM {$wpdb->prefix}wpsm_sites WHERE site_id = p.ID AND record_type = 'plugin-updates') AS plugin_updates,\n                (SELECT record_data FROM {$wpdb->prefix}wpsm_sites WHERE site_id = p.ID AND record_type = 'wp-version') AS wp_version\n            FROM {$wpdb->prefix}posts AS p\n            WHERE p.post_type = %s\n            AND p.post_status = %s\n        ";
        $results = $wpdb->get_results( 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.DirectQuery
            $wpdb->prepare( $query, 'sites', 'publish' )
         );
        $sites = array();
        if ( !empty( $results ) ) {
            foreach ( $results as $result ) {
                $site = array(
                    'ID'             => $result->ID,
                    'post_title'     => $result->post_title,
                    'post_link'      => get_permalink( $result->ID ),
                    'site_health'    => json_decode( $result->site_health, true ),
                    'plugin_updates' => json_decode( $result->plugin_updates, true ),
                );
                // Get the WP versions.
                $wp_version_current = $result->wp_version;
                $wp_version_latest = $this->get_latest_wp_version();
                if ( $wp_version_current && $wp_version_latest ) {
                    $site['wp_version'] = array(
                        'current' => $wp_version_current,
                        'latest'  => $wp_version_latest,
                    );
                }
                $sites[] = $site;
            }
            // Apply sorting.
            if ( isset( $sort_by ) ) {
                usort( $sites, function ( $a, $b ) use($sort_by) {
                    return $this->custom_sort( $a, $b, $sort_by );
                } );
            }
            // Apply pagination to the sorted list.
            $paged_sites = array_slice( $sites, $offset, $per_page );
            // Create a response object and set headers.
            $response = new \WP_REST_Response($paged_sites, 200);
            $response->header( 'X-WP-Total', count( $sites ) );
            $response->header( 'X-WP-TotalPages', ceil( count( $sites ) / $per_page ) );
            return $response;
        } else {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => 'No sites found',
            ), 404);
        }
    }

}
