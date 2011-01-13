<?php

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
