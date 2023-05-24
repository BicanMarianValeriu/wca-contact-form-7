<?php
/**
 * The frontend-specific functionality of the plugin.
 *
 * @link       https://www.wecodeart.com/
 * @since      1.0.0
 *
 * @package    WCA\EXT\CF7
 * @subpackage WCA\EXT\CF7\Condition
 */

namespace WCA\EXT\CF7;

use WeCodeArt\Conditional\Interfaces\ConditionalInterface;

/**
 * Conditional that is only met when plugin is active.
 */
class Condition implements ConditionalInterface {

	/**
	 * @inheritdoc
	 */
	public function is_met() {
		global $post, $_wp_current_template_content;

		$load_scripts = false;

		if( is_object( $post ) ) {
			if( 
				has_shortcode( $post->post_content, 'contact-form' ) ||
				has_shortcode( $post->post_content, 'contact-form-7' ) ||
				has_block( 'contact-form-7/contact-form-selector', $post )
			) {
				$load_scripts = true;
			}
		}

		if( is_string( $_wp_current_template_content ) ) {
			if(
				has_shortcode( $_wp_current_template_content, 'contact-form' ) ||
				has_shortcode( $_wp_current_template_content, 'contact-form-7' ) ||
				has_block( 'contact-form-7/contact-form-selector', $_wp_current_template_content )
			) {
				$load_scripts = true;
			}
		}

		return $load_scripts;
	}
}