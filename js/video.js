/**
 * Video page client scripting (AJAX, events etc.)
 */

// Usually replaced in PHP
siteUrl = '/';

function retrieveNsVlcPlugin(videoUrl)
{
	$.post(
		siteUrl + 'video/plugin/ns-vlc',
		{url: videoUrl},
		function(data) {
			$('#video_plugin').html(data);
		}
	);
}

function retrieveNsHtml5Plugin(videoUrl)
{
	$.post(
		siteUrl + 'video/plugin/ns-html5',
		{url: videoUrl},
		function(data) {
			$('#video_plugin').html(data);
		}
	);
}

/*$(document).ready(function()
{
	$	
}*/