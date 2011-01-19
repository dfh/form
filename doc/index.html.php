<?php
$this->title = 'Form';
?>
<h1>Form</h1>

<p class="intro">
A framework for defining, validating and rendering HTML forms.
</p>

<h2>Overview</h2>

<p>
Form is at it's core a PHP class named <code>Form</code>. When making a form to use on a web page, you simply extend this class, and implement a method that returns an array describing the fields of the form. This field description, or field definition, specifies the names of the fields, their default values (if any), how they should be validated and how they should be rendered.
</p>
<p>
Form has built-in support for validation and rendering of most normal fields like text input, select dropdowns, radio buttons, checkboxes, textareas etc.
</p>
<p>
For special cases, Form is very flexible. It allows you to use custom callbacks for default values, validation and rendering, in several different ways. Check out the <a href="#demos">demos</a> to see how to do it.
</p>
<p>
Unfortunately, there is no documentation written yet. However, the source is pretty well commented, and the demos should cover most normal uses and get you started.
</p>

<h2>License</h2>

<p>
Form is licensed under <a href="http://www.gnu.org/licenses/gpl-3.0.html">GPLv3</a>/<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/">CC BY-NC-SA 3.0</a> (use whichever one you like).
</p>

<h2>Source code</h2>

<p>
The latest sources can be downloaded from <a href="http://github.com/dfh/form">Github</a>.
</p>

<h2>Documentation</h2>

<p>
To be written..
</p>

<h2 id="demos">Demos</h2>

<ul>
	<li><a href="basic.php">Basics</a></li>
	<li><a href="inputs.php">Input fields (input, select, textarea etc.)</a></li>
	<li><a href="labels_and_help_messages.php">Labels and help messages</a></li>
	<li><a href="validators.php">Validators</a></li>
	<li><a href="error_messages.php">Error messages</a></li>
	<li><a href="rendering.php">Rendering</a></li>
	<li><a href="fieldsets.php">Fieldsets</a></li>
	<li><a href="xsrf_basic.php">Basic XSRF protection</a></li>
</ul>
