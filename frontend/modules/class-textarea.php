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
 * TextArea Fields.
 */
class TextArea extends Module {

	use Singleton;

	/**
	 * Module vars.
	 *
	 * @var mixed
	 */
	protected $name     = 'textarea';
	protected $fields   = [ 'textarea', 'textarea*' ];

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
        $class              = explode( ' ', wpcf7_form_controls_class( $tag->type, 'form-control' ) );
        
        if ( $validation_error ) {
            $class[] = 'wpcf7-not-valid';
            $attrs['aria-invalid'] 		= 'true';
            $attrs['aria-describedby'] 	= wpcf7_get_validation_error_reference( $tag->name );
        } else {
            $attrs['aria-invalid'] 		= 'false';
        }
    
        $attrs['cols'] 		= $tag->get_cols_option( '40' );
        $attrs['rows'] 		= $tag->get_rows_option( '10' );
        $attrs['maxlength'] = $tag->get_maxlength_option();
        $attrs['minlength'] = $tag->get_minlength_option();
    
        if ( $attrs['maxlength'] and $attrs['minlength'] and $attrs['maxlength'] < $attrs['minlength'] ) {
            unset( $attrs['maxlength'], $attrs['minlength'] );
        }
    
        $attrs['class']			= $tag->get_class_option( join( ' ', $class ) );
        $attrs['name']          = $tag->name;
        $attrs['id']            = $tag->get_id_option();
        $attrs['tabindex'] 		= $tag->get_option( 'tabindex', 'signed_int', true );
        $attrs['autocomplete'] 	= $tag->get_option( 'autocomplete', '[-0-9a-zA-Z]+', true );
        $attrs['readonly']      = $tag->has_option( 'readonly' );
    
        if ( $tag->is_required() ) {
            $attrs['required'] = true;
            $attrs['aria-required'] = 'true';
        }
    
        $label = $tag->has_option( 'first_as_label' ) ? (string) reset( $tag->values ) : false;
        $value = empty( $tag->content ) ? (string) end( $tag->values ) : $tag->content;
    
        if ( $tag->has_option( 'placeholder' ) or $tag->has_option( 'watermark' ) ) {
            $attrs['placeholder'] = $value;
            $value = '';
        }

	    $value = wpcf7_get_hangover( $tag->name, $tag->get_default_option( $value ) );
        $attrs['value'] = $value;

        $html = wecodeart( 'markup' )::wrap( 'cf7-textarea-field', [ [
            'tag' 	=> 'span',
            'attrs' => [
                'class'     => self::get_wrap_class( $tag ),
                'data-name' => $tag->name
            ]
        ] ], 'wecodeart_input', [ 'textarea', [
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
