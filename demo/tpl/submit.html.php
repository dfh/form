<div class="btns submit">
	<input type="submit" id="input-<?= $this->id; ?>" class="btn submit" value="<?= $this->label; ?>" />
<?php if ( $this->form->has_field('abort') ): ?>
	<?= _('or'); ?> <a href="<?= $this->form->field_info( 'abort', 'val' ); ?>"><?= $this->form->field_info( 'abort', 'label' ); ?></a>
<?php endif; ?>
</div>
