<?php
$cs = array();
if ( $this->first_fieldset ) $cs[] = 'f';
if ( $this->last_fieldset ) $cs[] = 'l';
?>
<?= $this->fieldset_title ? '<h3 class="fieldset_title">' . $this->fieldset_title . '</h3>' : ''; ?>

<fieldset<?= $cs ? ' class="' . implode( ' ', $cs ) . '"' : ''; ?>>
<?= $this->fieldset_content; ?>
</fieldset>
