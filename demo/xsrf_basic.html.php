<?php
$this->title = 'Basic XSRF protection';
?>

<p class="nav">
	← <a href="index.php">Back to index</a>
</p>

<h1>Demo: Basic XSRF-protected form</h1>

<p>
	This is a demo of the auto-magic built-in XSRF (Cross-Site Request Forgery) protection in Form. Try out the form below or skip to the <a href="#source">source code below</a>.
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
