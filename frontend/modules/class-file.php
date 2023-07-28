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
 * File Fields.
 */
class File extends Module {

	use Singleton;

	/**
	 * Module vars.
	 *
	 * @var mixed
	 */
	protected $name     = 'file';
	protected $fields   = [ 'file', 'file*' ];

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
    
        $attrs['type']      = 'file';
        $attrs['name']      = $tag->name;
        $attrs['id']        = $tag->get_id_option();
        $attrs['size'] 		= $tag->get_size_option( '40' );
        $attrs['class']		= $tag->get_class_option( join( ' ', $class ) );
        $attrs['tabindex'] 	= $tag->get_option( 'tabindex', 'signed_int', true );
        $attrs['accept']    = wpcf7_acceptable_filetypes( $tag->get_option( 'filetypes' ), 'attr' );

        if ( $tag->is_required() ) {
            $attrs['required'] = true;
            $attrs['aria-required'] = 'true';
        }

        $label = $tag->has_option( 'first_as_label' ) ? (string) reset( $tag->values ) : null;

        $html = wecodeart( 'markup' )::wrap( 'cf7-file-field', [ [
            'tag' 	=> 'span',
            'attrs' => [
                'class'     => self::get_wrap_class( $tag ),
                'data-name' => $tag->name
            ]
        ] ], 'wecodeart_input', [ 'file', [
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

    /**
	 * Get Module args.
	 *
	 * @return array
	 */
	protected function get_args() {
		return [
			'name-attr'			=> true,
			'file-uploading'	=> true,
		];
	}
}
