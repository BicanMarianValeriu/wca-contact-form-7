<?php
/**
 * The frontend-specific functionality of the plugin.
 *
 * @link       https://www.wecodeart.com/
 * @since      1.0.0
 *
 * @package    WCA\EXT\CF7\Frontend
 * @subpackage WCA\EXT\CF7\Frontend\Modules
 */

namespace WCA\EXT\CF7\Frontend;

use WeCodeArt\Singleton;

/**
 * Modules
 */
class Modules implements \ArrayAccess {

	use Singleton;

	/**
	 * The registered modules.
	 *
	 * @var Modules[]
	 */
	protected $items = [];

	/**
	 * Send to Constructor
	 */
	public function init() {
		$this->register( 'basic',       Modules\Basic::class    );
		$this->register( 'textarea',	Modules\TextArea::class	);
		$this->register( 'checkbox',	Modules\Checkbox::class	);
		$this->register( 'password',	Modules\Password::class	);
		$this->register( 'select',		Modules\Select::class	);
		$this->register( 'number',		Modules\Number::class	);
		$this->register( 'file',		Modules\File::class		);
		$this->register( 'date',       	Modules\Date::class		);
		$this->register( 'color',       Modules\Color::class    );
		$this->register( 'quiz',       	Modules\Quiz::class		);
		$this->register( 'submit',		Modules\Submit::class	);
		$this->register( 'acceptance',	Modules\Acceptance::class );
	}

	/**
	 * Loads all registered integrations if their conditionals are met.
	 *
	 * @return void
	 */
	public function load() {
		foreach ( $this->items as $class ) $class::get_instance()->register();
	}

	/**
     * Set a given module value.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     *
     * @return void
     */
    public function register( $key, $value = null ) {
        $this->set( $key, $value );
	}
	
    /**
     * Set a given module value.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     *
     * @return void
     */
    public function set( $key, $value = null ) {
        $keys = is_array( $key ) ? $key : [ $key => $value ];

        foreach ( $keys as $key => $value ) {
            $this->items[$key] = $value;
        }
	}

	/**
     * Determine if the given module value exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function has( $key ) {
        return isset( $this->items[$key] );
    }

    /**
     * Get the specified module value.
     *
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    public function get( $key, $default = null ) {
        if ( ! isset( $this->items[$key] ) ) {
            return $default;
        }

        return $this->items[$key];
    }
	
	/**
     * Removes module from the container.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function forget( $key ) {
		unset( $this->items[$key] );
    }

    /**
     * Get all of the module items for the application.
     *
     * @return array
     */
    public function all() {
        return $this->items;
    }

    /**
     * Determine if the given module option exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function offsetExists( $key ): bool {
        return $this->has( $key );
    }

    /**
     * Get a module option.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet( $key ) {
        return $this->get( $key );
    }

    /**
     * Set a configuration option.
     *
     * @param  string   $key
     * @param  mixed    $value
     *
     * @return void
     */
    public function offsetSet( $key, $value ): void {
        $this->set( $key, $value );
    }

    /**
     * Unset a configuration option.
     *
     * @param  string   $key
     *
     * @return void
     */
    public function offsetUnset( $key ): void {
        $this->set( $key, null );
    }
}