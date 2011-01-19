<?php

require '../lib/form.php';

Tpl::$default_template_dir = dirname( __FILE__ ) . '/';

/**
 * Form with custom labels and help messages.
 */
class Labeled_form extends Form
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
				'label' => 'Your name',
				'help_msg' => 'Please enter your full name.',
			),

			'title' => array(
				'required' => true,
				'label' => 'Your title',
				'help_msg' => 'E.g. "Mr.", "Ms." or "Dr."',
			),

			'gender' => array(
				'required' => true,
				'label' => 'Your gender',
				'help_msg' => "Not to worry, you don't have to tell.",
				# possible values for this field
				'values' => array(
					'n' => 'Not gonna tell!',
					'm' => 'Hunk',
					'f' => 'Babe',
				),
				# default value
				'value' => 'm',
				# need to specify how to render this field.
				# 'radio' would render a list of radio buttons with the same values,
				# for example (see the colors field below)
				'render_as' => 'select',
			),

			'msg' => array(
				'required' => true,
				'label' => 'Your message',
				'help_msg' => 'A little message here please.',
				'render_as' => 'textarea',
			),

			'colors' => array(
				'required' => true,
				'label' => 'Which one do you like the most?',
				'help_msg' => 'You can only pick one, sorry!',
				'values' => array(
					'y' => 'Yellow',
					'm' => 'Mellow',
				),
				'value' => 'm',
				'render_as' => 'radio',
			),

			'fruits' => array(
				'required' => true,
				'label' => 'Pick your favorite fruits',
				'help_msg' => 'Choose as many as you like!',
				'values' => array(
					'b' => 'Banana',
					'o' => 'Orange',
					'g' => 'Grapes',
					'a' => 'Avocado',
				),
				'render_as' => 'checkboxes',
			),

			# a single checkbox with a main label can be achieved with a list
			# of one checkboxes
			'newsletter' => array(
				'required' => true,
				'label' => 'Newsletter',
				'help_msg' => 'Get news on this, that and the other.',
				'values' => array(
					'label' => 'Yes, please subscribe me to the newsletter.',
				),
				'render_as' => 'checkboxes',
			),

			'standalone' => array(
				'required' => true,
				'label' => 'Standalone checkbox.',
				'help_msg' => 'This is a standalone checkbox. You do have to check it.',
				'render_as' => 'checkbox',
			),

			'favorite_picture' => array(
				'required' => true,
				'label' => 'A favorite picture',
				'help_msg' => 'JPG, GIF or PNG please.',
				'render_as' => 'file',
			),

			'password' => array(
				'required' => true,
				'label' => 'Please enter your password',
				'help_msg' => 'Must be at least 6 characters.',
				'min' => 6,
				'render_as' => 'password',
			),

			'abort' => array(
				'label' => "sorry, I'm not ready yet, go back",
				'value' => 'index.php',
				'render_as' => 'abort',
			),
			'submit' => array(
				'label' => 'Yes. Sign me up, Scotty!',
				'render_as' => 'submit',
			),
		);
	}
}

$form = new Labeled_form();
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
	wrapping( 'labels_and_help_messages.html.php' );
