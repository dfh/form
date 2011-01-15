<?php
$this->title = 'Rendering';
?>

<p class="nav">
	← <a href="index.php">Back to index</a>
</p>

<h1>Demo: rendering</h1>

<p>
	This is a demo of different ways of rendering forms and inputs in Form. Try out the form below or skip to the <a href="#source">source code below</a>.
</p>

<?php if ( $this->msg ): ?>
<div class="msg">
<p class="<?= $this->ok ? 'ok' : 'nok'; ?>">
	<?= $this->msg; ?>
</p>
</div>
<?php endif; ?>

<?= $this->form->render(); ?>

<div class="hr">
	<hr />
</div>

<h2 id="source">Source code</h2>

<p>
Below is the source code defining the form above.
</p>
<p>
For more information on creating forms, validating and rendering them, see <a href="overview.php">the Form overview</a>.
</p>

<pre class="lang-php"><code class="lang-php"><?= htmlspecialchars( <<<EOT
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
											\$this-&gt;render_<em>type</em>.',
				'render_as' => 'string',
			),

			# renderer
			'custom_renderer_callback' => array(
				'help_msg' => 'Use the flag <em>renderer</em> to define a custom
											renderer callback.',
				'renderer' => function( \$self, \$field, \$val, \$ctxt ) {
					return '
						<div>
						<label>Custom renderer callback</label>
						<span class="help">' . \$field['help_msg'] . '</span>
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
				'help_msg' => 'The method \$this-&gt;render_<em>field_name</em>()
											will be used as default, if it exists.',
			),
		);
	}

	/** Used for 'render_as' => 'string'. */
	protected function render_string( \$self, \$field, \$val, \$ctxt )
	{
		return '
			<div>
			<label>Custom type rendering</label>
			<span class="help">' . \$field['help_msg'] . '</span>
			</div>';
	}

	/** Used for 'renderer' => 'this:custom_renderer_method'. */
	protected function custom_renderer_method( \$self, \$field, \$val, \$ctxt )
	{	
		return '
			<div>
			<label>Custom renderer method (this:<em>method_name</em>)</label>
			<span class="help">' . \$field['help_msg'] . '</span>
			</div>';
	}

	/** Used for rendering the field 'render_field_method'. */
	protected function render_field_render_field_method( \$self, \$field, \$val, \$ctxt )
	{
		if ( \$ctxt['last'] ) \$class = ' class="l"';
		return '
			<div' . \$class . '>
			<label>Default renderer method(this:render_<em>field_name</em>)</label>
			<span class="help">' . \$field['help_msg'] . '</span>
			</div>';
	}
}
EOT
); ?></code></pre>
