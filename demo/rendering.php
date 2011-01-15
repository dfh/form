<?php

require '../lib/form.php';

Tpl::$default_template_dir = dirname( __FILE__ ) . '/';

/**
 * Form using different ways of rendering.
 */
class Demo_form extends Form
{
	protected function fields()
	{
		return array(
			# standard
			'default' => array(
				'help_msg' => 'Default is to render as text input.',
			),

			# render_as
			'render_as' => array(
				'help_msg' => 'Use flag <em>render_as</em> to render as built-in
											input types like text, select, checkbox etc.',
				'render_as' => 'checkbox',
			),

			# render_as custom type
			'render_as_custom' => array(
				'help_msg' => 'You can also use the flag <em>render_as</em> to
											render custom types on a per-type basis, by defining
											$this-&gt;render_<em>type</em>.',
				'render_as' => 'string',
			),

			# renderer
			'custom_renderer_callback' => array(
				'help_msg' => 'Use the flag <em>renderer</em> to define a custom
											renderer callback.',
				'renderer' => function( $self, $field, $val, $ctxt ) {
					return '
						<div>
						<label>Custom renderer callback</label>
						<span class="help">' . $field['help_msg'] . '</span>
						</div>';
					},
			),

			# renderer this:callback
			'custom_renderer_callback_this' => array(
				'help_msg' => 'The <em>renderer</em> flag also allows for
											this:<em>method_name</em> magic callbacks.',
				'renderer' => 'this:custom_renderer_method',
			),

			# render_<field_name>
			'render_field_method' => array(
				'help_msg' => 'The method $this-&gt;render_<em>field_name</em>()
											will be used as default, if it exists.',
			),
		);
	}

	/** Used for 'render_as' => 'string'. */
	protected function render_string( $self, $field, $val, $ctxt )
	{
		return '
			<div>
			<label>Custom type rendering</label>
			<span class="help">' . $field['help_msg'] . '</span>
			</div>';
	}

	/** Used for 'renderer' => 'this:custom_renderer_method'. */
	protected function custom_renderer_method( $self, $field, $val, $ctxt )
	{	
		return '
			<div>
			<label>Custom renderer method (this:<em>method_name</em>)</label>
			<span class="help">' . $field['help_msg'] . '</span>
			</div>';
	}

	/** Used for rendering the field 'render_field_method'. */
	protected function render_field_render_field_method( $self, $field, $val, $ctxt )
	{
		if ( $ctxt['last'] ) $class = ' class="l"';
		return '
			<div' . $class . '>
			<label>Default renderer method(this:render_<em>field_name</em>)</label>
			<span class="help">' . $field['help_msg'] . '</span>
			</div>';
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
	wrapping( 'rendering.html.php' );
