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
 * Number Fields.
 */
class Number extends Module {

	use Singleton;

	/**
	 * Module vars.
	 *
	 * @var mixed
	 */
	protected $name     = 'number';
	protected $fields   = [ 'number', 'number*', 'range', 'range*' ];

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
        
        if ( wpcf7_support_html5() ) {
            $attrs['type'] = $tag->basetype;
        } else {
            $attrs['type'] = 'text';
        }
        
        $validation_error   = wpcf7_get_validation_error( $tag->name );
        $input_class        = ( $attrs['type'] === 'range' ) ? 'form-range' : 'form-control';
        $class              = explode( ' ', wpcf7_form_controls_class( $tag->type, $input_class ) );
        $class[]            = 'wpcf7-validates-as-number';
        
        if ( $validation_error ) {
            $class[] = 'wpcf7-not-valid';
            $attrs['aria-invalid'] = 'true';
            $attrs['aria-describedby'] = wpcf7_get_validation_error_reference( $tag->name );
        } else {
            $attrs['aria-invalid'] = 'false';
        }
        
        $attrs['class']     = $tag->get_class_option( join( ' ', $class ) );
        $attrs['name']      = $tag->name;
        $attrs['id']        = $tag->get_id_option();
        $attrs['tabindex']  = $tag->get_option( 'tabindex', 'signed_int', true );
        $attrs['min']       = $tag->get_option( 'min', 'signed_int', true );
        $attrs['max']       = $tag->get_option( 'max', 'signed_int', true );
        $attrs['step']      = $tag->get_option( 'step', 'int', true );
        $attrs['autocomplete'] = $tag->get_option( 'autocomplete', '[-0-9a-zA-Z]+', true );
        $attrs['readonly']  = $tag->has_option( 'readonly' );
    
        if ( $tag->is_required() ) {
            $attrs['required'] = true;
            $attrs['aria-required'] = 'true';
        }
    
        $label = $tag->has_option( 'first_as_label' ) ? (string) reset( $tag->values ) : false;
        $value = count( $tag->values ) > 1 ? (string) end( $tag->values ) : reset( $tag->values );
    
        if ( $tag->has_option( 'placeholder' ) or $tag->has_option( 'watermark' ) ) {
            $attrs['placeholder'] = $value;
            $value = '';
        }

        $attrs['value'] = wpcf7_get_hangover( $tag->name, $tag->get_default_option( $value ) );
    
        $html = wecodeart( 'markup' )::wrap( 'cf7-number-field', [ [
            'tag' 	=> 'span',
            'attrs' => [
                'class'     => self::get_wrap_class( $tag ),
                'data-name' => $tag->name
            ]
        ] ], 'wecodeart_input', [ $attrs['type'], [
            'label' 	=> $label,
            'attrs' 	=> $attrs,
            'messages' 	=> [
                'invalid' => [
                    'text'  => $validation_error,
                    'class' => 'invalid-feedback'
                ]
            ]
        ] ], false );

        return $html;
	}
}
