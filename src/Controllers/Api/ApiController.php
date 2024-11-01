<?php
/**
 * Api controller.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Controllers\Api;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WP_Error;
use WPSitesMonitor\Interfaces\Controllers\Api\ApiControllerInterface;

/**
 * Api controller.
 *
 * @since 1.2.0
 */
class ApiController extends \WP_REST_Controller implements ApiControllerInterface
{
	/**
	 * Namespace to prefix REST calls.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	public $context = 'wpsm/';

	/**
	 * The current version of the REST calls.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	public $version = 'v1';

	/**
	 * Initialize events.
	 *
	 * @since 0.0.1
	 */
	public function register(): void
	{
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Check if user may access the endpoints.
	 *
	 * @since 0.0.1
	 */
	public function get_options_permission()
	{
		if ( ! current_user_can( 'manage_options' )) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to manage options.', 'sites-monitor' ), array( 'status' => 401 ) );
		}

		return true;
	}
}
