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
 * Quiz Fields.
 */
class Quiz extends Module {

	use Singleton;

	/**
	 * Module vars.
	 *
	 * @var mixed
	 */
	protected $name     = 'quiz';
	protected $fields   = 'quiz';

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
    
        $validation_error = wpcf7_get_validation_error( $tag->name );
    
        $class      = explode( ' ', wpcf7_form_controls_class( $tag->type, 'form-control' ) );
        $class[]    = 'wpcf7-validates-as-number';
        
        if ( $validation_error ) {
            $class[] = 'wpcf7-not-valid';
            $attrs['aria-invalid']      = 'true';
            $attrs['aria-describedby']  = wpcf7_get_validation_error_reference( $tag->name );
        } else {
            $attrs['aria-invalid']      = 'false';
        }
    
        $attrs = [];
    
        $attrs['type'] = 'text';
        $attrs['name'] = $tag->name;
        $attrs['size'] = $tag->get_size_option( '40' );
        $attrs['maxlength'] = $tag->get_maxlength_option();
        $attrs['minlength'] = $tag->get_minlength_option();
    
        if ( $attrs['maxlength'] and $attrs['minlength'] and $attrs['maxlength'] < $attrs['minlength'] ) {
            unset( $attrs['maxlength'], $attrs['minlength'] );
        }
    
        $attrs['id']            = $tag->get_id_option();
        $attrs['class']         = $tag->get_class_option( join( ' ', $class ) );
        $attrs['tabindex']      = $tag->get_option( 'tabindex', 'signed_int', true );
        $attrs['autocomplete']  = 'off';
        $attrs['required']      = true;
        $attrs['aria-required'] = 'true';
    
        $pipes = $tag->pipes;
        
        if ( $pipes and ! $pipes->zero() ) {
            $pipe       = $pipes->random_pipe();
            $question   = $pipe->before;
            $answer     = $pipe->after;
        } else {
            $question   = '1+1=?';
            $answer     = '2';
        }
    
        $answer = wpcf7_canonicalize( $answer, [
            'strip_separators' => true,
        ] );

        $html = wecodeart( 'markup' )::wrap( 'cf7-quiz-field', [ [
            'tag' 	=> 'span',
            'attrs' => [
                'class'     => self::get_wrap_class( $tag, 'input-group has-validation' ),
                'data-name' => $tag->name
            ]
        ] ], function() use ( $tag, $attrs, $question, $answer, $validation_error ) {
            ?>
            <span class="wpcf7-quiz-label input-group-text"><?php echo esc_html( $question ); ?></span>
            <?php
            
            wecodeart_input( 'hidden', [
                'attrs' => [
                    'id'    => false,
                    'class' => false,
                    'name'  => '_wpcf7_quiz_answer_' . $tag->name,
                    'value' => wp_hash( $answer, 'wpcf7_quiz' )
                ]
            ] );

            wecodeart_input( 'text', [
                'attrs' 	=> $attrs,
                'messages' 	=> [
                    'invalid' => [
                        'text'  => $validation_error,
                        'class' => 'invalid-feedback'
                    ]
                ]
            ] );

        }, [], false );

        return $html;
	}

    /**
	 * Get Module args.
	 *
	 * @return array
	 */
	protected function get_args() {
		return [
			'name-attr'     => true,
			'do-not-store'  => true,
			'not-for-mail'  => true,
		];
	}
}
