<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tests if parameter is a resolution string with format [width]x[height].
 * @param string $res
 * @return boolean
 */
function is_res($res)
{
	if (strpos($res, 'x') === FALSE)
	return FALSE;

	return TRUE;
}

/**
 * Return the width from a resolution string with format [width]x[height].
 * @param string $res
 */
function res_to_width($res)
{
	if (! is_res($res))
	return FALSE;

	return intval( substr($res, 0, strpos($res, 'x')) );
}

/**
* Return the height from a resolution string with format [width]x[height].
* @param string $res
*/
function res_to_height($res)
{
	if (! is_res($res))
	return FALSE;

	return intval( substr($res, strpos($res, 'x') + 1) );
}

/**
 * Compares two resolution strings $a and $b with format [width]x[height] based
 * on theirs megapixels number by return -1, 0 or 1 like any compare function.
 * @param string $a
 * @param string $b
 * @param function $access_function	filters input parameters by doing something
 * like $a = $access_function($a). Leave it NULL for no filtering.
 */
function megapixels_cmp($a, $b, $access_function = NULL)
{
	if ($access_function !== NULL)
	{
		$a = $access_function($a);
		$b = $access_function($b);
	}

	$a_w = intval( substr($a, 0, strpos($a, 'x')) );
	$a_h = intval( substr($a, strpos($a, 'x') + 1) );
	if ($a_w === FALSE || $a_h === FALSE)
	return 0;
	$a_Mp = $a_w * $a_h;
	$b_w = intval( substr($b, 0, strpos($b, 'x')) );
	$b_h = intval( substr($b, strpos($b, 'x') + 1) );
	if ($b_w === FALSE || $b_h === FALSE)
	return 0;
	$b_Mp = $b_w * $b_h;

	if ($a_Mp == $b_Mp)
	return 0;

	return $a_Mp > $b_Mp ? 1 : -1;
}

/**
 * Return the index of the $haystack element which has the closest resolution
 * to $needle resolution string.
 * @param array $haystack
 * @param string $needle
 * @param function $access_function	filters input parameters by doing something
 * like $a = $access_function($a). Leave it NULL for no filtering.
 */
function get_closest_res($haystack, $needle, $access_function = NULL)
{
	$d_min = INF;
	$i_min = FALSE;

	foreach($haystack as $i => $elem)
	{
		if ($access_function !== NULL)
		$elem = $access_function($elem);

		$d = abs(res_to_width($elem) * res_to_height($elem)
		- res_to_width($needle) * res_to_height($needle));
		if ($d < $d_min)
		{
			$d_min = $d;
			$i_min = $i;
		}
	}

	return $i_min;
}

/* End of file video_helper.php */
/* Location: ./application/helpers/video_helper.php */