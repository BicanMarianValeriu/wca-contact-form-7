<?php
/**
 * The frontend-specific functionality of the plugin.
 *
 * @link       https://www.wecodeart.com/
 * @since      1.0.0
 *
 * @package    WCA\EXT\CF7
 * @subpackage WCA\EXT\CF7\Frontend
 */

namespace WCA\EXT\CF7;

use WeCodeArt\Config\Traits\Asset;
use function WeCodeArt\Functions\get_prop;

/**
 * The frontend-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the frontend-specific stylesheet and JavaScript.
 *
 * @package    WCA\EXT\CF7
 * @subpackage WCA\EXT\CF7\Frontend
 * @author     Bican Marian Valeriu <marianvaleriubican@gmail.com>
 */
class Frontend {

	use Asset;

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
	 * @since	1.0.0
	 * @access	private
	 * @var		string    $version    The current version of this plugin.
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
	 * @param	string    $version		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $config ) {
		$this->plugin_name	= $plugin_name;
		$this->version 		= $version;
		$this->config 		= $config;
	}

	/**
	 * Assets
	 *
	 * @since 	1.0.0
	 * @version	1.0.0
	 *
	 * @return 	void
	 */
	public function options() {
		if( wecodeart_option( 'cf7_remove_autop' ) ) {
			add_filter( 'wpcf7_autop_or_not',	'__return_false' );
		}
	}

	/**
	 * Assets
	 *
	 * @since 	1.0.0
	 * @version	1.0.0
	 *
	 * @return 	void
	 */
	public function assets() {
		// If no form and control assets dont bother loading CF7 assets.
		if( ! wecodeart_if( 'cf7_has_form' ) && wecodeart_option( 'cf7_clean_assets' ) ) {
			wp_deregister_style( 'contact-form-7' );
			wp_deregister_script( 'contact-form-7' );
		}

		if( wecodeart_option( 'cf7_remove_css' ) ) {
			wp_deregister_style( 'contact-form-7' );
		}

		if( wecodeart_option( 'cf7_remove_js' ) ) {
			wp_deregister_script( 'contact-form-7' );
		}

		// If no form don't bother loading our plugin assets.
		if( ! wecodeart_if( 'cf7_has_form' ) ) return;
		
		$path = wecodeart_if( 'is_dev_mode' ) ? 'unminified' : 'minified';
		$name = wecodeart_if( 'is_dev_mode' ) ? 'frontend' : 'frontend.min';

		wecodeart( 'styles' )->Utilities->load( [ 'position-relative', 'd-block' ] );

		wp_enqueue_style(
			$this->make_handle(),
			wp_normalize_path( sprintf( '%s/assets/%s/css/%s.css', WCA_CF7_EXT_URL, $path, $name ) ),
			wecodeart( 'version' ),
		);

		wp_enqueue_script(
			$this->make_handle(),
			wp_normalize_path( sprintf( '%s/assets/%s/js/%s.js', WCA_CF7_EXT_URL, $path, $name ) ),
			[ 'contact-form-7' ],
			wecodeart( 'version' ),
			true
		);
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since	1.0.0
	 * @version 1.0.0
	 *
	 * @param 	array 	$classes Classes for the body element.
	 *
	 * @return 	array
	 */
	public function body_class( $classes ) {
		if( wecodeart_if( 'cf7_has_form' ) ) {
			$classes[] = 'wecodeart-cf7';
		}

		return $classes;
	}
	
	/**
	 * Filter CF7 Form class
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param	string	$class
	 *
	 * @return  string	$class
	 */
	public function form_class( $class ) {
		$classes = explode( ' ', $class );
		$classes[] = 'needs-validation';

		if( isset( $classes['invalid'] ) ) {
			$classes[] = 'was-validated';
		}

		return join( ' ', $classes );
	}
}
