<?php
$this->title = 'Fieldsets';
?>

<p class="nav">
	← <a href="index.php">Back to index</a>
</p>

<h1>Demo: fieldsets</h1>

<p>
	This is a demo of using fieldsets in Form. Try out the form below or skip to the <a href="#source">source code below</a>.
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
EOT
); ?></code></pre>
