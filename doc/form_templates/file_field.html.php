<div<?= $this->error ? ' class="error"' : ''; ?>>
	<label for="input-<?= $this->id; ?>"><?= $this->label; ?></label>
<?php if ( $this->help_msg ): ?>
	<span class="help"><?= $this->help_msg; ?></span>
<?php endif; ?>
	<?= $this->error ? '<div class="error-w">' : ''; ?>	
	<input type="file" name="<?= $this->field_name; ?>" id="input-<?= $this->id; ?>" class="file" value="<?= $this->value; ?>" />
	<?= $this->error ? '</div>' : ''; ?>
<?php if ( $this->error ): ?>
	<span class="error"><?= $this->error; ?></span>
<?php endif; ?>
</div>
