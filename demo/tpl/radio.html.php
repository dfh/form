<div <?= $this->error ? ' class="error"' : ''; ?>>
	<label id="label-<?= $this->id; ?>" for="input-<?= $this->id; ?>"><?= $this->label; ?></label>
	<?= $this->error ? '<div class="error-w">' : ''; ?>	
<ul>
<?php $i = 0; foreach ( $this->vals as $value => $label ): ?>
	<li>
		<input type="radio" name="<?= $this->name; ?>" class="radio" id="input-<?= $this->id . '-' . $i; ?>" value="<?= $value; ?>"<?= $this->value == $value ? ' checked' : ''; ?>/>
		<label for="input-<?= $this->id . '-' . $i++; ?>" class="radio"><?= $label; ?></label>
	</li>
<?php endforeach; ?>
</ul>
	<?= $this->error ? '</div>' : ''; ?>
<?php if ( $this->error ): ?>
	<span class="error"><?= $this->error; ?></span>
<?php endif; ?>
</div>
