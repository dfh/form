<form <?= $this->form_id ? 'id="' . $this->form_id . '" ' : ''; ?>action="<?= $this->action; ?>" method="<?= $this->method == 'get' ? 'get' : 'post' ?>"<?= $this->enctype ? ' enctype="' . $this->enctype . '"' : ''; ?> accept-encoding="<?= $this->accept_encoding; ?>">
<?= $this->content; ?>
<?php if ( $this->method == 'put' ): ?>
	<input type="hidden" name="use_http_put" value="1" />
<?php endif; ?>
<?php if ( $this->method == 'delete' ): ?>
	<input type="hidden" name="use_http_delete" value="1" />
<?php endif; ?>
</form>
