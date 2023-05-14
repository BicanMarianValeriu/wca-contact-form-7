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
				esc_html__( 'Go to theme extension options in order to setup your preferences.', 'wca-cf7' ),
				esc_url( admin_url( '/themes.php?page=wecodeart&tab=extensions' ) ),
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
}
