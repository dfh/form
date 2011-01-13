<?php

/**
 * Form base.
 */
abstract class Form extends Validatable
{
	# NOTE these names can not be used as field names
	public $action = '';
	public $method = 'post';
	public $accept_charset = 'utf-8';
	public $enctype = '';

	/**
	 * Field definitions.
	 */
	protected $_fields = array();

	/**
	 * Fieldsets (name => array w/ field names)
	 */
	protected $_fieldsets = array();

	/**
	 * Returns field definitions.
	 */
	protected function fields()
	{
		return array();
	}

	/**
	 * Returns field order, used when rendering
	 */
	public function fields_order()
	{
		return array_keys( $this->_fields );
	}

	/**
	 * Returns fieldsets for this form.
	 */
	public function fieldsets()
	{
		return array();
	}

	/**
	 * Returns fieldsets order, used when rendering.
	 */
	public function fieldsets_order()
	{
		return array_keys( $this->_fieldsets );
	}

	protected function field_default()
	{
		return array(
			'values' => null,
			'add_default_def' => false,
			'renderer' => null,
		);
	}

	/**
	 * Creates a new form.
	 */
	public function __construct()
	{
		if ( !$this->_fields )
			$this->_fields = $this->fields();

		# Validatable uses _valdefs, use the same "namespace" for the fields
		$this->_attrdefs =& $this->_fields;

		foreach ( $this->_fields as $name => $spec ) {
			$this->_init_field( $name );
		}

		parent::__construct();
	}

	protected function default_submit()
	{
		return array(
			'type' => 'string',
			'add_default_validators' => false,
			'renderer' => 'submit',
		);
	}

	protected function default_abort()
	{
		return array(
			'type' => 'string',
			'label' => 'Go back',
			'add_default_validators' => false,
			'renderer' => 'abort',
		);
	}

	/** Returns field info for given field and key. */
	public function field_info( $field, $key = null )
	{
		if ( $key ) {
			return $this->_fields[$field][$key];
		} else {
			return $this->_fields[$field];
		}
	}

	/** Returns true iff this form has a field named $field. */
	public function has_field( $field )
	{
		return isset( $this->_fields[$field] );
	}

	/** Use given source for field values. */
	public function source( & $source )
	{
		foreach ( $this->_fields as $f => $s ) {
			$this->$f = null;
			$this->$f =& $source[$f];
		}
	}


	#
	# Helpers
	#
	
	/**
	 * Initializes field.
	 */
	protected function _init_field( $field_name )
	{
		$field =& $this->_check_exists( $field_name );

		# use 'key' => '' as shorthand for adding defaults
		if ( !is_array( $field ) )
			$field = array( 'add_default_def' => true );

		# add defaults
		$field = array_merge( $this->field_default(), $field );
		
		# shortcut to field name in spec
		$field['field_name'] = $field_name;

		# readable field name (uppercased field name with _ -> spaces
		if ( !isset( $field['name'] ) ) 
			$field['name'] = ucfirst( str_replace( '_', ' ', $field['field_name'] ) );

		# merge in default field values if told so
		if ( $field['add_default_def'] && method_exists( $this, 'default_' . $field_name ) ) {
			$f = "default_${field_name}";
			$field = array_merge( $this->$f, $field );
		}

		# html label
		if ( !isset( $field['label'] ) )
			$field['label'] = ucfirst( $field['name'] );

		# html id
		if ( !isset( $field['id'] ) )
			$field['id'] = $field['field_name'];

		# help message
		if ( !isset( $field['help_msg'] ) )
			$field['help_msg'] = '';

		# default data? TODO move??
		if ( isset( $field['default'] ) && !isset( $this->$field_name ) )
			$this->$field_name = $this->_get_or_call( $field['default'] );
	}

	/**
	 * Returns renderer callback for given field.
	 */
	protected function _renderer( $field_name )
	{
		$field =& $this->_check_exists( $field_name );

		# explicit renderer
		if ( $field['renderer'] )
			return $field['renderer'];

		elseif ( isset( $field['render'] ) )
			return array( $this, 'render_' . $field['render'] );

		# $this->render_<field name>
		else
		 return array( $this, "render_${field_name}" );	
	}

	#
	# Rendering
	#
	
	/** Renders the form */
	public function render( $ctxt = array() )
	{
		$res = '';
		foreach ( $this->fields_order() as $name ) {
			$res .= $this->render_field( $name, $ctxt );
		}

		# wrap
		return $this->_render_form( $res, $ctxt );
	}

	/** Renders a field */
	public function render_field( $field_name, $ctxt = array() )
	{
		$field =& $this->_check_exists( $field_name );

		var_dump( $this->_renderer( $field_name ) );

		return $this->_get_or_call(
			$this->_renderer( $field_name ),
			array( $this, $field, $this->$field_name, $ctxt )
		);
	}

	/** Renders template */
	protected function _render_template( $self, $field, $val, $tpl = '', $ctxt = array() )
	{
		$ctxt += $field;
		$ctxt += array(
			'form' => $self,
			'field' => $field,
			'value' => $val,
			'error' => $form->error( $field['field_name'] ),
		);
		return Tpl::create( $tpl, $ctxt )->get();
	}

	protected function render_string( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/text_field.html.php', $ctxt );
	}

	protected function render_email( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/email_field.html.php' , $ctxt);
	}

	protected function render_password( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/password_field.html.php' , $ctxt);
	}

	protected function render_radio( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/radio.html.php' , $ctxt);
	}

	protected function render_submit( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/submit.html.php' , $ctxt);
	}

	protected function render_hidden( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/hidden_field.html.php' , $ctxt);
	}

	protected function render_file( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/file_field.html.php' , $ctxt);
	}

	protected function render_select( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/select.html.php' , $ctxt);
	}

	protected function render_textarea( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/textarea.html.php' , $ctxt);
	}

	protected function render_chk( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'form/checkbox.html.php' , $ctxt);
	}

	/** Renders the form wrapper */
	protected function _render_form( $content, $ctxt = array() )
	{
		$ctxt += array(
			'content' => $content,
			'action' => $this->action,
			'method' => $this->method,
			'enctype' => $this->enctype,
			'accept_charset' => $this->accept_charset,
		);

		return Tpl::create( 'form/form.html.php', $ctxt )->get();
	}
}
