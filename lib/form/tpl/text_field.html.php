<?php
$cs = array();
if ( $this->error ) $cs[] = 'error';
if ( $this->first ) $cs[] = 'f';
if ( $this->last ) $cs[] = 'l';
?>
<div <?= $cs ? 'class="' . implode( ' ', $cs ) . '"' : ''; ?>>
	<label for="input-<?= $this->id; ?>"><?= $this->label; ?><small><?= $this->required ? ' (required)' : '' ; ?></small></label>
<?php if ( $this->help_msg ): ?>
	<span class="help"><?= $this->help_msg; ?></span>
<?php endif; ?>
	<?= $this->error ? '<div class="error-w">' : ''; ?>	
	<input type="text" name="<?= $this->field_name; ?>" id="input-<?= $this->id; ?>" class="txt" value="<?= $this->value; ?>" />
	<?= $this->error ? '</div>' : ''; ?>
<?php if ( $this->error ): ?>
	<span class="error"><?= $this->error; ?></span>
<?php endif; ?>
</div>
