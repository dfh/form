<?php

/*
 Copyright 2010, 2011 David Högberg (david@hgbrg.se)

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * @author David Högberg <david@hgbrg.se>
 * @package form
 */

require dirname( __FILE__ ) . '/form/std.php';
require dirname( __FILE__ ) . '/form/Attr_controllable.php';
require dirname( __FILE__ ) . '/form/Validatable.php';
require dirname( __FILE__ ) . '/form/Tpl.php';
require dirname( __FILE__ ) . '/form/Form.php';

require dirname( __FILE__ ) . '/Validation_error.php';
require dirname( __FILE__ ) . '/Xsrf_guard.php';

/** Default templates. */
Form::$default_template_dir = dirname( __FILE__ ) . '/form/tpl/';
