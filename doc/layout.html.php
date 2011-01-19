<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?= $this->title; ?></title>

	<link rel="stylesheet" href="reset.css" media="screen" />
	<link rel="stylesheet" href="screen.css" media="screen" />
	<link rel="stylesheet" href="prettify.css" media="screen" />

	<link rel="stylesheet" href="form.css" media="screen" />

	<script src="jquery-1.4.4.min.js"></script>

	<script type="text/javascript">
		// http://tomayko.com/writings/javascript-prettification
		$(document).ready(function() {
			// add prettyprint class to all <pre><code></code></pre> blocks
			var prettify = false;
			$("pre code").parent().each(function() {
				$(this).addClass('prettyprint');
				prettify = true;
			});
			// if code blocks were found, bring in the prettifier ...
			if ( prettify ) {
				$.getScript("prettify.js", function() { prettyPrint() });
			}
		});
		$(document).ready( function() {
			var c = $('#filesource').children('pre');
			var a = $('<a href="#">Show source</a>');
			a.click( function() {
				if ( $(this).text() == 'Show source' ) {
					$(this).text('Hide source');
					c.slideDown('fast');
					return false;
				}	else {
					$(this).text('Show source');
					c.slideUp('fast');
					return false;
				}
			});
			$('#filesource h2').after( a );
			c.hide();
		});
	</script>
</head>
<body>

<!-- document -->
<div id="doc">

	<!-- content -->
<?= $this->content; ?>
	<!-- / content -->

<div class="hr">
	<hr />
</div>

<div class="source" id="filesource">
<h2>Full source code for this file</h2>
<pre class="lang-php"><code class="lang-php"><?= htmlspecialchars( $this->source ); ?></code></pre>
</div>

</div>
<!-- / document -->
</body>
</html>
