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
| Autoload Stylesheets
|--------------------------------------------------------------------------
|
| Sets the default CSSs that are going to be automatically added on any HTML
| page generated with 'application/views/html_begin.php' view and
| HTML_head_params library.
|
| Do not include any path to the file! 'css/' is going to be used.
|
| Use "array()" for no default stylesheet.
|
*/
$config['autoload_css'] = array('default.css',
								'jquery-ui.css');

/*
|--------------------------------------------------------------------------
| Autoload Javascript
|--------------------------------------------------------------------------
|
| Sets the default Javascripts that are going to be automatically added in any
| HTML page generated with 'application/views/html_begin.php' view and
| HTML_head_params library.
|
| Do not include any path to the file! 'js/' is going to be used.
|
| Use "array()" for no default javascript.
|
*/
$config['autoload_js'] = array('jquery.js',
								'jquery-ui.js',
								'jquery.ui.nsinstall.js');

/*
|--------------------------------------------------------------------------
| Default Video File Extension (OBSOLETE)
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
| in DB (for example in `videos` table), and values are string identifiers
| for categories. Category names localization can be made by concatenating
| 'ui_categ_' with the value in order to obtain a language key.
| IDs must be numeric and must preferably start from 1.
|
*/
$config['categories'] = array(1 => 'movies', 2 => 'tech-talks', 3 => 'events', 4 => 'karaoke');

/*
|--------------------------------------------------------------------------
| Videos per Page
|--------------------------------------------------------------------------
|
| The number of video icons shown per page (as in catalog/category).
|
*/
$config['videos_per_page'] = 20;

/*
|--------------------------------------------------------------------------
| Videos per Row
|--------------------------------------------------------------------------
|
| The number of video icons shown on a single line (as in home page).
|
*/
$config['videos_per_row'] = 5;

/*
|--------------------------------------------------------------------------
| Search Results per Page
|--------------------------------------------------------------------------
|
| The number of search results shown per page (as in catalog/search).
|
*/
$config['search_results_per_page'] = 20;
