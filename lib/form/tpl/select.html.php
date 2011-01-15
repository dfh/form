<div<?= $this->error ? ' class="error"' : ''; ?>>
	<label for="input-<?= $this->id; ?>"><?= $this->label; ?></label>
<?php if ( $this->help_msg ): ?>
	<span class="help"><?= $this->help_msg; ?></span>
<?php endif; ?>
	<?= $this->error ? '<div class="error-w">' : ''; ?>	
<select name="<?= $this->field_name; ?>" id="input-<?= $this->id; ?>">
<?php foreach ( $this->values as $val => $label ): ?>
	<option <?= $val == $this->value ? 'selected ' : ''; ?>value="<?= $val; ?>"><?= $label; ?></option>
<?php endforeach; ?>
</select>
	<?= $this->error ? '</div>' : ''; ?>
<?php if ( $this->error ): ?>
	<span class="error"><?= $this->error; ?></span>
<?php endif; ?>
</div>
