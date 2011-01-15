<?php
$this->title = 'Error messages';
?>

<p class="nav">
	← <a href="index.php">Back to index</a>
</p>

<h1>Demo: error messages</h1>

<p>
	This is a demo of customizing error messages in Form. Try out the form below or skip to the <a href="#source">source code below</a>.
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
				'help_msg' => '<em>\$this-&gt;errmsg_for_field_name()</em> will be
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
				'help_msg' => '<em>\$this-&gt;errmsg_validator_name()</em> will be
											automagically called getting error messages for
											validator named <em>validator_name</em>.',
				'regexp' => '/tangerine/',
			),
			'submit' => true,
		);
	}

	/** Will be used as error message for the field "using_callback". */
	protected function errmsg_for_using_callback( \$attr )
	{
		return 'Must be honeydew!';
	}

	/** Will be used as error message for the validator "regexp". */
	protected function errmsg_regexp( \$attr )
	{
		return 'Failed to match ' . \$attr['regexp'] . "!";
	}
}
EOT
); ?></code></pre>
