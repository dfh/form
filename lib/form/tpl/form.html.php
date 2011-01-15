<form <?= $this->form_id ? "id=\"$this->form_id\"" : ''; ?>action="<?= $this->action; ?>" method="<?= $this->method == 'get' ? 'get' : 'post' ?>"<?= $this->enctype ? " enctype=\"$this->enctype\"" : ''; ?><?= $this->accept_charset ? " accept-charset=\"$this->accept_charset\"" : ''; ?>>
<?= $this->content; ?>
<?php if ( $this->method == 'put' ): ?>
	<input type="hidden" name="use_http_put" value="1" />
<?php endif; ?>
<?php if ( $this->method == 'delete' ): ?>
	<input type="hidden" name="use_http_delete" value="1" />
<?php endif; ?>
</form>
