Form
====

A lightweight framework for defining, validating and rendering HTML forms.

## Overview

Form lets you define forms by subclassing a base `Form` class. You specify the
action, method etc. of the form, and override a class method that returns an
array defining all the fields of your form.

Defining, validating and rendering HTML forms basically comes down to three
areas:

  * Validation of form data.
  * Rendering of input elements.
  * Displaying error messages when user input is invalid.

Form has built-in support for validation of standard types of input data like
e-mail addresses, URLs, timestamps, integers etc. For special cases, adding
validation rules is as simple (or hard) as writing a regular expression or
writing a callback function.

The same goes for rendering standard input fields; there is built-in support
for rendering fields as text inputs, select drop-downs, checkboxes, radio
buttons, textareas etc., and defining custom rendering is simple.

Error messages are designed to be sensible by default, and pretty easy to
customize.

See "Installation and usage" below for a very minimal example. More examples
are in `doc/`.

## Requirements

Form has only been tested with PHP 5.3. Should work (possibly with minimal
modification) on any PHP 5+ version.

## Installation and usage

Install by copying `lib/form.php` and `lib/form/` to a sensible directory.
Include `lib/form.php` and you are ready to go.

A small example:

```php
<?php

require 'lib/form.php';

/** A minimal form example. */
class Simple_form extends Form
{
  public $form_action = 'target.php';
  public $form_method = 'post';

  /** Returns a definition of the fields of this form. */
  protected function fields()
  {
    return array(
      'name' => array(
        'label' => 'Please enter your name',
        'type' => 'string',
        'required' => true, # the user must fill in this field
        'render_as' => 'text',
      ),
      'submit' => true, # will use default submit button
    );
  }
}

$form = new Simple_form();

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
  # need to tell the form what data source to use
  $form->source( $_POST );
  if ( $form->is_valid() )
    echo 'OK!';
  else
    echo 'Uh-oh!';
}

echo $form->render();
```

## Known issues

Theses names are reserved (due to implementation choices) and not possible to
use as field names:

  * `form_action`
  * `form_method`
  * `form_enctype`
  * `form_accept_charset`
  * `template_dir`
  * anything prefixed by `_`

## License

Copyright (c) David Högberg.

Licensed under the MIT License. See LICENSE for more information.
