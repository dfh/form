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
 * Validation error.
 */
class Validation_error extends Exception
{
	protected $errors = array();

	public function __construct( $errors )
	{
		$this->errors = $errors;

		if ( is_array( $errors ) )
			$errmsg = implode( ', ', $errors );
		else
			$errmsg = $errors;

		parent::__construct( $errmsg );
	}

	public function errors()
	{
		return $this->errors;
	}
}
