<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.wecodeart.com/
 * @since             1.0.0
 * @package           WCA\EXT\CF7
 *
 * @wordpress-plugin
 * Plugin Name:       WCA: Contact Form 7
 * Plugin URI:        https://github.com/BicanMarianValeriu/wca-contact-form-7
 * Description:       WCA Contact Form 7 extension for WeCodeArt Framework theme - assets optimization and automatic forms styling.
 * Version:           1.0.7
 * Author:            Bican Marian Valeriu
 * Author URI:        https://www.wecodeart.com/about/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wca-cf7
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 */
namespace WCA\EXT\CF7;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WCA_CF7_EXT',	    __FILE__ );
define( 'WCA_CF7_EXT_VER', 	get_file_data( WCA_CF7_EXT, [ 'Version' ] )[0] ); // phpcs:ignore
define( 'WCA_CF7_EXT_DIR', 	plugin_dir_path( WCA_CF7_EXT ) );
define( 'WCA_CF7_EXT_URL', 	plugin_dir_url( WCA_CF7_EXT ) );
define( 'WCA_CF7_EXT_BASE',	plugin_basename( WCA_CF7_EXT ) );

require_once( WCA_CF7_EXT_DIR . '/includes/class-autoloader.php' );

new Autoloader( 'WCA\EXT\CF7', WCA_CF7_EXT_DIR . '/includes' );
new Autoloader( 'WCA\EXT\CF7', WCA_CF7_EXT_DIR . '/frontend' );
new Autoloader( 'WCA\EXT\CF7', WCA_CF7_EXT_DIR . '/frontend/modules' );
new Autoloader( 'WCA\EXT\CF7', WCA_CF7_EXT_DIR . '/admin' );

// Activation/Deactivation Hooks
register_activation_hook( WCA_CF7_EXT, [ Activator::class, 'run' ] );
register_deactivation_hook( WCA_CF7_EXT, [ Deactivator::class, 'run' ] );

/**
 * Hook the extension after WeCodeArt is Loaded
 */
add_action( 'wecodeart/theme/loaded', fn() => wecodeart( 'integrations' )->register( 'plugin/contact-form-7', __NAMESPACE__ ) );
