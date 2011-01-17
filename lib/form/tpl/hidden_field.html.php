<?php if ( is_array( $this->value ) ): ?>
<?php foreach ( $this->value as $v ): ?>
<input type="hidden" name="<?= $this->field_name; ?>[]" value="<?= $v; ?>" />
<?php endforeach; ?>
<?php else: ?>
<input type="hidden" name="<?= $this->field_name; ?>" value="<?= $this->value; ?>" />
<?php endif; ?>
