<?php
/**
 * Site service provider.
 *
 * @package WP_Sites_Monitor
 * @author  Verdant Studio
 * @since   1.2.0
 */

namespace WPSitesMonitor\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPSitesMonitor\Interfaces\Providers\SiteServiceProviderInterface;
use WPSitesMonitor\Traits\EncryptionTrait;
use WPSitesMonitor\Traits\SiteMetricsTrait;

/**
 * Site service provider.
 *
 * @since 1.2.0
 */
class SiteServiceProvider extends ServiceProvider implements SiteServiceProviderInterface
{
	use EncryptionTrait;
	use SiteMetricsTrait;

	/**
	 * The application password to the remote site.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private $site_ap;

	/**
	 * The site id given to us by the monitor.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private $site_id;

	/**
	 * The url to the remote site.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private $site_url;

	/**
	 * The username on the remote site.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private $site_username;

	public function __construct()
	{
		$this->services = array();

		$this->site_ap       = get_option( 'wpsm_site_ap' );
		$this->site_id       = get_option( 'wpsm_site_id' );
		$this->site_url      = get_option( 'wpsm_site_url' );
		$this->site_username = get_option( 'wpsm_site_username' );

		$this->initialize_encryption();
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.2.0
	 */
	protected function register_hooks(): void
	{
		add_action( 'wpsm_push_event', array( $this, 'push_init' ), 10, 2 );
	}

	/**
	 * Push the data to a monitor site.
	 *
	 * @since 0.0.1
	 */
	public function push_data( string $api_target, string $record_type, string $record_data ): void
	{
		$decrypt_site_ap = $this->decrypt( $this->site_ap );

		wp_remote_post(
			esc_url_raw( $api_target ),
			array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( "$this->site_username:$decrypt_site_ap" ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'record_data' => $record_data,
						'record_type' => $record_type,
					)
				),
			)
		);
	}

	/**
	 * Initialize the push to api, with record and data.
	 *
	 * @since 0.0.1
	 */
	public function push_init(): void
	{
		$general_info = $this->get_general_info();
		if (is_string( $general_info )) {
			$this->push_data(
				$this->site_url . '/wp-json/wpsm/v1/monitor/' . $this->site_id . '/general-info',
				'general-info',
				$general_info
			);
		}

		$site_health_summary = $this->get_site_health_summary();
		if (is_string( $site_health_summary )) {
			$this->push_data(
				$this->site_url . '/wp-json/wpsm/v1/monitor/' . $this->site_id . '/site-health',
				'site-health',
				$site_health_summary
			);
		}

		$current_wp_version = $this->get_current_wp_version();
		if (is_string( $current_wp_version )) {
			$this->push_data(
				$this->site_url . '/wp-json/wpsm/v1/monitor/' . $this->site_id . '/wp-version',
				'wp-version',
				$current_wp_version
			);
		}

		$plugin_updates = $this->get_plugin_updates();
		if (is_string( $plugin_updates )) {
			$this->push_data(
				$this->site_url . '/wp-json/wpsm/v1/monitor/' . $this->site_id . '/plugin-updates',
				'plugin-updates',
				$plugin_updates
			);
		}

		$plugin_info = $this->get_plugin_info();
		if (is_string( $plugin_info )) {
			$this->push_data(
				$this->site_url . '/wp-json/wpsm/v1/monitor/' . $this->site_id . '/plugin-info',
				'plugin-info',
				$plugin_info
			);
		}

		$directory_sizes = $this->get_directory_sizes();
		if ( ! is_wp_error( $directory_sizes ) && is_string( $directory_sizes ) ) {
			$this->push_data(
				$this->site_url . '/wp-json/wpsm/v1/monitor/' . $this->site_id . '/directory-sizes',
				'directory-sizes',
				$directory_sizes
			);
		}
	}
}
