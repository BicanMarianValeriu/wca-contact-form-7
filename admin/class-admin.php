<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.wecodeart.com/
 * @since      1.0.0
 *
 * @package    WCA\EXT\CF7
 * @subpackage WCA\EXT\CF7\Admin
 */

namespace WCA\EXT\CF7;

use WeCodeArt\Config\Traits\Asset;
use WeCodeArt\Admin\Request;
use WeCodeArt\Admin\Notifications;
use WeCodeArt\Admin\Notifications\Notification;
use function WeCodeArt\Functions\get_prop;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WCA\EXT\CF7
 * @subpackage WCA\EXT\CF7\Admin
 * @author     Bican Marian Valeriu <marianvaleriubican@gmail.com>
 */
class Admin {

	use Asset;

	const NOTICE_ID = 'wecodeart/plugin/cf7';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The config of this plugin.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @var		mixed    $config    The config of this plugin.
	 */
	private $config;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	string    $plugin_name	The name of this plugin.
	 * @param	string    $version    	The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $config ) {
		$this->plugin_name	= $plugin_name;
		$this->version 		= $version;
		$this->config 		= $config;
	}
    
    /**
	 * Check if active
	 *
	 * @since    1.0.0
	 */
	public function if_active() {
		$notification = new Notification(
			sprintf(
				'<h3 class="notice__heading" style="margin-bottom:0px">%1$s</h3>
				<div class="notice__content">
					<p>%2$s</p>
					<p><a href="%3$s" class="button button-primary">%4$s</a></p>
				</div>',
				esc_html__( 'Awesome, WCA: Contact Form 7 extension is activated!', 'wca-cf7' ),
				esc_html__( 'Go to Theme Options in order to setup your preferences.', 'wca-cf7' ),
				esc_url( admin_url( '/themes.php?page=wecodeart&tab=extensions#wca-cf7' ) ),
				esc_html__( 'Awesome, show me the options!', 'wca-cf7' )
			),
			[
				'id'			=> self::NOTICE_ID,
				'type'     		=> Notification::INFO,
				'priority' 		=> 1,
				'class'			=> 'notice is-dismissible',
				'capabilities' 	=> 'activate_plugins',
			]
		);

		if( get_user_option( self::NOTICE_ID ) === 'seen' ) {
			Notifications::get_instance()->remove_notification( $notification );
			set_transient( self::NOTICE_ID, true, WEEK_IN_SECONDS );
			return;
		}
		
		if( get_transient( self::NOTICE_ID ) === false ) {
			Notifications::get_instance()->add_notification( $notification );
		}
	}

	/**
	 * Admin Assets
	 *
	 * @since	1.0.0
	 * @version	1.0.0
	 */
	public function assets() {
		if( ! current_user_can( 'administrator' ) ) return;

		$path = wecodeart_if( 'is_dev_mode' ) ? 'unminified' : 'minified';
		$name = wecodeart_if( 'is_dev_mode' ) ? 'admin' : 'admin.min';
		$data = [
			'version' 		=> wecodeart( 'version' ),
			'dependencies'	=> [
				'wp-i18n',
				'wp-hooks',
				'wp-element',
				'wp-components',
			],
		];

		wp_register_script( 
			$this->make_handle(),
			wp_normalize_path( sprintf( '%s/assets/%s/js/%s.js', plugin_dir_url( __DIR__ ), $path, $name ) ),
			$data['dependencies'], 
			$data['version'], 
			true 
		);

		wp_enqueue_script( $this->make_handle() );
	}

	/**
	 * Update
	 *
	 * @since	1.0.0
	 * @version	1.0.0
	 */
	public function update( $transient ) {
		if ( ! is_object( $transient ) ) {
			return $transient;
		}

		if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ){
			$transient->response = [];
		}

		$cache_key  = 'wecodeart/transient/extension/cf7/update';
		$api_url	= 'https://api.github.com/repos/BicanMarianValeriu/wca-contact-form-7/releases/latest';
		
		if ( false === ( $response = get_transient($cache_key ) ) ) {
			$request	= new Request( $api_url, [] );
			$request->send( $request::METHOD_GET );
			$response = $request->get_response_body( true );
			set_transient( $cache_key, $response, 12 * HOUR_IN_SECONDS );
		}

		if( $response ) {
			$tag_name 	= get_prop( $response, 'tag_name', 'v1.0.1' );
			$version 	= str_replace( 'v', '', $tag_name );

			if( \version_compare( WCA_CF7_EXT_VER, $version, '<' ) ) {
				$transient->response[WCA_CF7_EXT_BASE] = (object) [
					'slug'         	=> 'wca-google-tools-extension',
					'plugin'		=> WCA_CF7_EXT_BASE,
					'new_version'	=> $version,
					'url'          	=> 'https://github.com/BicanMarianValeriu/wca-contact-form-7/releases/tag/' . $tag_name,
					'package'      	=> sprintf( 'https://github.com/BicanMarianValeriu/wca-contact-form-7/archive/refs/tags/%s.zip', $tag_name ),
					'upgrade_notice'=> '',
				];
			} else {
				unset( $transient->response[ WCA_CF7_EXT_BASE ] );
			}
		}

		return $transient;
	}

	/**
	 * Meta
	 *
	 * @since	1.0.0
	 * @version	1.0.0
	 */
	public function meta( $plugin_meta, $plugin_file ) {		
		// If we are not on the correct plugin, abort.
		if( WCA_CF7_EXT_BASE !== $plugin_file) {
			return $plugin_meta;
		}

		$review_link  = '<a href="https://wordpress.org/support/plugin/wca-contact-form-7/reviews/?filter=5" aria-label="' . esc_attr__( 'Review plugin on WordPress.org', 'wca-cf7' ) . '" target="_blank">';
		$review_link .= esc_html__( 'Leave a Review', 'wca-cf7' );
		$review_link .= '</a>';

		return array_merge( $plugin_meta, [
			'review' => $review_link,
		] );
	}

	/**
	 * Links
	 *
	 * @since	1.0.0
	 * @version	1.0.0
	 */
	public function links( $plugin_links, $plugin_file ) {
		// If we are not on the correct plugin, abort.
		if ( WCA_CF7_EXT_BASE !== $plugin_file ) {
			return $plugin_links;
		}

		$settings_link  = '<a href="' . esc_url( admin_url( '/themes.php?page=wecodeart&tab=extensions#wca-cf7' ) ) . '" aria-label="' . esc_attr__( 'Navigate to the extension settings.', 'wca-cf7' ) . '">';
		$settings_link .= esc_html__( 'Settings', 'wca-cf7' );
		$settings_link .= '</a>';

		array_unshift( $plugin_links, $settings_link );

		return $plugin_links;
	}
}
