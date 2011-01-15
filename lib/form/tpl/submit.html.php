<?php
$cs = array();
if ( $this->error ) $cs[] = 'error';
if ( $this->first ) $cs[] = 'f';
if ( $this->last ) $cs[] = 'l';
?>
<div class="btns submit<?= $cs ? ' ' . implode( ' ', $cs ) : ''; ?>">
	<input type="submit" id="input-<?= $this->id; ?>" class="btn submit" value="<?= $this->label; ?>" />
<?php if ( $this->form->has_field('abort') ): ?>
	<?= _('or'); ?> <a href="<?= $this->form->field_info( 'abort', 'value' ); ?>"><?= $this->form->field_info( 'abort', 'label' ); ?></a>
<?php endif; ?>
</div>
