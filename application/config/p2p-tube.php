<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// P2P-Tube specific configuration files

/*
|--------------------------------------------------------------------------
| Site Name
|--------------------------------------------------------------------------
|
| Sets the name of the site. It can be used for example in pages title.
|
*/
$config['site_name'] = 'P2P-Next UPB LivingLab';

/*
|--------------------------------------------------------------------------
| Default Stylesheet
|--------------------------------------------------------------------------
|
| Sets the default CSS that is going to be automatically added on any HTML
| page generated with 'application/views/html_begin.php' view and
| HTML_head_params library.
|
| Do not include any path to the file! 'stylesheets/' is going to be used.
|
| Leave blank for no default stylesheet.
|
*/
$config['default_stylesheet'] = 'default.css';

/*
|--------------------------------------------------------------------------
| Default Javascript
|--------------------------------------------------------------------------
|
| Sets the default Javascript that is going to be automatically added in any
| HTML page generated with 'application/views/html_begin.php' view and
| HTML_head_params library.
|
| Do not include any path to the file! 'javascripts/' is going to be used.
|
| Leave blank for no default javascript.
|
*/
$config['default_javascript'] = '';

/*
|--------------------------------------------------------------------------
| Default Video File Extension
|--------------------------------------------------------------------------
|
| Sets the default video file extension, which must be set without '.' prefix. 
| This extension is going to be added to the `name` field from the DB in
| order to deduce the video file name if not stated otherwise in the format.
| Possible values:
|
|	ogv
|	ogg
|
*/
$config['default_video_ext'] = 'ogv';

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
$config['default_torrent_ext'] = 'tstream';

/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
|
| An associative list with the video categories of the site. IDs are used
| in DB (for example in `videos` table), and value are human-friendly names
| for categories. IDs must be numeric and must preferably start from 1.
|
*/
$config['categories'] = array(1 => 'Movies', 2 => 'TechTalks', 3 => 'Events', 4 => 'Karaoke');
