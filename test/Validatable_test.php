<?php

function debug( $msg, $arg1 = null ) {
	return;

	$args = func_get_args();

	if( $arg1 )
		$msg = @vsprintf( array_shift( $args ), $args );

	echo $msg . "\n";
}

require '../lib/form.php';

/** custom validator for testing validator callbacks */
function validator_func( $self, $attr, $val )
{
	if ( $val != 'aa' )
		throw new Validation_error( 'errvalidatorfunc' );
}

/**
 * Tests the Validatable class.
 */
class Validatable_test extends PHPUnit_Framework_TestCase
{
	public $v = null;

	/** custom validator for testing validator callbacks */
	public function validator_method( $self, $attr, $val )
	{
		if ( $val != 'aa' )
			throw new Validation_error( 'errvalidatormethod' );
	}

	public function setUp()
	{
		$this->v = new Validatable_class();
	}

	/** @dataProvider _test_validate_attr */
	public function test_validate_attr( $attr, $str, $ok, $errstr = null )
	{
		$this->v->$attr = $str;
		try {
			$this->v->validate( $attr );
			if ( !$ok )
				$this->fail( 'Exception not thrown for ' . $attr . ' = ' . $str );
		} catch ( Validation_error $e ) {
			if ( $ok )
				$this->fail( 'Exception thrown for ' . $attr . ' = ' . $str );
			$errmsg = $e->errors();
			if ( $errmsg != $errstr )
				$this->fail( 'Error message mismatch: expected "' . $errstr . '" but got "' . $errmsg . '"' );
		}
	}
	public function _test_validate_attr()
	{
		return array(

			# required attributes of all types
			
			array( 'attr_string', null, false, 'errempty' ),
			array( 'attr_string', false, false, 'errempty' ),
			array( 'attr_string', 0, false, 'errempty' ),
			array( 'attr_string', '', false, 'errempty' ),
			array( 'attr_string', 'a', true, 'errstring' ),
			array( 'attr_string', 'aa', true, 'errstring' ),

			array( 'attr_email', null, false, 'errempty' ),
			array( 'attr_email', false, false, 'errempty' ),
			array( 'attr_email', 0, false, 'errempty' ),
			array( 'attr_email', '', false, 'errempty' ),
			array( 'attr_email', 'a', false, 'erremail' ),
			array( 'attr_email', 'user@host.com', true, 'erremail' ),

			array( 'attr_url', null, false, 'errempty' ),
			array( 'attr_url', false, false, 'errempty' ),
			array( 'attr_url', 0, false, 'errempty' ),
			array( 'attr_url', '', false, 'errempty' ),
			array( 'attr_url', 'a', false, 'errurl' ),
			array( 'attr_url', 'http://host.com', true, 'errurl' ),

			array( 'attr_int', null, false, 'errempty' ),
			array( 'attr_int', false, false, 'errint' ),
			array( 'attr_int', 0.5, false, 'errint' ),
			array( 'attr_int', '0.5', false, 'errint' ),
			array( 'attr_int', 0.0, true ),
			array( 'attr_int', '0.0', true ),
			array( 'attr_int', 0, true ),
			array( 'attr_int', '0', true ),
			array( 'attr_int', 1, true ),
			array( 'attr_int', '1', true ),

			array( 'attr_num', null, false, 'errempty' ),
			array( 'attr_num', false, false, 'errnum' ),
			array( 'attr_num', 0.0, true ),
			array( 'attr_num', '0.0', true ),
			array( 'attr_num', 0, true ),
			array( 'attr_num', '0', true ),
			array( 'attr_num', 1, true ),
			array( 'attr_num', '1', true ),

			array( 'attr_bool', null, false, 'errempty' ),
			array( 'attr_bool', false, true ),
			array( 'attr_bool', 0.0, true ),
			array( 'attr_bool', '0.0', true ),
			array( 'attr_bool', 0, true ),
			array( 'attr_bool', '0', true ),
			array( 'attr_bool', 1, true ),
			array( 'attr_bool', '1', true ),

			array( 'attr_timestamp', null, false, 'errempty' ),
			array( 'attr_timestamp', false, false, 'errtimestamp' ),
			array( 'attr_timestamp', 0.5, false, 'errtimestamp' ),
			array( 'attr_timestamp', '0.5', false, 'errtimestamp' ),
			array( 'attr_timestamp', -1.0, true ),
			array( 'attr_timestamp', '-1.0', true ),
			array( 'attr_timestamp', 0, true ),
			array( 'attr_timestamp', '0', true ),
			array( 'attr_timestamp', 1, true ),
			array( 'attr_timestamp', '1', true ),
			
			# min and max limits (min: 2: max: 1, min/max: 2/3)
			
			array( 'attr_string_min', null, true, 'errstring' ), # empty OK
			array( 'attr_string_min', '', true, 'errstring' ), # empty OK
			array( 'attr_string_min', 'a', false, 'errstring' ),
			array( 'attr_string_min', 'aa', true ),
			array( 'attr_string_min', 'aaa', true ),

			array( 'attr_string_max', '', true ),
			array( 'attr_string_max', 'a', true ),
			array( 'attr_string_max', 'aa', false, 'errstring' ),
			array( 'attr_string_max', 'aaa', false, 'errstring' ),

			array( 'attr_string_min_max', null, true, 'errstring' ), # empty OK
			array( 'attr_string_min_max', '', true, 'errstring' ), # empty OK
			array( 'attr_string_min_max', 'a', false, 'errstring' ),
			array( 'attr_string_min_max', 'aa', true ),
			array( 'attr_string_min_max', 'aaa', true ),
			array( 'attr_string_min_max', 'aaaaa', false, 'errstring' ),
			array( 'attr_string_min_max', 'aaaaaa', false, 'errstring' ),

			array( 'attr_int_min', 0, false, 'errint' ),
			array( 'attr_int_min', 1, false, 'errint' ),
			array( 'attr_int_min', 2, true ),
			array( 'attr_int_min', 3, true ),

			array( 'attr_int_max', 0, true ),
			array( 'attr_int_max', 1, true ),
			array( 'attr_int_max', 2, false, 'errint' ),
			array( 'attr_int_max', 3, false, 'errint' ),

			array( 'attr_int_min_max', 0, false, 'errint' ),
			array( 'attr_int_min_max', 1, false, 'errint' ),
			array( 'attr_int_min_max', 2, true ),
			array( 'attr_int_min_max', 3, true ),
			array( 'attr_int_min_max', 4, false, 'errint' ),
			array( 'attr_int_min_max', 5, false, 'errint' ),

			array( 'attr_num_min', 0, false, 'errnum' ),
			array( 'attr_num_min', 1.999, false, 'errnum' ),
			array( 'attr_num_min', 2.000, true ),
			array( 'attr_num_min', 3, true ),

			array( 'attr_num_max', 0, true ),
			array( 'attr_num_max', 0.999, true ),
			array( 'attr_num_max', 1.001, false, 'errnum' ),
			array( 'attr_num_max', 2, false, 'errnum' ),

			array( 'attr_num_min_max', 0, false, 'errnum' ),
			array( 'attr_num_min_max', 1.999, false, 'errnum' ),
			array( 'attr_num_min_max', 2, true ),
			array( 'attr_num_min_max', 2.999, true ),
			array( 'attr_num_min_max', 3, true ),
			array( 'attr_num_min_max', 3.001, false, 'errnum' ),
			array( 'attr_num_min_max', 4, false, 'errnum' ),


			# regexpes

			array( 'attr_regexp', '', false, 'errregexp' ),
			array( 'attr_regexp', 'a', false, 'errregexp' ),
			array( 'attr_regexp', 'aa', true ),
			array( 'attr_regexp', 'aaa', false, 'errregexp' ),


			# custom

			array( 'attr_custom', '', false, 'errcustom' ),
			array( 'attr_custom', 'a', false, 'errcustom' ),
			array( 'attr_custom', 'aa', true ),
			array( 'attr_custom', 'aaa', false, 'errcustom' ),

			array( 'attr_custom_no_def', '', false, 'errcustom' ),
			array( 'attr_custom_no_def', 'a', false, 'errcustom' ),
			array( 'attr_custom_no_def', 'aa', true ),
			array( 'attr_custom_no_def', 'aaa', false, 'errcustom' ),

			# custom validator class methods
			
			array( 'attr_custom_class_method', '', false, 'errcustom' ),
			array( 'attr_custom_class_method', 'a', false, 'errcustom' ),
			array( 'attr_custom_class_method', 'aa', true ),
			array( 'attr_custom_class_method', 'aaa', false, 'errcustom' ),

			array( 'attr_custom_class_method_no_def', '', false, 'errcustom' ),
			array( 'attr_custom_class_method_no_def', 'a', false, 'errcustom' ),
			array( 'attr_custom_class_method_no_def', 'aa', true ),
			array( 'attr_custom_class_method_no_def', 'aaa', false, 'errcustom' ),

			
			# custom error messages
			
			array( 'attr_custom_errmsg_callback', '', false, 'errempty' ),
			array( 'attr_custom_errmsg_callback', 'a', false, 'errcustom' ),
			array( 'attr_custom_errmsg_callback_this', '', false, 'errempty' ),
			array( 'attr_custom_errmsg_callback_this', 'a', false, 'errcustom' ),
			
			array( 'attr_custom_errmsg_string', '', false, 'erroverride_empty' ),
			array( 'attr_custom_errmsg_string', 'a', false, 'erroverride_string' ),

			array( 'attr_custom_errmsg_class_method', '', false, 'erroverride' ),
			array( 'attr_custom_errmsg_class_method', 'a', false, 'erroverride' ),
		);
	}

	/** @dataProvider _test_add_validator */
	public function test_add_validator( $attr_name, $validator, $validator_name, $val, $ok, $errstr = null )
	{
		$this->v->add_validator( $attr_name, $validator, $validator_name );
		$this->test_validate_attr( $attr_name, $val, $ok, $errstr );
	}
	public function _test_add_validator()
	{
		return array(
			# errempty is still used for empty check
			array( 'attr_string', 'this:validator_callback', 'custom', '', false, 'errempty' ),
			array( 'attr_string', 'this:validator_callback', 'custom', 'a', false, 'errcustom' ),
			array( 'attr_string', 'this:validator_callback', 'custom', 'aa', true ),

			# not required -> errempty won't trigger
			array( 'attr_string_not_req', 'this:validator_callback', 'custom', '', false, 'errcustom' ),
			array( 'attr_string_not_req', 'this:validator_callback', 'custom', 'a', false, 'errcustom' ),
			array( 'attr_string_not_req', 'this:validator_callback', 'custom', 'aa', true ),
			
			# no default validators -> errempty won't trigger
			array( 'attr_string_no_def', 'this:validator_callback', 'custom', '', false, 'errcustom' ),
			array( 'attr_string_no_def', 'this:validator_callback', 'custom', 'a', false, 'errcustom' ),
			array( 'attr_string_no_def', 'this:validator_callback', 'custom', 'aa', true ),

			
			# callbacks
			array( 'attr_string_no_def', function( $self, $attr, $val ) { if ( $val != 'aa' ) throw new Validation_error( 'errcustomcb' ); }, 'custom', '', false, 'errcustomcb' ),
			array( 'attr_string_no_def', function( $self, $attr, $val ) { if ( $val != 'aa' ) throw new Validation_error( 'errcustomcb' ); }, 'custom', 'aa', true ),

			# function name as callback
			array( 'attr_string_no_def', 'validator_func', 'custom', '', false, 'errvalidatorfunc' ),
			array( 'attr_string_no_def', 'validator_func', 'custom', 'aa', true ),
			
			# method as callback
			array( 'attr_string_no_def', array( $this, 'validator_method' ), 'custom', '', false, 'errvalidatormethod' ),
			array( 'attr_string_no_def', array( $this, 'validator_method' ), 'custom', 'aa', true ),
		);
	}
}

class Validatable_class extends Validatable
{
	protected function attrdefs()
	{
		return array(
			'attr_string' => array(
				'type' => 'string',
				'required' => true,
			),
			'attr_string_not_req' => array(
				'type' => 'string',
				'required' => false,
			),
			'attr_string_no_def' => array(
				'type' => 'string',
				'required' => true,
				'add_def_validators' => false,
			),
			'attr_email' => array(
				'type' => 'email',
				'required' => true,
			),
			'attr_url' => array(
				'type' => 'url',
				'required' => true,
			),
			'attr_int' => array(
				'type' => 'int',
				'required' => true,
			),
			'attr_num' => array(
				'type' => 'num',
				'required' => true,
			),
			'attr_bool' => array(
				'type' => 'bool',
				'required' => true,
			),
			'attr_timestamp' => array(
				'type' => 'timestamp',
				'required' => true,
			),

			# string length restrictions
			'attr_string_min' => array(
				'type' => 'string',
				'min' => 2,
			),
			'attr_string_max' => array(
				'type' => 'string',
				'max' => 1,
			),
			'attr_string_min_max' => array(
				'type' => 'string',
				'min' => 2,
				'max' => 3,
			),
			
			# int interval restrictions
			'attr_int_min' => array(
				'type' => 'int',
				'min' => 2,
			),
			'attr_int_max' => array(
				'type' => 'int',
				'max' => 1,
			),
			'attr_int_min_max' => array(
				'type' => 'int',
				'min' => 2,
				'max' => 3,
			),
			
			# num interval restrictions
			'attr_num_min' => array(
				'type' => 'num',
				'min' => 2,
			),
			'attr_num_max' => array(
				'type' => 'num',
				'max' => 1,
			),
			'attr_num_min_max' => array(
				'type' => 'num',
				'min' => 2,
				'max' => 3,
			),

			# regexp
			'attr_regexp' => array(
				'type' => 'string',
				'regexp' => '/^aa$/',
			),


			# custom validators
			
			# custom (with defaults)
			'attr_custom' => array(
				'type' => 'string',
				'errmsg' => 'errcustom',
				'validators' => function( $o, $attr, $val ) { if ( $val != 'aa' ) throw new Validation_error( $attr['errmsg'] ); },
			),
			
			# custom (without defaults - type check will be ignored)
			'attr_custom_no_def' => array(
				'type' => 'int',
				'add_def_validators' => false,
				'errmsg' => 'errcustom',
				'validators' => function( $o, $attr, $val ) { if ( $val != 'aa' ) throw new Validation_error( $attr['errmsg'] ); },
			),

			# with custom validator function as class method
			'attr_custom_class_method' => array(
				'type' => 'string',
			),
		
			# with custom validator function as class method
			'attr_custom_class_method_no_def' => array(
				'type' => 'string',
			),


			# custom error messages via defs and methods
			
			# override errmsg
			'attr_custom_errmsg' => array(
				'type' => 'string',
				'errmsg' => 'erroverride',
			),

			# use callback
			'attr_custom_errmsg_callback' => array(
				'type' => 'string',
				'required' => true,
				'min' => 2,
				'errmsg_string' => function() { return 'errcustom'; },
			),
			'attr_custom_errmsg_callback_this' => array(
				'type' => 'string',
				'required' => true,
				'min' => 2,
				'errmsg_string' => 'this:errmsg_callback',
			),

			# override errmsg w/ errmsg_string
			'attr_custom_errmsg_string' => array(
				'type' => 'string',
				'errmsg' => 'errmsg',
				'errmsg_empty' => 'erroverride_empty',
				'errmsg_string' => 'erroverride_string',
				'required' => true,
				'min' => 2,
			),

			# override all w/ method
			'attr_custom_errmsg_class_method' => array(
				'type' => 'string',
				'errmsg' => 'errmsg',
				'errmsg_string' => 'errmsg',
				'required' => true,
				'min' => 2,
			),
		);
	}

	# custom validator
	protected function validate_attr_custom_class_method( $self, $attr, $val )
	{
		if ( $val != 'aa' )
			throw new Validation_error( 'errcustom' );
	}
	
	# custom validator
	protected function validate_attr_custom_class_method_no_def( $self, $attr, $val )
	{
		if ( $val != 'aa' )
			throw new Validation_error( 'errcustom' );
	}

	# custom validator
	protected function validator_callback( $self, $attr, $val )
	{
		if ( $val != 'aa' )
			throw new Validation_error( 'errcustom' );
	}

	# custom errmsgs
	protected function errmsg_for_attr_custom_errmsg_class_method()
	{
		return 'erroverride';
	}

	protected function errmsg_callback()
	{
		return 'errcustom';
	}

	protected function errmsg( $attr )
	{
		return 'errmsg';
	}

	protected function errmsg_empty( $attr )
	{
		return 'errempty';
	}

	protected function errmsg_regexp( $attr )
	{
		return 'errregexp';
	}

	protected function errmsg_string( $attr )
	{
		return 'errstring';
	}

	protected function errmsg_email( $attr )
	{
		return 'erremail';
	}

	protected function errmsg_url( $attr )
	{
		return 'errurl';
	}

	protected function errmsg_timestamp( $attr )
	{
		return 'errtimestamp';
	}

	protected function errmsg_int( $attr )
	{
		return 'errint';
	}

	protected function errmsg_num( $attr )
	{
		return 'errnum';
	}

	protected function errmsg_bool( $attr )
	{
		return 'errbool';
	}
}
