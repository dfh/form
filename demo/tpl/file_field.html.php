<div<?= $this->error ? ' class="error"' : ''; ?>>
	<label for="input-<?= $this->id; ?>"><?= $this->label; ?></label>
	<?= $this->error ? '<div class="error-w">' : ''; ?>	
	<input type="file" name="<?= $this->name; ?>" id="input-<?= $this->id; ?>" class="file" value="<?= $this->value; ?>" />
<?php if ( $this->help_msg ): ?>
	<small class="help"><?= $this->help_msg; ?></small>
<?php endif; ?>
	<?= $this->error ? '</div>' : ''; ?>
<?php if ( $this->error ): ?>
	<span class="error"><?= $this->error; ?></span>
<?php endif; ?>
</div>
