<div<?= $this->error ? ' class="error"' : ''; ?>>
	<label for="input-<?= $this->id; ?>"><?= $this->label; ?></label>
	<?= $this->error ? '<div class="error-w">' : ''; ?>	
	<input type="text" name="<?= $this->name; ?>" id="input-<?= $this->id; ?>" class="txt" value="<?= $this->value; ?>" />
<?php if ( $this->help_msg ): ?>
	<span class=""><?= $this->help_msg; ?></span>
<?php endif; ?>
	<?= $this->error ? '</div>' : ''; ?>
<?php if ( $this->error ): ?>
	<span class="error"><?= $this->error; ?></span>
<?php endif; ?>
</div>
