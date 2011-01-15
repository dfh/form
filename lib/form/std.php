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

#
# PHP standard library
#

if ( !defined( 'SYSLOG_FACILITY' ) ) {
	define( 'SYSLOG_FACILITY', LOG_LOCAL2 );
}

if ( !function_exists( 'debug' ) ) {
	function debug( $msg, $arg1 = null )
	{
		$args = func_get_args();

		if( $arg1 )
			$msg = @vsprintf( array_shift( $args ), $args );

		syslog( SYSLOG_FACILITY | LOG_DEBUG, $msg );
	}
}

/**
 * Returns a HTML representation of the input PHP data. Internally uses var_dump()
 */
function var_html()
{
	$args = func_get_args();
	ob_start();
	call_user_func_array('var_dump', $args);
	$txt = ob_get_clean();
	return '<pre>' . htmlspecialchars($txt) . '</pre>';
}

/**
 * Returns a readable error type description given error number.
 */
function readable_errtype( $errno )
{
	$errtypes = array(
		E_ERROR => 'error',
		E_WARNING => 'warning',
		E_NOTICE => 'notice',
		E_STRICT => 'strict',
		E_USER_ERROR => 'user error',
		E_USER_WARNING => 'user warning',
		E_USER_NOTICE => 'user notice'
	);

	if( isset( $errypes[$errno] ) ) {
		return $errtypes[$errono];
	} else {
		return $errno;
	}
}

/**
 * sprintf w/ named input, python style: %(name)x 
 *
 * NOTE: this is pretty crude, and doesn't support the full range of options for printf.
 */
function sprintfn( $str, $args )
{
	if ( !is_array( $args ) )
		$args = array( $args );
	
	# translate %(name)x -> positional args
	$i = 1;
	foreach( $args as $k => $v ) {
		$str = preg_replace( '/(\(' . $k . '\))(\w)/', $i++ . '$$2', $str );
	}
	$res = vsprintf( $str, array_values( $args ) );
	return $res;
}

/**
 * Strips \n, \r, \c and \t from given string.
 */
function html_clean( $html )
{
	return str_replace( array( "\n", "\r", "\c", "\t" ), ' ', $html );
}

/**
 * Is GET?
 */
function is_get()
{
	return $_SERVER['REQUEST_METHOD'] == 'GET';
}

/**
 * Is POST?
 */
function is_post()
{
	return $_SERVER['REQUEST_METHOD'] == 'POST';
}

/**
 * Sends mail using PHP's mail().
 */
function send_mail( $to, $options = array() )
{
	$options += array(
		'subject' => '',
		'message' => '',
		'cc' => '',
		'bcc' => '',
		'from' => '',
		'content_type' => 'text/plain',
		'headers' => array()
	);

	# set headers
	$addtl_headers = array();
	$addtl_headers[] = 'MIME-Version: 1.0';
	$addtl_headers[] = 'Content-Type: ' . $options['content_type'] . '; charset=UTF-8';
	foreach( array( 'cc', 'bcc', 'from' ) as $o ) {
		if( $options[$o] ) {
			$addtl_headers[] = ucfirst( $o ) . ': ' . $options[$o];
		}	
	}
	$addtl_headers = implode( "\r\n", $options['headers'] + $addtl_headers );

	if ( !is_array( $to ) ) {
		$to = array( $to );
	}
	$res = true;
	foreach( $to as $t ) {
		$res &= mail(
			$t,
			'=?UTF-8?B?' . base64_encode( $options['subject'] ) . '?=',
			$options['message'], $addtl_headers
		);
		if ( $res ) {
			debug('Sent mail to: ' . $t );
		} else {
			debug('Failed sending mail to: ' . $t );
		}
	}
	return $res;
}

/** parses CLI args. see http://pwfisher.com/nucleus/index.php?itemid=45 */
function parse_args( $argv )
{
	array_shift($argv);
	$out = array();
	foreach ( $argv as $arg ) {
		# --key or --key=val
		if ( substr( $arg, 0, 2) == '--' ) {
			$eqPos = strpos( $arg, '=' );
			# --key
			if ( $eqPos === false ) {
				$key = substr( $arg, 2 );
				$out[$key] = isset( $out[$key] ) ? $out[$key] : true;
			# --key=val
			} else {
				$key = substr( $arg, 2, $eqPos - 2 );
				$out[$key] = substr( $arg, $eqPos + 1 );
			}
		# -k=val or -klm
		} elseif ( substr( $arg, 0, 1 ) == '-' ) {
			# -k=val
			if ( substr( $arg, 2, 1 ) == '=' ) {
				$key = substr( $arg, 1, 1 );
				$out[$key] = substr( $arg, 3 );
			# -klm
			} else {
				foreach ( str_split( substr( $arg, 1 ) ) as $char ) {
					$key = $char;
					$out[$key] = isset($out[$key]) ? $out[$key] : true;
				}
			}
		} else {
				$out[] = $arg;
		}
	}
	return $out;
}

/** requires all files in given dir. */
function require_all( $path )
{
	foreach( glob( dirname( $path ) . '/*.php' ) as $fn ) {
		require $fn;
	}
}

/** merges two urls */
function url_merge( $u1, $u2 )
{
	function_exists( 'join_url' ) or require 'join_url.php';
	function_exists( 'split_url' ) or require 'split_url.php';

	$u1 = split_url( $u1, false );
	$u2 = split_url( $u2, false );

	# merge query string
	$u1_q = $u2_q = array();
	if ( isset( $u1['query'] ) ) {
		parse_str( $u1['query'], $u1_q );
	}
	if ( isset( $u2['query'] ) ) {
		parse_str( $u2['query'], $u2_q );
	}
	$u_q = array_merge( $u1_q, $u2_q );

	$u = array_merge( $u1, $u2 );	
	if ( $u_q ) {
		$u['query'] = http_build_query( $u_q );
	}	

	return join_url( $u, false );
}

/** Adds next url */
function add_next_url( $url, $next, $overwrite = false, $next_name = 'next' )
{		
	if ( !$next )
		return $url;

	$qs = "?$next_name=" . urlencode( $next );

	if ( $overwrite ) 
		$url = url_merge( $url, $qs );
	else
		$url = url_merge( $qs, $url );

	return $url;
}

/** gettext + ngettext alias. */
function __( $msg, $plural = '', $n = 1 )
{
	if( func_num_args() == 1 ) {
		return _( $msg );
	} else {
		return ngettext( $msg, $plural, $n );
	}
}

/** truncate given string to n words */
function truncate_by_paragraph( $str, $paragraphs = 1, $newline = "\n" )
{
	# paragraph break is simply newline
	$ps = explode( $newline, $str );
	if ( count( $ps ) > $paragraphs )
		return implode( "\n", array_slice( $ps, 0, $paragraphs ) );
	else
		return implode( "\n", $ps );
}


/**
 * VALIDATORS
 * 
 * To make sure data (usually user input) complies to some certain rule
 * (must be a valid e-mail adress, url, integer between 10 and 20 etc.).
 * 
 * All validators return true iff given data is correct.
 */

/**
 * Validates an integer, optionally checks for upper/lower boundaries.
 */
function validate_int( $int, $min = null, $max = null )
{
	return
		$int !== null &&
		$int !== false &&
		
		is_numeric( $int ) &&
		// it must validate to itself, allowing for 3.0, "6" etc.
		( (int) $int ) == $int &&
		
		// correct interval
		( $min === null || $min <= $int ) &&
		( $max === null || $int <= $max );
}

/**
 * Validates a number, optionally checks for upper/lower boundaries.
 */
function validate_num( $n, $min = null, $max = null )
{
	return
		$n !== null &&
		$n !== false &&
		
		// numbers are numeric
		is_numeric( $n ) &&
		
		// correct interval
		( $min === null || $min <= $n ) &&
		( $max === null || $n <= $max );
}

/**
 * Validates string length.
 */
function validate_string( $string, $min = null, $max = null, $charset = 'utf-8' )
{
	$len = $charset == null ? strlen( $string ) : mb_strlen( $string, $charset );
	return
		( $min === null || $min <= $len ) &&
		( $max === null || $len <= $max );
}

/**
 * Validates a URL.
 */
function validate_url( $url )
{
	# filter_var returns false if filter fails
	return filter_var( $url, FILTER_VALIDATE_URL ) !== false;
}

/**
 * Validates an e-mail address.
 */
function validate_email( $email )
{
	return filter_var( $email, FILTER_VALIDATE_EMAIL ) !== false;
}

/**
 * Validates IP address.
 */
function validate_ip( $ip )
{
	return filter_var( $ip, FILTER_VALIDATE_IP ) !== false;
}
