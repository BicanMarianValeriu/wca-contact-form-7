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
 * Password Fields.
 */
class Password extends Module {

	use Singleton;

	/**
	 * Module vars.
	 *
	 * @var mixed
	 */
	protected $name     = 'password';
	protected $fields   = [ 'password', 'password*' ];

    /**
	 * Register Module
	 */
	public function register() {
		parent::register();

        add_action( 'wpcf7_admin_init',         [ $this, 'admin_init'   ], 15, 0 );
        add_filter( 'wpcf7_validate_password',  [ $this, 'validation'   ], 10, 2 );
        add_filter( 'wpcf7_validate_password*', [ $this, 'validation'   ], 10, 2 );
	}

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

        $attrs   = [];

        if ( wpcf7_support_html5() ) {
            $attrs['type'] = $tag->basetype;
        } else {
            $attrs['type'] = 'password';
        }
    
        $validation_error   = wpcf7_get_validation_error( $tag->name );
        $class              = explode( ' ', wpcf7_form_controls_class( $tag->type, 'wpcf7-password form-control' ) );
    
        if ( in_array( $tag->basetype, array( 'password' ), true ) ) {
            $class[] = ' wpcf7-validates-as-' . $tag->basetype;
        }
    
        if ( $validation_error ) {
            $class[] = 'wpcf7-not-valid';
            $attrs['aria-invalid'] = 'true';
        } else {
            $attrs['aria-invalid'] = 'false';
        }
    
        $attrs['name']      = $tag->name;
        $attrs['size']      = $tag->get_size_option( '40' );
        $attrs['maxlength'] = $tag->get_maxlength_option();
        $attrs['minlength'] = $tag->get_minlength_option();
    
        if ( $attrs['maxlength'] && $attrs['minlength'] ) {
            $attrs['pattern'] = ".{{$attrs['minlength']},{$attrs['maxlength']}}";
        } elseif ( $attrs['minlength'] ) {
            $attrs['pattern'] = ".{{$attrs['minlength']},}";
        }
    
        if ( $attrs['maxlength'] and $attrs['minlength'] && $attrs['maxlength'] < $attrs['minlength'] ) {
            unset( $attrs['maxlength'], $attrs['minlength'] );
        }
    
        $attrs['id']           = $tag->get_id_option();
        $attrs['class']        = $tag->get_class_option( $class );
        $attrs['tabindex']     = $tag->get_option( 'tabindex', 'signed_int', true );
        $attrs['autocomplete'] = $tag->get_option( 'autocomplete', '[-0-9a-zA-Z]+', true );
    
        if ( $tag->has_option( 'readonly' ) ) {
            $attrs['readonly'] = 'readonly';
        }
    
        if ( $tag->is_required() ) {
            $attrs['required']      = true;
            $attrs['aria-required'] = 'true';
        }
    
        $label = $tag->has_option( 'first_as_label' ) ? (string) reset( $tag->values ) : false;
        $value = count( $tag->values ) > 1 ? (string) end( $tag->values ) : (string) reset( $tag->values );
    
        if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
            $attrs['placeholder'] = $value;
            $value               = '';
        }

        $attrs['value'] = wpcf7_get_hangover( $tag->name, $tag->get_default_option( $value ) );
    
        $html = wecodeart( 'markup' )::wrap( 'cf7-password-field', [ [
            'tag' 	=> 'span',
            'attrs' => [
                'class'     => self::get_wrap_class( $tag ),
                'data-name' => $tag->name
            ]
        ] ], 'wecodeart_input', [ $attrs['type'], [
            'label'     => $label,
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
	 * Validation.
	 *
	 * @param   array   $result
	 * @param   object  $tag
     * 
	 * @return  object
	 */
    public function validation( $result, $tag ): object {
        $name  = $tag->name;
        $value = isset( $_POST[ $name ] ) ? trim( wp_unslash( strtr( (string) $_POST[ $name ], "\n", ' ' ) ) ) : '';

        if ( 'password' === $tag->basetype ) {
            if ( $tag->is_required() and '' === $value ) {
                $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
            }

            $atts['maxlength'] = $tag->get_maxlength_option();
            $atts['minlength'] = $tag->get_minlength_option();

            if ( $atts['maxlength'] && strlen( $value ) > $atts['maxlength'] ) {
                $result->invalidate( $tag, wpcf7_get_message( 'invalid_too_long' ) );
            }

            if ( $atts['minlength'] && strlen( $value ) < $atts['minlength'] ) {
                $result->invalidate( $tag, wpcf7_get_message( 'invalid_too_short' ) );
            }
        }

        return $result;
    }

    /**
	 * Admin.
     *
     * @return  void
	 */
    public function admin_init(): void {
        $tag_generator = \WPCF7_TagGenerator::get_instance();
        $tag_generator->add( 'password', __( 'password', 'wca-cf7' ), [ $this, 'admin_view' ] );
    }

    public function admin_view( $form, $args ): void {
        $args = wp_parse_args( $args, [] );
        $type = 'password';

        ?>
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Field type', 'contact-form-7' ); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php esc_html_e( 'Field type', 'contact-form-7' ); ?></legend>
                                    <label><input type="checkbox" name="required" /> <?php esc_html_e( 'Required field', 'contact-form-7' ); ?></label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php esc_html_e( 'Name', 'contact-form-7' ); ?></label></th>
                            <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php esc_html_e( 'Default value', 'contact-form-7' ); ?></label></th>
                            <td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br />
                            <label><input type="checkbox" name="placeholder" class="option" /> <?php esc_html_e( 'Use this text as the placeholder of the field', 'contact-form-7' ); ?></label></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php esc_html_e( 'Id attribute', 'contact-form-7' ); ?></label></th>
                            <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php esc_html_e( 'Class attribute', 'contact-form-7' ); ?></label></th>
                            <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box" style="display:flex;flex-wrap:wrap;">
            <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" style="flex:1;" />
            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
            </div>
            <p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
        </div>
        <?php
    }

    /**
	 * Get Module args.
	 *
	 * @return  array
	 */
	protected function get_args() {
		return [
			'name-attr' => true,
		];
	}
}
