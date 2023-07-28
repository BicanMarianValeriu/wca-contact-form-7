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

namespace WCA\EXT\CF7\Frontend\Modules;

use WeCodeArt\Singleton;

/**
 * Checkbox Fields.
 */
class Checkbox extends Module {

	use Singleton;

	/**
	 * Module vars.
	 *
	 * @var mixed
	 */
	protected $name     = 'checkbox';
	protected $fields   = [ 'checkbox', 'checkbox*', 'radio' ];

	/**
	 * Return field HTML.
	 *
	 * @param   object $tag
     * 
	 * @return  string Rendered field output.
	 */
	public function get_html( $tag ) {
		if ( empty( $tag->name ) ) {
            return '';
        }

        $attrs = [];

        $validation_error   = wpcf7_get_validation_error( $tag->name );
        $class              = explode( ' ', wpcf7_form_controls_class( $tag->type ) );
    
        if ( $validation_error ) {
            $class[] = 'wpcf7-not-valid';
            $attrs['aria-describedby'] = wpcf7_get_validation_error_reference( $tag->name );
        }
    
        $is_inline      = $tag->has_option( 'inline' );
        $exclusive      = $tag->has_option( 'exclusive' );
        $first_as_label = $tag->has_option( 'first_as_label' );
        // To be implemented
        // $free_text      = $tag->has_option( 'free_text' );
        $multiple       = false;
    
        if ( 'checkbox' == $tag->basetype ) {
            $multiple   = ! $exclusive;
        } else { // radio
            $exclusive  = false;
        }
    
        if ( $exclusive ) {
            $class[]= 'wpcf7-exclusive-checkbox';
        }
    
        $attrs['id']    = $tag->get_id_option();
        $attrs['name']  = $tag->name;
        
        $attrs['class'] = join( ' ', array_filter( [
            $is_inline ? 'form-check-inline' : '',
            $tag->get_class_option( join( ' ', $class ) ),
        ] ) );
    
        $values = $tag->values;
        $labels = $tag->labels;

        if( $first_as_label ) {
            array_shift( $values );
            array_shift( $labels );
        }
    
        $default_choice = $tag->get_default_option( null, [ 'multiple' => $multiple ] );
        $choices    = [];
        $hangover   = wpcf7_get_hangover( $tag->name, $multiple ? [] : '' );

        if ( $hangover ) {
            $selected = $hangover;
        } else {
            $selected = $default_choice;
        }

        if( $selected ) {
            $attrs['value'] = $selected;
        }

        $label = $first_as_label ? (string) reset( $tag->values ) : false;

        foreach ( $values as $key => $value ) $choices[ $value ] = isset( $labels[$key] ) ? $labels[$key] : $value;
    
        $html = wecodeart( 'markup' )::wrap( 'cf7-checkbox-field', [ [
            'tag' 	=> 'span',
            'attrs' => [
                'class'     => self::get_wrap_class( $tag ),
                'data-name' => $tag->name
            ]
        ] ], 'wecodeart_input', [ 'fieldset', [
            'type'      => $tag->basetype,
            'label'     => $label,
            'exclusive' => $exclusive,
            'attrs' 	=> $attrs,
            'choices'   => $choices,
            'messages' 	=> [
                'invalid' => [
                    'text'  => $validation_error,
                    'class' => 'invalid-feedback'
                ]
            ]
        ] ], false );

        return $html;
	}

    /**
	 * Get Module args.
	 *
	 * @return array
	 */
	protected function get_args() {
		return [
			'name-attr'         => true,
			'selectable-values' => true,
			'multiple-controls-container' => true,
		];
	}
}
