<?php
$this->title = 'Basic';
?>

<p class="nav">
	← <a href="index.php">Back to index</a>
</p>

<h1>Demo: basic form</h1>

<p>
	This is a demo of the basic functionality of Form. Try out the form below!
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

<h2>Source code</h2>

<p>
The basic code for forms are split in two sections: (a) the form definition and (b) the controller code.
</p>
<p>
The form definition specifies what input elements the form has, how they should be validated, what error messages to print if there are validation errors, etc.
</p>
<p>
The controller code is the code that sets up the form, initiates validation and takes action depending on the result of the validation. Exactly how this will look depends on what framework (if any) that you're working within.
</p>

<h3>Form definition</h3>

<p>
In it's simplest, the form definition code looks like this:
</p>

<pre class="lang-php"><code class="lang-php"><?= htmlspecialchars( <<<EOT
/** A very simple form. */
class Simple_form extends Form
{
	/** Returns a definition of the fields of this form. */
	protected function fields()
	{
		return array(
			'name' => array(
				'required' => true, # client is required to fill in this field
			),
			'submit' => true,
		);
	}
}
EOT
); ?></code></pre>

<h3>Controller code</h3>

<p>
The controller code for this demo page looks something like this:
</p>

<pre class="lang-php"><code class="lang-php"><?= htmlspecialchars( <<<EOT
\$form = new Simple_form();

# validate if posted
if ( is_post() ) {
	# need to tell the form what data source to use
	\$form->source( \$_POST );

	# is_valid() will validate and save error messages (if any)
	if ( \$form->is_valid() ) {
		echo 'OK!';
	} else {
		echo 'ERROR!';
	}
}
EOT
); ?></code></pre>

