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
	 * @version	1.0.4
	 *
	 * @return 	void
	 */
	public function options() {
		$options = wecodeart_option( 'contact_form_7' );
		
		if( get_prop( $options, [ 'remove_autop' ] ) ) {
			add_filter( 'wpcf7_autop_or_not', '__return_false', 20 );
		}

		if( get_prop( $options, [ 'feedback' ], '' ) !== '' ) {
			add_filter( 'wpcf7_form_response_output', '__return_empty_string', 20 );
		}
	}

	/**
	 * Assets
	 *
	 * @since 	1.0.0
	 * @version	1.0.7
	 *
	 * @return 	void
	 */
	public function assets() {
		// If no form don't bother loading our plugin assets.
		if( ! wecodeart_if( 'cf7_has_form' ) ) return;
		
		$options = wecodeart_option( 'contact_form_7' );
		
		$path = wecodeart_if( 'is_dev_mode' ) ? 'unminified' : 'minified';
		$name = wecodeart_if( 'is_dev_mode' ) ? 'frontend' : 'frontend.min';

		wecodeart( 'styles' )->Utilities->load( [ 'position-relative', 'd-block' ] );
		
		$feedback = strtolower( get_prop( $options, [ 'feedback' ], '' ) );
		if( $feedback !== '' ) {
			$positions = explode( ' ', get_prop( $options, [ 'feedback_position' ], '' ) );
			wecodeart( 'styles' )->Utilities->load( [ 'position-fixed', ...$positions, 'p-3', 'me-auto', 'd-flex', 'align-items-center' ] );
			wecodeart( 'styles' )->Components->load( [ $feedback ] );
			wp_enqueue_script( 'wecodeart-support-assets-' . $feedback );
		}

		wecodeart( 'assets' )->add_style( $this->make_handle(), [
			'inline'	=> 'file:' . sprintf( '%s/assets/%s/css/%s.css', untrailingslashit( WCA_CF7_EXT_DIR ), $path, $name ),
			'version'	=> $this->version,
		] );

		wecodeart( 'assets' )->add_script( $this->make_handle(), [
			'path' 		=> sprintf( '%s/assets/%s/js/%s.js', untrailingslashit( WCA_CF7_EXT_URL ), $path, $name ),
			'deps'		=> [ 'contact-form-7' ],
			'version'	=> $this->version,
			'locale'	=> [
				'feedback'	=> [
					'type' 		=> $feedback ? ucfirst( $feedback ) : '',
					'position' 	=> get_prop( $options, [ 'feedback_position' ], '' )
				],
				'labels'	=> [
					'close'		=> __( 'Close form modal', 'wca-cf7' ),
					'error'		=> __( 'Error', 'wca-cf7' ),
					'success' 	=> __( 'Success', 'wca-cf7' ),
				]
			]
		] );
	}

	/**
	 * Assets Cleanup
	 *
	 * @since 	1.0.7
	 * @version	1.0.7
	 *
	 * @return 	void
	 */
	public function cleanup() {
		$options = wecodeart_option( 'contact_form_7' );

		// If no form and control assets dont bother loading CF7 assets.
		if( ! wecodeart_if( 'cf7_has_form' ) && get_prop( $options, 'clean_assets' ) ) {
			// Plugin cleanup
			wp_deregister_style( 'contact-form-7' );
			wp_deregister_script( 'contact-form-7' );
			// Recaptcha cleanup
			wp_deregister_script( 'google-recaptcha' );
			wp_deregister_script( 'wpcf7-recaptcha' );
		}

		if( get_prop( $options, 'remove_css' ) ) {
			wp_deregister_style( 'contact-form-7' );
		}

		if( get_prop( $options, 'remove_js' ) ) {
			wp_deregister_script( 'contact-form-7' );
		}
	}

	/**
	 * Redirection
	 *
	 * @since 	1.0.4
	 * @version	1.0.4
	 *
	 * @param	object	$form
	 *
	 * @return 	void
	 */
	public function mail_sent( $form ) {
		$has_redirect 	= $form->pref( 'redirect_to' );
		
		if( ! $has_redirect ) return;

		add_filter( 'wpcf7_submission_result', static function( $result ) use ( $form, $has_redirect ) {
			$redirect 	= is_numeric( $has_redirect ) ? get_permalink( $has_redirect ) : $has_redirect;

			return wp_parse_args( [
				'redirect' => [
					'url' 	=> esc_url( $redirect ),
					'delay'	=> (int) $form->pref( 'redirect_delay' ),
					'blank'	=> (bool) $form->is_true( 'redirect_blank' )
				],
			], $result );
		} );
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
