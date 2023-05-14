<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.wecodeart.com/
 * @since      1.0.0
 *
 * @package    WCA\EXT\CF7
 * @subpackage WCA\EXT\CF7\Activator
 */

namespace WCA\EXT\CF7;

use WeCodeArt\Admin\Notifications;
use WeCodeArt\Admin\Notifications\Notification;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    IAmBican
 * @subpackage WCA\EXT\CF7\Activator
 * @author     Bican Marian Valeriu <marianvaleriubican@gmail.com>
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$errors = self::if_compatible();

		if ( count( $errors ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( current( $errors ) );
		}

		if ( ! function_exists( 'wecodeart_option' ) ) {
			exit;
		}

		wecodeart_option( [
			'cf7_remove_js'		=> false,
			'cf7_remove_css'	=> true,
			'cf7_remove_autop'	=> false,
			'cf7_clean_assets'	=> true,
		] );
	}

	/**
	 * Check if compatible
	 *
	 * @since    1.0.0
	 */
	public static function if_compatible() {
		$checks = [
			'theme' 	=> function_exists( 'wecodeart' ),
			'plugin' 	=> defined( 'WPCF7_PLUGIN' )
		];

		$errors = [
			'theme' 	=> __( 'This extension requires WeCodeArt Framework (or a skin) installed and active.', 'wca-cf7' ),
			'plugin' 	=> __( 'This extension requires Contact Form 7 plugin installed and active.', 'wca-cf7' ),
		];

		$checks = array_filter( $checks, function( $value ) {
			return (boolean) $value === false;
		} );

		return array_intersect_key( $errors, $checks );	
	}
}
