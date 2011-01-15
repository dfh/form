<div class="textarea<?= $this->error ? ' error' : ''; ?>">
	<label for="input-<?= $this->id; ?>"><?= $this->label; ?></label>
<?php if ( $this->help_msg ): ?>
	<span class="help"><?= $this->help_msg; ?></span>
<?php endif; ?>
	<?= $this->error ? '<div class="error-w">' : ''; ?>	
<textarea name="<?= $this->field_name; ?>" id="input-<?= $this->id; ?>"><?= $this->value; ?></textarea>
	<?= $this->error ? '</div>' : ''; ?>
<?php if ( $this->error ): ?>
	<span class="error"><?= $this->error; ?></span>
<?php endif; ?>
</div>
