<?php
$this->title = 'Inputs';
?>

<p class="nav">
	← <a href="index.php">Back to index</a>
</p>

<h1>Demo: input elements</h1>

<p>
	This is a demo of using different input elements in Form. Try out the form below or skip to the <a href="#source">source code below</a>.
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
EOT
); ?></code></pre>
