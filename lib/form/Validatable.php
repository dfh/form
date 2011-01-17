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

/**
 * An object that can be validated.
 *
 * A Validatable object has one or more attributes (class properties)
 * that can be validated according to some specific rules.
 *
 * For example, we might have a class Person with the two attributes
 * name and age:
 *
 *	class Person {
 *		public $name;
 *		public $age;
 *	}
 *
 * And have the persons' name be required to be a string, and the age
 * to be a positive integer less than 150. Also, the name must be set,
 * but the age is allowed to be null.
 *
 * This validatable Person looks like this:
 *
 *	class Person extends Validatable {
 *		protected function attrdefs()
 *		{
 *			return array(
 *				'name' => array(
 *					'type' => 'string',
 *					'min' => 1,
 *					'max' => 2,
 *					'required' => true,
 *				),
 *				'age' => array(
 *					'type' => 'int',
 *					'min' => 0,
 *					'max' => 150,
 *				),
 *			);
 *		}
 *
 * The class method attrdefs() returns an attribute definitions that
 * specifies what attributes should be validated and what their requirements
 * are.
 *
 * Every attribute has a type that normally makes the base for it's validation.
 * In addition to this, there are several other flags that specify how the
 * validation should be made, as well as flags to define error messages and
 * custom validators (any valid callback can be used to validate an attribute).
 *
 * This is the full list of possible attribute definition flags. Only the type
 * is required (sensible defaults will be used for omitted flags):
 *
 *	type (string, 'string'):
 *		type (string|email|url|int|num|bool)
 *
 *	required (bool, false):
 *		attribute is required or not
 *
 *	name (string):
 *		readable attribute name, will default to uppercased attr_name
 *
 *	min (int, null):
 *		minimum value for int or string length
 *
 *	max (int, null):
 *		maximum ^^
 *	
 *	regexp (string, null):
 *		syntax to validate
 *
 *	errmsg (string):
 *		default error message
 *
 *	errmsg_<validator> (string):
 *		error message for <validator>
 *
 *	add_def_validators (bool, true):
 *		add default (empty+type) validators
 *
 *	validators (array (validator name => callback) or string):
 *		all validators to run. if add_def_validators, default will be added
 *
 *
 * In addition to this, default type validators and error messages can be
 * set by extending classes by overriding validator_*() and errmsg_*() methods.
 *
 * @author David Högberg <david@hgbrg.se>
 * @package form
 */
class Validatable extends Attr_controllable
{
	/** Attribute definitions. */
	protected $_attrdefs = array();

	/** Error messages. Associative array attr name => error message. */
	protected $_errors = array();

	/**
	 * Returns array with attribute definitions.
	 *
	 * @override
	 */
	protected function attrdefs()
	{
		return array();
	}

	/**
	 * Default attribute definition. All fields not set in the definition for
	 * attrs will be set according to these default values.
	 */
	protected function attrdef_default()
	{
		return array(
			'type' => 'string',
			'add_def_validators' => true,
			'required' => false,
			'min' => null,
			'max' => null,
			'validators' => array(),
		);
	}

	/**
	 * Creates a new Validatable.
	 */
	public function __construct()
	{
		if ( !$this->_attrdefs )
			$this->_attrdefs = $this->attrdefs();

		foreach ( $this->_attrdefs as $name => & $attr ) {
			$this->_init_attrdef( $name );
		}
	}

	/**
	 * Sets/get attribute definition for given attribute.
	 */
	public function attrdef( $attr_name, $attrdef = null )
	{
		if ( $attrdef )
			$this->_attrdefs[$attr_name] = $attrdef;
	
		return $this->_attrdefs[$attr_name];
	}

	/**
	 * Sets/gets validators for given attribute.
	 */
	public function validators( $attr_name, $vs = null )
	{
		if ( $vs )
			$this->_attrdefs[$attr_name]['validators'] = $vs;

		return $this->_attrdefs[$attr_name];
	}

	/**
	 * Adds a validator to given attribute, optionally with an identifying name.
	 * A name is needed for removing a validator with $this->del_validator().
	 * 
	 * The validator is either:
	 *
	 *	1. A valid PHP callable, or
	 *	2. 'this:<method_name>' which will be mapped to $this-><method_name>
	 *
	 * The validator will be called with three arguments:
	 *
	 *	<validator>( $self, $attr, $val )
	 *
	 * Where $self is the instance of the Validatable class, $attr is the 
	 * attribute definition, and $val is the current value.
	 */
	public function add_validator( $attr_name, $v, $v_name = null )
	{
		if ( $v_name )
			$this->_attrdefs[$attr_name]['validators'][$v_name] = $v;
		else
			$this->_attrdefs[$attr_name]['validators'][] = $v;
	}

	/**
	 * Removes named validator.
	 */
	public function del_validator( $attr_name, $v_name )
	{
		if ( isset( $this->_attrdefs[$attr_name]['validators'][$v_name] ) )
			unset( $this->_attrdefs[$attr_name]['validators'][$v_name] );
	}

	/**
	 * Performs validation and returns true iff object is valid.
	 *
	 * Validation errors can be retrieved via $this->errors().
	 */
	public function is_valid( $attr_name = null )
	{
		try {
			$this->validate( $attr_name );
		} catch ( Validation_error $e ) {
			# error messages have been set by $this->validate()
			return false;
		}

		return true;
	}

	/**
	 * Performs validation. Throws a Validation_error on validation failure, true
	 * on success.
	 *
	 * Validation errors can be retrieved via Validation_error::errors().
	 */
	public function validate( $attr_name = null )
	{
		debug( 'validatable: validating..' );


		$attrs =& $this->_attrdefs;
		if ( $attr_name )
			$attrs = array( $attr_name => $this->_attrdefs[$attr_name] );

		foreach ( $attrs as $n => $a ) {
			try {
				$this->_do_validate( $n );
			} catch ( Validation_error $e ) {
				debug( 'validatable: error validating %s', $n );
				$this->_errors[$n] = $e->errors();
			}
		}

		# if validating specific attribute, check for specific error
		if ( $attr_name && isset( $this->_errors[$attr_name] ) )
			throw new Validation_error( $this->_errors[$attr_name] );

		# if validating all, check for any error
		if ( !$attr_name && $this->_errors )
			throw new Validation_error( $this->_errors );

		# no errors, might be set before though, so need to empty
		if ( $attr_name )
			$this->_errors[$attr_name] = array();
		else
			$this->_errors = array();

		return true;
	}

	/**
	 * Sets/gets current errors as associative array attr_name => error msg
	 */
	public function errors( $errors = null )
	{
		if ( $errors )
			$this->_errors = $errors;

		return $this->_errors;
	}

	/**
	 * Returns error message for given attribute, null if none.
	 */
	public function error( $attr_name )
	{
		return isset( $this->_errors[$attr_name] ) ? $this->_errors[$attr_name] : null;
	}

	/**
	 * Adds given errors.
	 *
	 * @param	$errors	error messages: attr_name => error message
	 */
	public function add_errors( $errors )
	{
		$this->_errors = array_merge( $this->_errors, $errors );
	}

	#
	# helpers
	#
	
	/**
	 * Initializes attr.
	 */
	protected function _init_attrdef( $attr_name )
	{
		$attr =& $this->_check_exists( $attr_name );

		# save name shortcut in 'attr_name'
		$attr['attr_name'] = $attr_name;

		# name
		if ( !isset( $attr['name'] ) )
			$attr['name'] = ucfirst( $attr_name );

		# add defaults
		$attr = array_merge( $this->attrdef_default(), $attr );

		# enable shorthand 'validators' => array( 'validators' )
		# also makes sure that validators is always an array
		$attr['validators'] = (array) $attr['validators'];
	}

	/**
	 * Checks if attr exists and returns if so.
	 */
	protected function & _check_exists( $attr_name )
	{
		if ( !isset( $this->_attrdefs[$attr_name] ) )
		 	throw new InvalidArgumentException();

		return $this->_attrdefs[$attr_name];
	}

	/**
	 * Performs the actual validation.
	 */
	protected function _do_validate( $attr_name )
	{
		$attr =& $this->_check_exists( $attr_name );

		debug( 'validatable: validating %s', $attr_name );

		$vs = $this->_validators( $attr_name );

		foreach ( $vs as $n => $v ) {
			$this->_get_or_call( $v, array( $this, $attr, $this->$attr_name ) );
		}

		return true;
	}

	/**
	 * Gets value as string or as result of callback.
	 *
	 * Valid callbacks are PHP callbacks (function name, array w/ object/class 
	 * name + method name, function closure etc.) or the magic callback string
	 * 'this:<method>' that will be mapped to $this->method.
	 */
	protected function _get_or_call( $callback, $args = array() )
	{
		# magic 'this:'-callback?
		if (
			is_string( $callback ) &&
			substr( $callback, 0, 5 ) == 'this:'
		)
			$callback = array( $this, substr( $callback, 5 ) );

		if ( is_callable( $callback ) ) {
			debug( 'validatable: running callback %s', is_array( $callback ) ? $callback[1] : ( is_string( $callback ) ? $callback : '<closure>' ) );
			return call_user_func_array( $callback, $args ); 
		}
		return $callback;
	}

	/**
	 * Returns validators for given attribute.
	 *
	 * If the definition tells to add default validators (via 
	 * add_def_validators), the following validators will be added:
	 *
	 *	1. this:validator_<attr name>
	 *	2. this:validator_regexp
	 *	3. this:validator_empty
	 *	4. this:validator_<attr type>
	 */
	protected function _validators( $attr_name )
	{
		$attr =& $this->_check_exists( $attr_name );

		$vs = $attr['validators'];

		# add default?
		if ( $attr['add_def_validators'] ) {
			$vs = array(
				'this:validate_' . $attr_name,
				'regexp' => 'this:validator_regexp',
				'empty' => 'this:validator_empty',
				$attr['type'] => 'this:validator_' . $attr['type'],
			);

			# already set validators are not overwritten
			$vs = array_merge( $vs, $attr['validators'] );
		}

		return $vs;
	}

	/**
	 * Returns error message for given attribute and validator.
	 *
	 * Error messages will be nsprintf:d with the attribute definition plus
	 * 'value' => attribute value.
	 *
	 * Priority order:
	 *
	 *	1. $this->errmsg_for_<attr name>()
	 *	2. def: 'errmsg_<validator name>' (if validator name given)
	 *	3. $this->errmsg_<validator name>() (if validator name given) 
	 *	4. def: 'errmsg'
	 *	5. $this->errmsg()
	 */
	protected function _errmsg( $attr, $validator_name = null )
	{
		# allow for $attr as attribute name instead of attr def
		if ( !is_array( $attr ) )
			$attr =& $this->_check_exists( $attr );

		$attr_name = $attr['attr_name'];

		$msg = '';

		# $this->errmsg_for_<attr_name>()
		if ( method_exists( $this, "errmsg_for_${attr_name}" ) ) {
			$f = "errmsg_for_${attr_name}";
			$msg = $this->$f( $attr );

		# def. errmsg_<validator_name> if validator name given
		} elseif ( $validator_name && isset( $attr["errmsg_${validator_name}"] ) ) {
			# might be callback
			$msg = $this->_get_or_call( $attr["errmsg_${validator_name}"], array( $attr ) );

		# $this->errmsg_<validator_name>() if validator name given
		} elseif ( $validator_name && method_exists( $this, "errmsg_${validator_name}" ) ) {
			$f = "errmsg_${validator_name}";
			$msg = $this->$f( $attr );

		# def. errmsg if set
		//} elseif ( isset( $attr['errmsg'] ) ) {
			//# might be callback
			//$msg = $this->_get_or_call( $attr["errmsg"], array( $attr ) );

		# standard error message by default
		} else {
			$msg = $this->errmsg( $attr );
		}

		return sprintfn( $msg, $attr + array( 'value' => $this->$attr['attr_name'] ) );
	}

	#
	# Default validators
	#
	# The validator_* functions are used to validate custom named validators.
	#
	# For example, the 'empty' validator is mapped to validator_empty.
	#
	# Validators can be set on an individual attribute basis in the attribute
	# definition, and any such validators will override the default.
	#
	# See $this->_validators() for the code that determines what validators
	# to use for a given attribute.
	#
	
	/**
	 * Validates regexp.
	 */
	protected function validator_regexp( $v, $attr, $val )
	{
		if ( empty( $attr['regexp'] ) )
			return true;

		if ( !preg_match( $attr['regexp'], $val ) )
			throw new Validation_error( $this->_errmsg( $attr, 'regexp' ) );
		
		return true;
	}

	/**
	 * Validates empty (ensures that attr is non-empty)
	 */
	protected function validator_empty( $v, $attr, $val )
	{
		if ( empty( $attr['required'] ) )
			return true;

		debug( '%s: %s is required..', __CLASS__, $attr['attr_name'] );
		$set = true;
		switch ( $attr['type'] ) {
			# non-strings: only empty if null
			case 'timestamp':
			case 'int':
			case 'num':
			case 'bool':
				$set = $val !== null;
				break;

			# string types: empty if null, false or empty string
			default:
				# casting in for loop below won't catch nulls
				$set &= !empty( $val );
				foreach ( (array) $val as $v ) {
					$set &= is_string( $v ) && strlen( $v ) != 0;
				}
		}
		if ( !$set )
			throw new Validation_error( $this->_errmsg( $attr, 'empty' ) );
	}

	/**
	 * Helper function for the type validators. Basically checks if the attribute 
	 * value is an array, and if so checks that all array entries are valid.
	 */
	protected function _validate_using( $validator, $self, $attr, $val )
	{
		# always true if null (TODO OK?)
		if ( $val === null )
			return true;

		# (array) typecast for checking every value of $val if $val is array
		foreach ( (array) $val as $v ) {
			call_user_func( array( $this, $validator ), $self, $attr, $v, true );
		}

		return true;
	}

	/**
	 * Validates string.
	 */
	protected function validator_string( $self, $attr, $val, $perform = false )
	{
		if ( !$perform )
			$this->_validate_using( __FUNCTION__, $self, $attr, $val );
		else
			if ( $val !== '' && !validate_string( $val, $attr['min'], $attr['max'] ) )
				throw new Validation_error( $this->_errmsg( $attr, 'string' ) );
	}	

	/**
	 * Validates e-mail.
	 */
	protected function validator_email( $self, $attr, $val, $perform = false )
	{
		if ( !$perform )
			$this->_validate_using( __FUNCTION__, $self, $attr, $val );
		else
			if ( $val && !validate_email( $val ) )
				throw new Validation_error( $this->_errmsg( $attr, 'email' ) );
	}	

	/**
	 * Validates URL.
	 */
	protected function validator_url( $self, $attr, $val, $perform = false )
	{
		if ( !$perform )
			$this->_validate_using( __FUNCTION__, $self, $attr, $val );
		else
			if ( $val && !validate_url( $val ) )
				throw new Validation_error( $this->_errmsg( $attr, 'url' ) );
	}	

	/**
	 * Validates timestamp.
	 */
	protected function validator_timestamp( $self, $attr, $val, $perform = false )
	{
		if ( !$perform )
			$this->_validate_using( __FUNCTION__, $self, $attr, $val );
		else
			if ( !validate_int( $val, $attr['min'], $attr['max'] ) )
				throw new Validation_error( $this->_errmsg( $attr, 'timestamp' ) );
	}	

	/**
	 * Validates int.
	 */
	protected function validator_int( $self, $attr, $val, $perform = false )
	{
		if ( !$perform )
			$this->_validate_using( __FUNCTION__, $self, $attr, $val );
		else
			if ( $val !== '' && !validate_int( $val, $attr['min'], $attr['max'] ) )
				throw new Validation_error( $this->_errmsg( $attr, 'int' ) );
	}	

	/**
	 * Validates numeric.
	 */
	protected function validator_num( $self, $attr, $val, $perform = false )
	{
		if ( !$perform )
			$this->_validate_using( __FUNCTION__, $self, $attr, $val );
		else
			if ( $val !== '' && !validate_num( $val, $attr['min'], $attr['max'] ) )
				throw new Validation_error( $this->_errmsg( $attr, 'num' ) );
	}	

	/**
	 * Validates bool.
	 */
	protected function validator_bool( $self, $attr, $val, $perform = false )
	{
		if ( !$perform )
			$this->_validate_using( __FUNCTION__, $self, $attr, $val );
		else
			if ( $val === null )
				throw new Validation_error( $this->_errmsg( $attr, 'bool' ) );
	}	

	#
	# Default error messages
	#
	# errmsg_* functions are used to get error messages for named
	# validator errors. For example, the 'empty' validator gets its
	# error message from errmsg_empty.
	#
	# errmsg() returns default fallback error message.
	#
	# Note that error messages also can be set on an individual basis
	# in the attribute definition, and will if set take precedence over
	# the functions below.
	#
	# See $this->_errmsg() for fetching and precedence of error messages.
	#

	/**
	 * Default error message.
	 */
	protected function errmsg( $attr )
	{
		return "%(name)s is not valid (value: '%(value)s', type: %(type)s).";
	}
	
	/**
	 * Error message for empty attributes that are required to not be empty.
	 */
	protected function errmsg_empty( $attr )
	{
		return '%(name)s must not be empty.';
	}

	/**
	 * Error message for attributes that fail regexp validation.
	 */
	protected function errmsg_regexp( $attr )
	{
		return '%(name)s is not valid.';
	}
	
	/**
	 * Error message for attributes that fail string validation.
	 */
	protected function errmsg_string( $attr )
	{
		if ( $attr['min'] && $attr['max'] )
			return '%(name)s must be between %(min)s and %(max)s characters long.';
		elseif ( $attr['max'] )
			return '%(name)s must be at most %(max)s characters long.';
		elseif ( $attr['min'] )
			return '%(name)s must be at least %(min)s characters long.';
		else
			return '%(name)s is not a valid string.';
	}

	/**
	 * Error message for attributes that fail e-mail validation.
	 */
	protected function errmsg_email( $attr )
	{
		return '%(name)s is not a valid e-mail address.';
	}

	/**
	 * Error message for attributes that fail URL validation.
	 */
	protected function errmsg_url( $attr )
	{
		return '%(name)s is not a valid URL.';
	}

	/**
	 * Error message for attributes that fail timestamp validation.
	 */
	protected function errmsg_timestamp( $attr )
	{
		return '%(name)s is not a valid timestamp.';
	}

	/**
	 * Error message for attributes that fail integer validation.
	 */
	protected function errmsg_int( $attr )
	{
		if ( $attr['min'] && $attr['max'] )
			return '%(name)s must be between %(min)s and %(max)s.';
		elseif ( $attr['max'] )
			return '%(name)s must be less than %(max)s.';
		elseif ( $attr['min'] )
			return '%(name)s must be greater than %(min)s.';
		else
			return '%(name)s is not a valid integer.';
	}

	/**
	 * Error message for attributes that fail numeric validation.
	 */
	protected function errmsg_num( $attr )
	{
		if ( $attr['min'] && $attr['max'] )
			return '%(name)s must be between %(min)s and %(max)s.';
		elseif ( $attr['max'] )
			return '%(name)s must be less than %(max)s.';
		elseif ( $attr['min'] )
			return '%(name)s must be greater than %(min)s.';
		else
			return '%(name)s is not a valid number.';
	}

	/**
	 * Error message for attributes that fail boolean validation.
	 */
	protected function errmsg_bool( $attr )
	{
		return '%(name)s is not a valid bool.';
	}
}
