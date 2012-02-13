<?php

/*
 * OBSOLETE!
 * These functions retrieve information about audio and video files.
 * Depends on MediaInfo CLI program (http://mediainfo.sourceforge.net/)
 * 
 * @author Călin-Andrei Burloiu
 */

/**
 *
 * @param string $params mediainfo parameters, including the input file name
 * passed as in shell
 * @return string mediainfo standard output
 */
function exec_mediainfo($params)
{
	// If file does not exist it exists with code 1. If file is not a valid
	// audio/video file it exists with code 0 and outputs nothing.
	$h = popen('mediainfo ' . $params . ' 2>&1', 'r');

	$r = fgets($h, 512);
	$r = trim($r);
	
	if (pclose($h) > 0 || empty($r))
		return FALSE;
	
	return $r;
}

/**
 * Returns duration in hours, minutes and seconds for an audio/video file.
 *
 * @param string $file_name 
 * @return array an associative array with keys 'h', 'min', 's'
 */
function get_av_duration($file_name)
{
	$output = exec_mediainfo(
			'--Inform="General;%Duration/String3%" "'. $file_name. '"');
	
	if (!$output)
		return FALSE;
	
	$toks = explode(':', $output);
	$res['h'] = intval($toks[0]);
	$res['min'] = intval($toks[1]);
	$res['s'] = floatval($toks[2]);
	
	return $res;
}

/**
 * Returns video width size in pixels.
 * 
 * @param string $file_name
 * @return int
 */
function get_video_width($file_name)
{
	$output = exec_mediainfo(
			'--Inform="Video;%Width%" "'. $file_name. '"');
	
	if (!$output)
		return FALSE;
	
	return intval($output);
}

/**
 * Returns video height size in pixels.
 * 
 * @param string $file_name
 * @return int
 */
function get_video_height($file_name)
{
	$output = exec_mediainfo(
			'--Inform="Video;%Height%" "'. $file_name. '"');
	
	if (!$output)
		return FALSE;
	
	return intval($output);
}

/**
 * Returns Display Aspect Ration (DAR) of a video.
 * 
 * @param string $file_name
 * @return string a ratio represented a two integers separated by a colon
 */
function get_video_dar($file_name)
{
	$output = exec_mediainfo(
			'--Inform="Video;%DisplayAspectRatio/String%" "'. $file_name. '"');
	
	if (!$output)
		return FALSE;
	
	return $output;
}

/* End of file av_info_helper.php */
/* Location: ./application/helpers/av_info_helper.php */