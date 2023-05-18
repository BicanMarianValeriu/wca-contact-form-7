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
			'version' 		=> $this->version,
			'dependencies'	=> [
				'wp-i18n',
				'wp-hooks',
				'wp-element',
				'wp-components',
			],
		];

		wp_register_script( 
			$this->make_handle(),
			wp_normalize_path( sprintf( '%s/assets/%s/js/%s.js', WCA_CF7_EXT_URL, $path, $name ) ),
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
		if ( empty( $transient->checked ) ) {
			return $transient;
		}
		
		$latest 	= self::get_github_data();
		$tag_name 	= get_prop( $latest, 'tag_name', 'v1.0.0' );
		$version 	= str_replace( 'v', '', $tag_name );

		if ( 1 === version_compare( $version, $this->version ) ) {
			$response 				= new \stdClass;
			$response->new_version 	= $version;
			$response->slug 		= dirname( WCA_CF7_EXT_BASE );
			$response->url 			= 'https://github.com/BicanMarianValeriu/wca-contact-form-7';
			$response->package 		= sprintf( 'https://api.github.com/repos/BicanMarianValeriu/wca-contact-form-7/zipball/%s', $tag_name );
			// $response->package	= sprintf( 'https://github.com/BicanMarianValeriu/wca-contact-form-7/archive/refs/tags/%s.zip', $tag_name );
			// $response->upgrade_notice	= '';

			// If response is false, don't alter the transient
			if ( false !== $response ) {
				$transient->response[ WCA_CF7_EXT_BASE ] = $response;
			}
		}
		
		return $transient;
	}

	/**
	 * Upgrader/Updater
	 *
	 * @since 	1.0
	 * @param 	boolean $true       always true
	 * @param 	mixed   $hook_extra not used
	 * @param 	array   $result     the result of the move
	 *
	 * @return 	array 	$result the result of the move
	 */
	public function install( $true, $hook_extra, $result ) {
		global $wp_filesystem;

		// Move & Activate
		$proper_destination 	= WP_PLUGIN_DIR. '/'. dirname( WCA_CF7_EXT_BASE );
		$wp_filesystem->move( $result['destination'], $proper_destination );
		$result['destination'] 	= $proper_destination;
		$activate 				= activate_plugin( WP_PLUGIN_DIR. '/' . WCA_CF7_EXT_BASE );

		// Output the update message
		echo is_wp_error( $activate ) ?
			esc_html__( 'The plugin has been updated, but could not be reactivated. Please reactivate it manually.', 'wca-cf7' ) :
			esc_html__( 'Plugin reactivated successfully.', 'wca-cf7' );

		return $result;
	}

	/**
	 * Get Plugin info
	 *
	 * @since 	1.0
	 * @version	1.0.1
	 * @param 	bool    $false  always false
	 * @param 	string  $action the API function being performed
	 * @param 	object  $args   plugin arguments
	 *
	 * @return 	object $response the plugin info
	 */
	public function info( $false, $action, $response ) {
		// Check if this call API is for the right plugin
		if ( ! isset( $response->slug ) || $response->slug !== dirname( WCA_CF7_EXT_BASE ) ) {
			return false;
		}

		$plugin		= get_plugin_data( WP_PLUGIN_DIR . '/' . WCA_CF7_EXT_BASE );
		$latest 	= self::get_github_data();
		$tag_name 	= get_prop( $latest, 'tag_name', 'v1.0.0' );
		$published  = get_prop( $latest, 'published_at' );

		$response->slug 		= dirname( WCA_CF7_EXT_BASE );
		$response->plugin_name 	= $this->plugin_name;
		$response->version 		= str_replace( 'v', '', $tag_name );
		$response->author 		= $plugin['Author'];
		$response->homepage		= $plugin['PluginURI'];
		$response->requires 	= $plugin['RequiresWP'];
		$response->downloaded   = 0;
		$response->last_updated = date( 'Y-m-d', strtotime( $published ) );
		$response->sections		= [
			'description' 	=> $plugin['Description'],
			'changelog' 	=> '---soon---'
		];
		$response->download_link = sprintf( 'https://api.github.com/repos/BicanMarianValeriu/wca-contact-form-7/zipball/%s', $tag_name );

		return $response;
	}

	/**
	 * Meta
	 *
	 * @since	1.0.0
	 * @version	1.0.1
	 */
	public function meta( $plugin_meta, $plugin_file ) {		
		// If we are not on the correct plugin, abort.
		if( WCA_CF7_EXT_BASE !== $plugin_file) {
			return $plugin_meta;
		}

		$review_link  = '<a href="https://github.com/BicanMarianValeriu/wca-contact-form-7" aria-label="' . esc_attr__( 'Give it a star on GitHub', 'wca-cf7' ) . '" target="_blank">';
		$review_link .= esc_html__( 'Star on GitHub', 'wca-cf7' );
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

	/**
	 * Get Github Data
	 *
	 * @since	1.0.0
	 * @version	1.0.0
	 */
	public function get_github_data()  {
		$cache_key  = 'wecodeart/transient/extension/cf7/update';
		$api_url	= 'https://api.github.com/repos/BicanMarianValeriu/wca-contact-form-7/releases/latest';

		if ( false === ( $response = get_transient( $cache_key ) ) ) {
			$request	= new Request( $api_url, [] );
			$request->send( $request::METHOD_GET );
			$response = $request->get_response_body( true );
			set_transient( $cache_key, $response, 12 * HOUR_IN_SECONDS );
		}

		return $response;			
	}
}
