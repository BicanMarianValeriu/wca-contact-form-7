<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.wecodeart.com/
 * @since      1.0.0
 *
 * @package    WCA\EXT\CF7
 * @subpackage WCA\EXT\CF7\Autoloader
 */

namespace WCA\EXT\CF7;

/**
 * Autoloader class
 *
 * @since 1.0.0
 */
class Autoloader {

	/**
	 * Vars.
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * 
	 * @var 	array 	$cached_paths	Cached FilePaths
	 * @var 	string	$_namespace		Theme Namespace
	 * @var 	string	$_separator		Namespace Separator
	 */
	private $cached_paths 	= [];
	private $namespace		= '';
	private $directory		= '';
	private $separator 		= '\\';

	/**
	 * Class constructor.
	 *
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function __construct( $namespace = 'WCA\EXT\CF7', $root = __DIR__ ) { 
		$this->namespace = $namespace;
		$this->directory = $root;

		spl_autoload_register( [ $this, 'autoload' ] );
	} 

	/**
	 * The WeCodeArt class autoloader.
	 * Finds the path to a class that we're requiring and includes the file.
	 *
	 * @access 	protected
	 * @since 	1.0.0
	 *
	 * @param 	string 	$class_name 	The name of the class we're trying to load.
	 */
	protected function autoload( $class_name ) {

		// Not a WeCodeArt file, early exit.
		if ( 0 !== stripos( $class_name, $this->namespace ) ) {
			return;
		}

		// Check if we've got it cached and ready.
		if ( isset( $this->cached_paths[ $class_name ] ) && file_exists( $this->cached_paths[ $class_name ] ) ) {
			include_once $this->cached_paths[ $class_name ];
			return;
		}

		// If no chach, run get_paths, set cache and include.
		$paths = $this->get_paths( $class_name );

		foreach ( $paths as $path ) {
			$path = wp_normalize_path( $path );
			if ( file_exists( $path ) ) {
				$this->cached_paths[ $class_name ] = $path;
				include_once $path;
				return;
			}
		}
	}

	/**
	 * Get an array of possible paths for the file.
	 *
	 * @access 	protected
	 * @since 	1.0.0
	 *
	 * @param 	string 	$class_name 	The name of the class we're trying to load.
	 *
	 * @return 	array
	 */
	protected function get_paths( $class_name ) {
		$paths = [];

		// Build the filename
		$exploded = explode( $this->separator, $class_name );
		$filename = 'class-' . strtolower( end( $exploded ) ) . '.php';

		// Check same directory
		$paths[] = $this->directory . DIRECTORY_SEPARATOR . $filename;

		// Look into sub-directory
		$substr   = str_replace( $this->namespace . $this->separator, '', $class_name );
		$exploded = explode( $this->separator, $substr );
		$levels   = count( $exploded );

		// Build the filepath
		$previous_path = '';
		for ( $i = 0; $i < $levels; $i++ ) {
			$paths[]        = $this->directory . DIRECTORY_SEPARATOR . $previous_path . strtolower( $exploded[ $i ] ) . DIRECTORY_SEPARATOR . $filename;
			$previous_path .= strtolower( $exploded[ $i ] ) . DIRECTORY_SEPARATOR;
		}

		// Return Paths
		return $paths;
	}
}
