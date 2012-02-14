<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Content ingestion configuration; CIS interoperability configuration. 
 * 
 * Content ingestion is provided by a CIS (Content Ingestion Server).
 * Communication is provided through web services directly or in a transparent
 * manner through a CIS-LB (CIS - Load Balancer).
 */

/*
|--------------------------------------------------------------------------
| CIS / CIS-LB URL
|--------------------------------------------------------------------------
|
| CIS web service URL (ended with '/'). For example:
| 
|	http://cis.org:31500/
|
*/
$config['cis_url'] = 'http://p2p-next-03.grid.pub.ro:31500/';

/*
|--------------------------------------------------------------------------
| Video Formats
|--------------------------------------------------------------------------
|
| Formats available for the ingested video. You need to provide an array
| of dictionaries as in the following example:
| 
|	$config['formats'] = array(
|		array(
|			'container'=>'ogg',
|			'extension'=>'ogv',
|			'audio_codec'=>'vorbis',
|			'audio_bit_rate'=>'128k',
|			'audio_sampling_rate'=>44100,
|			'audio_channels'=>2,
|			'video_codec'=>'theora',
|			'video_bit_rate'=>'768k',
|			'video_frame_rate'=>25,
|			'video_height'=>600
|		)
|	);
|
| For a list of available containers and audio / video codecs see CIS
| documentation. The video_height parameter is a desired height resolution.
| It may be lower if the uploaded video has a lower resolution.
|
*/
// 
$config['formats'] = array(
	array(
		'container'=>'ogg',
		'extension'=>'ogv',
		'audio_codec'=>'vorbis',
		'audio_bit_rate'=>'128k',
		'audio_sampling_rate'=>44100,
		'audio_channels'=>2,
		'video_codec'=>'theora',
		'video_bit_rate'=>'768k',
		'video_frame_rate'=>25,
		'video_height'=>600
	),
	array(
		'container'=>'ogg',
		'extension'=>'ogv',
		'audio_codec'=>'vorbis',
		'audio_bit_rate'=>'192k',
		'audio_sampling_rate'=>44100,
		'audio_channels'=>2,
		'video_codec'=>'theora',
		'video_bit_rate'=>'1536k',
		'video_frame_rate'=>25,
		'video_height'=>1080
	)
);

/*
|--------------------------------------------------------------------------
| Thumnail images count
|--------------------------------------------------------------------------
|
| Number of thumbnail images for a video asset.
|
*/
$config['thumbs_count'] = 4;

/*
|--------------------------------------------------------------------------
| Eliminate Duplicate Resolutions
|--------------------------------------------------------------------------
|
| Eliminate consecutive formats with the same resolution after processing
| them.
|
*/
$config['elim_dupl_res'] = TRUE;