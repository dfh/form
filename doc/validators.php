<?php

require '../lib/form.php';

Tpl::$default_template_dir = dirname( __FILE__ ) . '/';

/**
 * Form with custom validators.
 */
class Demo_form extends Form
{
	/**
	 * Returns a definition of the fields of this form.
	 */
	protected function fields()
	{
		return array(
			'string' => array(
				'label' => 'Validate by type (string)',
				'help_msg' => 'Default validation is based on the field type
											("string", in this case).',
			),
			'e-mail' => array(
				'type' => 'email',
				'label' => 'Validate by type (email)',
				'help_msg' => 'Using type "email" will validate as e-mail address.',
			),
			'int' => array(
				'type' => 'int',
				'label' => 'Validate by type (int)',
				'help_msg' => 'Using type "int" will validate as integer.',
			),
			'regexp' => array(
				'type' => 'string',
				'label' => 'Validate using regexp',
				'help_msg' => 'Use <em>regexp</em> to validate regular expression.',
				'regexp' => '/foo/',
			),
			'no_validation' => array(
				'add_def_validators' => false,
				'help_msg' => 'Disable default validators by setting
											<em>add_def_validators</em> to false.',
			),
			'class_method_validation' => array(
				'help_msg' => 'If $this-&gt;validate_<em>field_name</em>() exists,
											it will be automagically used for validation.',
			),
			'validator_callback' => array(
				'required' => true,
				'label' => 'Single validator callback',
				'help_msg' => 'Add custom validators by adding callbacks to
											<em>validators</em>.',
				'validators' => function( $self, $field, $val ){
						if ( $val != 'orange' )
							throw new Validation_error( 'Must be orange!' );
					},
			),
			'validator_callback_without_defaults' => array(
				'required' => true,
				'label' => 'Single validator callback without defaults',
				'help_msg' => 'Default validators (required and type) are added
											by default, disable using <em>add_def_validators</em>.',
				'add_def_validators' => false,
				'validators' => function( $self, $field, $val ) {
						if ( $val != 'orange' )
							throw new Validation_error( 'Must be orange!' );
					},
			),
			'validator_callback_this' => array(
				'label' => 'Using "this:"-callbacks',
				'help_msg' => 'The magic callback "this:<em>method_name</em>"
											maps to <em>method_name()</em> on the form object.',
				'validators' => 'this:validate_class_method_validation',
			),
			'multiple_validator_callbacks' => array(
				'help_msg' => 'Add multiple validators using an array, they will
											be run in sequence. Mixing callback types is perfectly
											OK. (This field will never validate)',
				'validators' => array(
					function( $self, $field, $val ) {
						if ( $val != 'grape' )
							throw new Validation_error( 'Must be grape!' );
					},
					'this:validate_class_method_validation',
				),
			),
			'custom_type' => array(
				'type' => 'special_k',
				'help_msg' => 'Validation of custom types are mapped to
											$this-&gt;validator_<em>type()</em>. Override default
											type validation by overriding corresponding
											$this-&gt;validator_<em>type()</em>.',
			),
			'submit' => true,
		);
	}

	/**
	 * Will be used for validating the field "class_method_validation".
	 */
	protected function validate_class_method_validation( $self, $field, $val )
	{
		if ( $val != 'banana' )
			throw new Validation_error( 'Must be banana!' );
	}

	/**
   * Will be used for validating the type "special_k".
	 */
	protected function validator_special_k( $self, $field, $val )
	{
		if ( $val != 'nothankyou' )
			throw new Validation_error( 'Must be nothankyou!' );
	}
}

$form = new Demo_form();
$msg = '';
$ok = true;

# validate if posted
if ( is_post() ) {
	# need to tell the form what data source to use
	$form->source( $_POST );

	# is_valid() will validate and save error messages (if any)
	if ( $form->is_valid() ) {
		$msg = "OK!";
	} else {
		$ok = false;
		$msg = "NOT OK!";
	}
}

$ctxt = array(
	'form' => $form,
	'ok' => $ok,
	'msg' => $msg,
	'source' => file_get_contents( __FILE__ ),
);

echo Tpl::create( 'layout.html.php', $ctxt )->
	wrapping( 'validators.html.php' );
