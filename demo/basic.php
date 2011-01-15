<?php

require '../lib/form.php';

Tpl::$default_template_dir = dirname( __FILE__ ) . '/';

/**
 * A very simple form.
 */
class Simple_form extends Form
{
	/**
	 * Returns a definition of the fields of this form.
	 */
	protected function fields()
	{
		return array(
			# type will default to string, will be rendered as a normal text input
			'name' => array(
				'required' => true, # client is required to fill in this field
			),

			# use default submit button
			'submit' => true,
		);
	}
}

$form = new Simple_form();
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

echo Tpl::create( 'layout.html.php', $ctxt )->wrapping( 'basic.html.php' )->get();
