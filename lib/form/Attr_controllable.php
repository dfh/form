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
 * Object with tweaked __get/__call behavior.
 *
 * Shortly, unset properties will return null, and calls to undefined
 * methods will return a) the property with the same name (P) or b) the result
 * of calling P, if P is callable.
 *
 * This results in two obvious gotchas:
 *
 *	1. Misspelling of property names might go unnoticed.
 *	2. Misspelling of method names might go unnoticed.
 */
class Attr_controllable 
{
	/**
	 * Stores attributes with attr($k, $v = null) get/set behavior.
	 *
	 * @see Attr_controllable::act_as_map()
	 */
	protected $_acts_as_map = array();

	/**
	 * Return null when trying to get attribute that has not been set.
	 *
	 * This is somewhat of a gotcha, as it might mask misspelled property
	 * names, for example:
	 *
	 *	$obj->color = 'red';
	 *
	 *	echo $obj->colour; # prints null, as $obj->colour has not been set.
	 */
	public function __get( $attr )
	{
		return null;
	}

	/**
	 * If $this->$func is set, call it. Else return it. Also deals with props
	 * set to act as maps by $this->act_as_map().
	 */
	public function __call( $func, $args )
	{
		if ( isset( $this->$func ) ) {
			$f =& $this->$func;
			if ( isset( $this->_acts_as_map[$func] ) && count( $args ) > 0 ) {
				# at least two args, set key and value to first and second args 
				# respectively
				if ( count( $args ) > 1 ) {
					$f[$args[0]] = $args[1];
				}	
				return isset( $f[$args[0]] ) ? $f[$args[0]] : null;

			# callable, return call value
			} elseif ( is_callable( $f ) ) {
				return call_user_func_array( $f, $args );

			# not callable, return property, whatever it might be
			} else {
				return $f;
			}
		}
	}

	/**
	 * Makes $this->$attr have $attr($k, $v = null) get/set behavior.
	 *
	 * This makes it easy to add get/set-functions for properties:
	 *
	 *	$obj->post =& $_POST;
	 *	$obj->act_as_map( 'post' );
	 *	$obj->post( 'param' ); # -> POST param 'param'
	 *	$obj->post( 'param', 'value' ); # sets param 'param' to 'value'
	 */
	public function act_as_map( $attr )
	{
		$this->_acts_as_map[$attr] = true;
	}
}
