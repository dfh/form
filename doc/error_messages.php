<?php

require '../lib/form.php';

Tpl::$default_template_dir = dirname( __FILE__ ) . '/';

/**
 * Form with custom error messages.
 */
class Demo_form extends Form
{
	/**
	 * Returns a definition of the fields of this form.
	 */
	protected function fields()
	{
		return array(
			'using_callback' => array(
				'help_msg' => '<em>$this-&gt;errmsg_for_field_name()</em> will be
											automagically called getting the error message for
											<em>field_name</em>.',
				'regexp' => '/honeydew/',
			),
			'using_errmsg_validator_name' => array(
				'help_msg' => 'Set <em>errmsg_validator_name</em> to customize
											error message for <em>validator_name</em>.',
				'required' => true,
				'min' => 3,
				'errmsg_empty' => 'Please write something will ya!',
				'errmsg_string' => 'At least 3 characters, please!',
			),
			'errmsg_validator_name_method' => array(
				'help_msg' => '<em>$this-&gt;errmsg_validator_name()</em> will be
											automagically called getting error messages for
											validator named <em>validator_name</em>.',
				'regexp' => '/tangerine/',
			),
			'submit' => true,
		);
	}

	/** Will be used as error message for the field "using_callback". */
	protected function errmsg_for_using_callback( $attr )
	{
		return 'Must be honeydew!';
	}

	/** Will be used as error message for the validator "regexp". */
	protected function errmsg_regexp( $attr )
	{
		return 'Failed to match ' . $attr['regexp'] . "!";
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
	wrapping( 'error_messages.html.php' );
