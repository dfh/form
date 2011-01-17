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
 * Tpl - A minimalistic templating engine.
 *
 * Rendering is done bottom-first. Meaning context can be set in inner 
 * templates to be used in outer, but not vice versa.
 */
class Tpl
{
	/** Default template directory. */
	static $default_template_dir = '/';

	/**
	 * Default context. Use to add stuff that should be available to every 
	 * rendered template.
	 */
	public static $default_context = array();

	/** Holds context. */
	protected $context = array();

	/** Holds template (file path, function or string). */
	protected $tpl = null;

	/** Holds current template directory. */
	private $template_dir = null;

	/**
	 * Creates and returns a new instance of a Tpl.
	 *
	 * Neat chaining:
	 *
	 *   Tpl::create('outer.html.php')->
	 *     wrapping('inner.html.php')->
	 *       get();
	 */
	public static function create( $template, &$context = array(), $template_dir = null )
	{
		$context = array_merge( static::$default_context, (array) $context );

		return new Tpl( $template, $context, $template_dir );
	}

	/**
	 * Creates a new template.
	 */
	public function __construct( $template, & $context = array(), $template_dir = null )
	{
		if ( $template_dir )
			$this->template_dir = $template_dir;
		else 
			$this->template_dir = self::$default_template_dir;

		$this->tpl = $template;
		$this->context = $context;
	}

	/**
	 * Creates a child of this template, sharing context with this template.
	 */
	public function child( $tpl )
	{
		if ( !( $tpl instanceof self ) )
			$tpl = self::create( $tpl );

		# copy context so that the child gets the parent's context.
		# on overrides the childs context win
		foreach ( $tpl->context as $k => $v ) {
			$this->context[$k] =& $tpl->context[$k];
		}
		$tpl->context =& $this->context;

		return $tpl;
	}

	/**
	 * Sets/gets context.
	 */
	public function context( &$c = array() )
	{
		if ( func_num_args() == 1 )
			$this->context = $c;

		return $this->context;
	}

	/**
	 * Wraps another template in this one. The wrapped template will be rendered, 
	 * and the result will be set to $this->content.
	 *
	 * For example:
	 *
	 *   $t = Tpl::create( 'layout.html.php', $ctxt );
	 *   $r = $t->wrapping( 'inner.html.php' );
	 *   echo $r->get();
	 */
	public function wrapping( $tpl )
	{
		$tpl = $this->child( $tpl );

		$this->content = $tpl->get();

		return $this;
	}

	/**
	 * Gets context variable.
	 */
	public function __get( $var )
	{
		if ( isset( $this->context[$var] ) )
			return $this->context[$var];
		else
			return null;
	}

	/**
	 * Sets context variable.
	 */
	public function __set( $var, $val )
	{
		$this->context[$var] = $val;
	}

	/**
	 * Parses template and returns the string representation.
	 *
	 * Exactly what is done depends on $this->tpl:
	 *
	 *   1. $this->tpl is callable, it will be called w/ current context.
	 *   2. $this->tpl is a readable filename, it will be included
	 *      in outbut buffering. Template context will be available through
	 *      $this-><context_variable>.
	 *   3. $this->tpl will be used as a string.
	 */
	public function get( &$context = array() )
	{
		$context = (array) $context;

		debug( "Tpl::get '%s'", $this->tpl );

		$res = null;
		$ctxt = $this->context;
		$context += $ctxt;

		if ( is_callable( $this->tpl ) ) {
			debug( "Tpl::get rendering function" );
			$f = $this->tpl; # PHP, I love you, but sometimes you're just driving me crazy
			$res = $f( $this );
		} elseif ( is_readable( $this->template_dir . $this->tpl ) ) {
			debug( "Tpl::get rendering file: {$this->tpl}" );
			ob_start();
			$r = require $this->template_dir . $this->tpl;
			$res = ob_get_clean();
		} else {
			debug( "Tpl::get rendering string" );
			$res = $this->tpl;
		}

		return $res;
	}

	/**
	 * toString = get, mucho handy!
	 */
	public function __toString()
	{
		return $this->get();
	}
}
