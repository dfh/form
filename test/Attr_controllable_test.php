<?php

require '../lib/form.php';

/**
 * Tests Attr_controllable.
 */
class Attr_controllable_test extends PHPUnit_Framework_TestCase
{
	protected $a;

	public function setUp()
	{
		$this->a = new Attr_controllable();
	}

	public function tearDown()
	{
		$this->a = null;
	}

	public function test_simple_set_get()
	{
		$this->a->a = 'a';

		$this->assertEquals( 'a', $this->a->a );

		# get access of unset attrs -> null
		$this->assertEquals( null, $this->a->c );

		$this->assertEquals( 'a', $this->a->a() );

		# call access of unset attrs -> null
		$this->assertEquals( null, $this->a->c() );

		# set reference
		$o = 'o';
		$this->a->o = null;
		$this->a->o =& $o;
		$o = 'k';
		$this->assertEquals( 'k', $this->a->o );
	}

	public function test_get_set_callback()
	{
		$this->a->a = function() { return 'a'; };
		$this->assertEquals( 'a', $this->a->a() );

		$this->a->b = array( 'Attr_controllable_test', 'testfunc' );
		$this->assertEquals( 't', $this->a->b() );
	}

	public static function testfunc()
 	{
		return 't';
	}

	public function test_array()
	{
		$this->a->a = array( 'one', 'two' );
		$this->assertEquals( 'one', $this->a->a[0] );
		$this->assertEquals( 'two', $this->a->a[1] );

		$a = array( 'three', 'four' );
		$this->a->a =& $a;
		$this->assertEquals( 'three', $this->a->a[0] );
		$this->assertEquals( 'four', $this->a->a[1] );
		$a[0] = 'five';
		$this->assertEquals( 'five', $this->a->a[0] );
	}

	public function test_act_as_map()
	{
		$this->a->a = array( 'a', 'b' );

		$this->a->a(0, 'b');
		$this->assertEquals( 'a', $this->a->a[0] );

		$this->a->act_as_map('a');
		$this->a->a(0, 'b');
		$this->assertEquals( 'b', $this->a->a(0) );

		$this->assertEquals( null, $this->a->a(2) );
	}
}
