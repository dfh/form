<?php

require '../lib/form.php';

Tpl::$default_template_dir = dirname( __FILE__ ) . '/';

/**
 * Form with fieldsets.
 */
class Demo_form extends Form
{
	protected function fields()
	{
		return array(
			'name' => array(),
			'fav_color' => array(),
			'fav_fruit' => array(),
			'fav_season' => array(),
			'fav_beverage' => array(),
			'fav_sound' => array(),
			'fav_oil' => array(),
			'submit' => true,
		);
	}

	protected function fieldsets()
	{
		return array(
			# normal, verbose definition
			'favs' => array(
				'title' => 'These are a few of my favorite things',
				'fields' => array(
					'fav_color', 'fav_fruit', 
				),
			),

			# if no title needed, single field can be given as string
			'personal_info' => 'name',

			# actually, multiple can, too, comma-separated
			'more_favs' => 'fav_oil,fav_sound',

			'even_more_favs' => array(
				'title' => 'Last two now!',
				'fields' => array(
					'fav_season',	'fav_beverage',
				),
			),

			'submit' => array(
				# actually don't wrap the submit button in a fieldset, please
				'render_in_fieldset' => false,
				'fields' => 'submit',
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
	wrapping( 'fieldsets.html.php' );
