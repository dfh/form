<?php

require '../lib/form.php';

Tpl::$default_template_dir = dirname( __FILE__ ) . '/';

$ctxt = array(
	'source' => file_get_contents( __FILE__ ),
);

echo Tpl::create( 'layout.html.php', $ctxt )->
	wrapping( 'index.html.php' );
