<div <?= $this->error ? ' class="error"' : ''; ?>>
	<label id="label-<?= $this->id; ?>" for="input-<?= $this->id; ?>"><?= $this->label; ?></label>
<?php if ( $this->help_msg ): ?>
	<span class="help"><?= $this->help_msg; ?></span>
<?php endif; ?>
	<?= $this->error ? '<div class="error-w">' : ''; ?>	
<ul>
<?php $i = 0; foreach ( $this->values as $value => $label ): ?>
	<li>
		<input type="checkbox" name="<?= $this->field_name; ?>[]" class="chk" id="input-<?= $this->id . '-' . $i; ?>" value="<?= $value; ?>"<?= in_array( $value, (array) $this->value ) ? ' checked' : ''; ?>/>
		<label for="input-<?= $this->id . '-' . $i; ?>" class="chk"><?= $label; ?></label>
	</li>
<?php $i++; endforeach; ?>
</ul>
	<?= $this->error ? '</div>' : ''; ?>
<?php if ( $this->error ): ?>
	<span class="error"><?= $this->error; ?></span>
<?php endif; ?>
</div>
