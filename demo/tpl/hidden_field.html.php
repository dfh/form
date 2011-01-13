<?php if ( is_array( $this->value ) ): ?>
<?php foreach ( $this->value as $v ): ?>
<input type="hidden" name="<?= $this->name; ?>[]" value="<?= $v; ?>" />
<?php endforeach; ?>
<?php else: ?>
<input type="hidden" name="<?= $this->name; ?>" value="<?= $this->value; ?>" />
<?php endif; ?>
