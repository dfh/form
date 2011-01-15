<?php

require '../lib/form.php';

Tpl::$default_template_dir = dirname( __FILE__ ) . '/';

/**
 * Form with different input fields.
 */
class Demo_form extends Form
{
	/**
	 * Returns a definition of the fields of this form.
	 */
	protected function fields()
	{
		return array(
			'text' => array(
				'help_msg' => 'Default is text.',
			),
			'password' => array(
				'help_msg' => '<em>render_as</em> <em>password</em> for
											password fields.',
				'render_as' => 'password',
			),
			'select' => array(
				'help_msg' => '<em>select</em> for select fields.',
				'values' => array(
					'n' => 'Not gonna tell!',
					'm' => 'Hunk',
					'f' => 'Babe',
				),
				'value' => 'm',
				'render_as' => 'select',
			),
			'textarea' => array(
				'help_msg' => '<em>textarea</em> for textarea fields.',
				'render_as' => 'textarea',
			),
			'radio' => array(
				'help_msg' => '<em>radio</em> for radio buttons.',
				'values' => array(
					'y' => 'Yellow',
					'm' => 'Mellow',
				),
				'value' => 'm',
				'render_as' => 'radio',
			),
			'checkbox' => array(
				'help_msg' => '<em>checkbox</em> for single checkboxes.',
				'render_as' => 'checkbox',
			),
			'checkboxes' => array(
				'help_msg' => '<em>checkboxes</em> for multiple checkboxes.',
				'values' => array(
					'b' => 'Banana',
					'o' => 'Orange',
					'g' => 'Grapes',
					'a' => 'Avocado',
				),
				'value' => array( 'b', 'g' ),
				'render_as' => 'checkboxes',
			),
			'list_style_checkbox' => array(
				'help_msg' => 'You can also render a single checkbox as
											<em>checkboxes</em>.',
				'values' => array(
					'label' => 'Yes, please subscribe me to the newsletter.',
				),
				'render_as' => 'checkboxes',
			),
			'file' => array(
				'help_msg' => '<em>file</em> for file inputs.',
				'render_as' => 'file',
			),
			'abort' => array(
				'label' => '<em>abort</em> for abort links',
				'value' => 'index.php',
				'render_as' => 'abort',
			),
			'submit' => array(
				'label' => 'Submits as submit',
				'render_as' => 'submit',
			),
		);
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
	wrapping( 'inputs.html.php' );
