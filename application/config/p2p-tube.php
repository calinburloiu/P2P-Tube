<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// P2P-Tube specific configuration files

/*
|--------------------------------------------------------------------------
| Default Video File Extension
|--------------------------------------------------------------------------
|
| Sets the default video file extension, which must be set without '.' prefix. 
| This extension is going to be added to the `name` field from the DB in
| order to deduce the video file name if not stated otherwise. Possible values:
|
|	ogv
|	ogg
|
*/
$config['default_video_ext'] = 'ogg';	// TODO: Change to 'ogv'!

/*
|--------------------------------------------------------------------------
| Default Torrent File Extension
|--------------------------------------------------------------------------
|
| Sets the default torrent file extension, which must be set without '.' prefix. 
| This extension is going to be added to the video file name in order to deduce
| the torrent file name if not stated otherwise. Possible values:
|
|	tstream
|	torrent
|
*/
$config['default_video_ext'] = 'tstream';

