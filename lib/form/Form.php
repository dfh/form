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
 * Form base.
 *
 * Form is a lightweight framework for defining, validating and rendering
 * HTML forms.
 *
 * In it's simplest form:
 *
 *   class Simple_form extends Form
 *   {
 *     protected function fields()
 *     {
 *       return array(
 *         'name' => array(
 *           'required' => true,
 *          ),
 *          'submit' => true,
 *       ),
 *     }
 *   }
 */
abstract class Form extends Validatable
{
	/** Default template directory. */
	public static $default_template_dir = '';

	/** Template directory. */
	protected $template_dir;

	/** Form action. */
	public $form_action = '';

	/** Form method. */
	public $form_method = 'post';

	/** Form accept-charset. */
	public $form_accept_charset = '';

	/** Form enctype. */
	public $form_enctype = '';

	/**
	 * Holds field definitions.
	 */
	protected $_fields = array();

	/**
	 * Holds fieldsets as an associative array:
	 *
	 *	fieldset name => array with field names
	 */
	protected $_fieldsets = array();

	/**
	 * Returns field definitions as an associative array:
	 *
	 *	field name => field definition
	 *
	 * The field definition is an array with the following
	 * possible keys:
	 *
	 * name: string
	 *	readable name
	 *	defaults to uppercased field name w/ underscores => spaces
	 *
	 * label: string
	 *	readable label
	 *	defaults to name
	 *
	 * id: string
	 *	HTML id
	 *	defaults to field name
	 *
	 * help_msg: string
	 *	help message
	 *	defaults to ''
	 *
	 * add_default_def: bool
	 *	if true, fill with preset default definition based on the field name.
	 *	defaults to false
	 *
	 * values: array or callback
	 *	valid values, if applicable. for selects etc.
	 *	defaults to null
	 *
	 * value: mixed (callback allowed)
	 *	value to use for field (for hidden fields and default values)
	 *	defaults to null
	 *
	 * render_as: string
	 *	shorthand for specifying renderer ('select', 'file', 'checkbox' etc.)
	 *	maps to a $this->renderer_<value>().
	 *	defaults to 'text'
	 *
	 * renderer: string or callback
	 *	specifies renderer to render the field with. if string, interpreted
	 *	as a template filename.
	 *	defaults to null (which will use the default render_as)
	 */
	protected function fields()
	{
		return array();
	}

	/**
	 * Returns array with field names indicating field order,
	 * used when rendering.
	 */
	public function fields_order()
	{
		return array_keys( $this->_fields );
	}

	/**
	 * Returns fieldsets for this form, as associative array
	 *
	 *	fieldset name => array with field names
	 */
	public function fieldsets()
	{
		return array();
	}

	/**
	 * Returns array with fieldset names indicating fieldset order,
	 * used when rendering.
	 */
	public function fieldsets_order()
	{
		return array_keys( $this->_fieldsets );
	}

	/**
	 * Returns default values to be added to each field.
	 */
	protected function field_default()
	{
		return array(
			'values' => null,
			'render_as' => 'text',
			'renderer' => null,
		);
	}

	/**
	 * Creates a new Form.
	 */
	public function __construct( $template_dir = null )
	{
		if ( $template_dir )
			$this->template_dir = $template_dir;
		else
			$this->template_dir = self::$default_template_dir;

		if ( !$this->_fields )
			$this->_fields = $this->fields();

		# Validatable uses _valdefs, use the same "namespace" for the fields
		$this->_attrdefs =& $this->_fields;

		foreach ( $this->_fields as $name => $spec ) {
			$this->_init_field( $name );
		}

		parent::__construct();
	}

	/**
	 * Default values for field named 'submit'.
	 */
	protected function default_submit()
	{
		return array(
			'type' => 'string',
			'add_default_validators' => false,
			'render_as' => 'submit',
		);
	}

	/**
	 * Default values for field named 'abort'.
	 */
	protected function default_abort()
	{
		return array(
			'type' => 'string',
			'label' => 'Go back',
			'add_default_validators' => false,
			'render_as' => 'abort',
		);
	}

	/**
	 * Returns field info for given field and key.
	 *
	 * @see $this->fields()
	 */
	public function field_info( $field, $key = null )
	{
		if ( $key ) {
			return $this->_fields[$field][$key];
		} else {
			return $this->_fields[$field];
		}
	}

	/**
	 * Returns true iff this form has a field named $field.
	 */
	public function has_field( $field )
	{
		return isset( $this->_fields[$field] );
	}

	/**
	 * Use given source for field values.
	 *
	 * For example, for using POST data:
	 *
	 *	$form->source( $_POST );
	 */
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
	 * Initializes field with given name.
	 *
	 * If field flag 'add_default_def' is true, default field flags
	 * from $this->default_<field name>() will be merged to the field
	 * (if the method exists). Already set flags will not be overwritten.
	 *
	 * After merging as described above, default field flags will be
	 * merged in the same way, using $this->field_defaults().
	 */
	protected function _init_field( $field_name )
	{
		$field =& $this->_check_exists( $field_name );

		# use 'key' => '' as shorthand for adding defaults
		if ( !is_array( $field ) )
			$field = array( 'add_default_def' => true );

		# merge in default field values if told so
		if (
			!empty( $field['add_default_def'] ) &&
			method_exists( $this, 'default_' . $field_name )
		) {
			$f = "default_${field_name}";
			$field = array_merge( $this->$f(), $field );
		}

		# add global defaults
		$field = array_merge( $this->field_default(), $field );

		# shortcut to field name in spec
		$field['field_name'] = $field_name;

		# readable field name (uppercased field name with _ -> spaces
		if ( !isset( $field['name'] ) ) 
			$field['name'] = ucfirst( str_replace( '_', ' ', $field['field_name'] ) );

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
	 *
	 * The renderer is determined as such:
	 *
	 *  1. Explicit renderer set by field flag 'renderer'.
	 *  2. Shorthand renderer set by field flag 'render_as'.
	 *     (mapped to $this->render_<render_as>), but only if
	 *     $this->render_field_<field_name>() does not exist.
	 *  3. No renderer set, use $this->render_field_<field_name>()
	 */
	protected function _renderer( $field_name )
	{
		$field =& $this->_check_exists( $field_name );

		# explicit 'renderer'
		if ( $field['renderer'] )
			return $field['renderer'];

		# 'render_as' set
		elseif ( isset( $field['render_as'] ) && !method_exists( $this, "render_field_${field_name}" ) )
			return array( $this, 'render_' . $field['render_as'] );

		# $this->render_field_<field name>()
		else
		 return array( $this, "render_field_${field_name}" );	
	}

	#
	# Rendering
	#
	
	/**
	 * Renders the form.
	 */
	public function render( $ctxt = array() )
	{
		$res = '';
		$i = 0;
		$fields = $this->fields_order();
		$c = count( $fields );
		foreach ( $fields as $name ) {
			$ctxt['first'] = !$i++;
			$ctxt['last'] = $i == $c;
			$res .= $this->render_field( $name, $ctxt );
		}

		# wrap in form
		return $this->_render_form( $res, $ctxt );
	}

	/**
	 * Renders a field
	 */
	public function render_field( $field_name, $ctxt = array() )
	{
		$field =& $this->_check_exists( $field_name );

		return $this->_get_or_call(
			$this->_renderer( $field_name ),
			array( $this, $field, $this->$field_name, $ctxt )
		);
	}

	/**
	 * Renders given template.
	 */
	protected function _render_template( $self, $field, $val, $tpl = '', $ctxt = array() )
	{
		$ctxt += $field;
		$ctxt += array(
			'form' => $self,
			'field' => $field,
			'value' => $val,
			'error' => $self->error( $field['field_name'] ),
		);
		return Tpl::create( $tpl, $ctxt, $this->template_dir )->get();
	}

	/** Renders text input. */
	protected function render_text( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'text_field.html.php', $ctxt );
	}

	/** Renders e-mail input. */
	protected function render_email( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'email_field.html.php' , $ctxt);
	}

	/** Render password input. */
	protected function render_password( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'password_field.html.php' , $ctxt);
	}

	/** Renders radio buttons. */
	protected function render_radio( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'radio.html.php' , $ctxt);
	}

	/** Renders submit button. */
	protected function render_submit( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'submit.html.php' , $ctxt);
	}

	/** Renders hidden input. */
	protected function render_hidden( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'hidden_field.html.php' , $ctxt);
	}

	/** Renders file input. */
	protected function render_file( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'file_field.html.php' , $ctxt);
	}

	/** Renders select input. */
	protected function render_select( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'select.html.php' , $ctxt);
	}

	/** Renders textarea. */
	protected function render_textarea( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'textarea.html.php' , $ctxt);
	}

	/** Renders checkbox. */
	protected function render_checkbox( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'checkbox.html.php' , $ctxt);
	}

	/** Renders multiple checkboxes. */
	protected function render_checkboxes( $form, $field, $val, $ctxt = array() )
	{
		return $this->_render_template( $form, $field, $val, 'checkboxes.html.php' , $ctxt);
	}

	/** Renders the form wrapper. */
	protected function _render_form( $content, $ctxt = array() )
	{
		$ctxt += array(
			'content' => $content,
			'action' => $this->form_action,
			'method' => $this->form_method,
			'enctype' => $this->form_enctype,
			'accept_charset' => $this->form_accept_charset,
		);

		return Tpl::create( 'form.html.php', $ctxt, $this->template_dir )->get();
	}
}
