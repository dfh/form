<div<?= $this->error ? ' class="error"' : ''; ?>>
	<?= $this->error ? '<div class="error-w">' : ''; ?>	
<input class="chk" type="checkbox" name="<?= $this->field_name; ?>" value="1" id="input-<?= $this->id; ?>" <?= $this->value ? ' checked' : ''; ?>>
	<label class="chk" for="input-<?= $this->id; ?>"><?= $this->label; ?></label>
<?php if ( $this->help_msg ): ?>
	<small class="help"><?= $this->help_msg; ?></small>
<?php endif; ?>
	<?= $this->error ? '</div>' : ''; ?>
<?php if ( $this->error ): ?>
	<span class="error"><?= $this->error; ?></span>
<?php endif; ?>
</div>
