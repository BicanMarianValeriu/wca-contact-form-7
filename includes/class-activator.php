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
	public static function run() {
		$errors = self::if_compatible();

		if ( count( $errors ) ) {
			deactivate_plugins( WCA_CF7_EXT_BASE );
			wp_die( current( $errors ) );
		}

		if( ! wecodeart_option( 'contact_form_7' ) ) {
			wecodeart_option( [
				'contact_form_7' => [
					'remove_js'		=> false,
					'remove_css'	=> true,
					'remove_autop'	=> false,
					'clean_assets'	=> true,
					'feedback'		=> '',
				]
			] );
		}
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
