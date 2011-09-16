<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Encodes CI segments for using them in URLs.
 * Useful when the last page needs to be remembered in the URL.
 * The function replaces all '/' with '\' and then encodes with urlencode PHP
 * function.
 * 
 * @param string $str	segments string to encode
 * @param string $disallowed_prefix	if $str start with $disallowed_prefix the
 * function returns a null string.
 * @return string	the encoded segments
 */
function urlencode_segments($str, $disallowed_prefix = NULL)
{
	if ($disallowed_prefix && strpos($str, $disallowed_prefix) === 0)
		return '';
	
	$str = str_replace('/', '\\', $str);
	return urlencode($str);
}

/**
 * Decodes a string encoded with urlencode_segments helper.
 * 
 * @param string $str	string to decode
 * @return string	the valid CI segments decoded from $str
 */
function urldecode_segments($str)
{
	$str = urldecode($str);
	return str_replace('\\', '/', $str);
}

/* End of file MY_url_helper.php */
/* Location: ./application/helpers/MY_url_helper.php */