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

/**
 * Returns information about an Audio/Video file.
 * 
 * @param string $file_name Audio/Video file
 * @return dictionary a dictionary of audio/video properties with keys:
 * <ul>
 *   <li>width</li>
 *   <li>height</li>
 *   <li>dar (display aspect ratio)</li>
 *   <li>duration (formated as [HH:]mm:ss)</li>
 *   <li>size (in bytes)</li>
 * </ul>
 */
function get_av_info($file_name)
{
	// TODO use ffprobe to return width, height, DAR, duration and size of a video
	
	return array('width'=> 1440, 'height'=> 1080, 'dar'=> '16:9',
			'duration'=> '00:10', 'size'=> 5568748);
}

/**
 * Return a dictionary with formats compliant for DB and CIS and computes
 * resolutions such that an uploaded video will not be converted to a higher
 * resolution.
 * 
 * @param type $formats formats as in content_ingestion config file
 * @param type $av_info structure as returned by get_av_info function from this
 * helper
 * @param type $elim_dupl_res eliminate consecutive formats with the same
 * resolution
 * @return array a dictionary with DB format at key 'db_formats' and CIS format
 * at key 'transcode_configs' 
 */
function prepare_formats($formats, $av_info, $elim_dupl_res=FALSE)
{
	$transcode_configs = array();
	$db_formats = array();
	
	for ($i = 0; $i < count($formats); $i++)
	{
		$transcode_configs[$i]['container'] = $formats[$i]['container'];
		$transcode_configs[$i]['extension'] = $formats[$i]['extension'];
		$db_formats[$i]['ext'] = $formats[$i]['extension'];
		$transcode_configs[$i]['a_codec'] = $formats[$i]['audio_codec'];
		$transcode_configs[$i]['a_bitrate'] = $formats[$i]['audio_bit_rate'];
		$transcode_configs[$i]['a_samplingrate'] = $formats[$i]['audio_sampling_rate'];
		$transcode_configs[$i]['a_channels'] = $formats[$i]['audio_channels'];
		$transcode_configs[$i]['v_codec'] = $formats[$i]['video_codec'];
		$transcode_configs[$i]['v_bitrate'] = $formats[$i]['video_bit_rate'];
		$transcode_configs[$i]['v_framerate'] = $formats[$i]['video_frame_rate'];
		$transcode_configs[$i]['v_dar'] = $av_info['dar'];
		$db_formats[$i]['dar'] = $av_info['dar'];

		$sar = $av_info['width'] / $av_info['height'];
		
		if ($av_info['height'] < $formats[$i]['video_height'])
		{
			$width = $av_info['width'];
			$height = $av_info['height'];
		}
		else
		{
			$height = $formats[$i]['video_height'];
			$width = round($sar * $height);
		}
		
		$transcode_configs[$i]['v_resolution'] = "${width}x${height}";
		$db_formats[$i]['res'] = "${width}x${height}";
	}
	
	// Eliminate formats with duplicate resolutions.
	if ($elim_dupl_res)
	{
		for ($i = 1; $i < count($transcode_configs); $i++)
		{
			if ($transcode_configs[$i]['v_resolution']
					=== $transcode_configs[$i - 1]['v_resolution'])
			{
				unset($transcode_configs[$i - 1]);
				unset($db_formats[$i - 1]);
				$i--;
			}
		}
	}
	
	return array('transcode_configs'=>$transcode_configs,
		'db_formats'=>$db_formats);
}

/* End of file video_helper.php */
/* Location: ./application/helpers/video_helper.php */