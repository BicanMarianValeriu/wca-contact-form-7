<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.wecodeart.com/
 * @since      1.0.0
 *
 * @package    WCA\EXT\CF7
 * @subpackage WCA\EXT\CF7\I18N
 */

namespace WCA\EXT\CF7;

use WeCodeArt\Singleton;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    WCA\EXT\CF7
 * @subpackage WCA\EXT\CF7\I18N
 * @author     Bican Marian Valeriu <marianvaleriubican@gmail.com>
 */
class I18N {

	use Singleton;
	
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wca-cf7', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}
}
