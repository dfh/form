<?php

/**
 * Functionality for guarding against XSRF attacks.
 */
class Xsrf_guard
{
	public static $default_token_generator = array( __CLASS__, 'default_token_generator' );
	public static $default_token_validator = array( __CLASS__, 'default_token_validator' );

	protected $key = '';
	protected $userdata = '';
	protected $timeout = 900;
	protected $field_name = '__xsrf_guard';
	protected $error;
	protected $hash_alg = 'sha256';
	protected $now;

	protected $token_generator;
	protected $token_validator;

	public function __construct()
	{
	}

	public function key( $key = null )
	{
		$key and 
			$this->key = $key;

		return $this->key;
	}

	public function userdata( $userdata = null )
	{
		$userdata and
			$this->userdata = $userdata;

		return $this->userdata;
	}

	public function timeout( $timeout = null )
	{
		$timeout and 
			$this->timeout = $timeout;

		return $this->timeout;
	}

	public function field_name( $field_name = null )
	{
		$field_name and
			$this->field_name = $field_name;

		return $this->field_name;
	}

	public function error()
	{
		return $this->error;
	}

	public function hash_alg( $hash_alg = null )
	{
		$hash_alg and
			$this->hash_alg = $hash_alg;

		return $this->hash_alg;
	}

	public function now( $now = null )
	{
		$now and
			$this->now = $now;

		return $this->now;
	}

	public function token_generator( $callback = null )
	{
		$callback and
			$this->token_generator = $callback;

		return $this->token_generator;
	}

	public function token_validator( $callback = null )
	{
		$callback and
			$this->token_validator = $callback;

		return $this->token_validator;
	}

	# get_token: if no token generators, use default
	public function token()
	{
		if ( is_callable( $this->token_generator ) )
			$f = $this->token_generator;
		else
			$f = self::$default_token_generator;

		return call_user_func( $f, $this );
	}

	# validate: if no token validators, use default
	public function validate( $token )
	{
		if ( is_callable( $this->token_validator ) )
			$f = $this->token_validator;
		else
			$f = self::$default_token_validator;

		return call_user_func( $f, $token, $this );
	}	

	public function is_valid( $token )
	{
		try {
			$this->validate();
		} catch ( Validation_error $e ) {
			$this->error( $e->error() );
			return false;
		}

		return true;
	}	

	public static function default_token_generator( $self )
	{
		$token = base64_encode(
			hash( $self->hash_alg(), $self->key() . ":" . $self->now() ) .
			":" . $self->now()
		);

		return $token;
	}

	public static function default_token_validator( $token, $self )
	{
		$token = base64_decode( $token );
		$parts = explode( ':', $token );

		if ( count( $parts ) != 2 )
			throw new Validation_error( 'Invalid token syntax!' );

		list( $hash, $token_time ) = $parts;

		if ( $token_time + $self->timeout() < $self->now() )
			throw new Validation_error( 'Token died of old age and is no good around here anymore!' );

		$ref_hash = hash( $self->hash_alg(), $self->key() . ":$token_time" );
		if ( $hash !== $ref_hash )
			throw new Validation_error( 'Looks like somebody tinkered that token, boy!' );

		return true;
	}
}
