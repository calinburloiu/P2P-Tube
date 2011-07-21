// Usually replaced in PHP
siteUrl = '/';

function getNsVlcPlugin(videoUrl)
{
	$.post(
		siteUrl + 'video/plugin/ns-vlc',
		{url: videoUrl},
		function(data) {
			$('#video_plugin').html(data);
		}
	);
}

function getNsHtml5Plugin(videoUrl)
{
	$.post(
		siteUrl + 'video/plugin/ns-html5',
		{url: videoUrl},
		function(data) {
			$('#video_plugin').html(data);
		}
	);
}