<div id="video-widget-imagemap">
	<h1>Video Widget Snapshot</h1>
	<div><div class="ui-imagemap-image" style="margin: 0 auto; margin-bottom: 1em">
		<img src="<?php echo site_url('img/nsvideo-widget.jpg') ?>" alt="NextShare Video Widget Screenshot" title="NextShare Video Widget Screenshot" width="446" height="353" />
	</div></div>
	<h4>Click on a component bellow to highlight it on the snapshot above and view a description of it:</h4> 
	
	<h1>Video Widget Components</h1>
	<div id="video-widget-titles">
		<h3 class="ui-imagemap-title"
				data-left="5px" data-top="44px" data-width="433px" data-height="246px">
			<a href="#">Watching Area</a>
		</h3>
		<div>
			Here you can watch the video.
		</div>
		
		<h3 class="ui-imagemap-title"
				data-left="3px" data-top="314px" data-width="32px" data-height="34px">
			<a href="#"><em>Play</em> / <em>Pause</em> button</a>
		</h3>
		<div>
			Toggle playing and pausing the video.
		</div>
		
		<h3 class="ui-imagemap-title"
				data-left="41px" data-top="321px" data-width="39px" data-height="21px">
			<a href="#">Current Time</a>
		</h3>
		<div>
			View the time elapsed from the beginning of the video.
		</div>
		
		<h3 class="ui-imagemap-title"
				data-left="86px" data-top="323px" data-width="57px" data-height="19px">
			<a href="#">Total Time</a>
		</h3>
		<div>
			View video duration.
		</div>
		
		<h3 class="ui-imagemap-title"
				data-left="2px" data-top="295px" data-width="439px" data-height="22px">
			<a href="#">Time Progress Slider</a>
		</h3>
		<div>
			View a graphical representation of the time elapsed from the movie from its duration. You can also use this slider to seek current position in the video to a desired point.
		</div>
		
		<h3 class="ui-imagemap-title"
				data-left="213px" data-top="320px" data-width="80px" data-height="24px">
			<a href="#"><em>Volume</em> Slider</a>
		</h3>
		<div>
			A graphical representation of the sound volume level which can be manipulated with the mouse in order change current volume.
		</div>
		
		<h3 class="ui-imagemap-title"
				data-left="181px" data-top="313px" data-width="32px" data-height="36px">
			<a href="#"><em>Mute</em> button</a>
		</h3>
		<div>
			Use this button to alternatively disable or enable the sound for the video.
		</div>
		
		<h3 class="ui-imagemap-title"
				data-left="293px" data-top="313px" data-width="119px" data-height="37px">
			<a href="#">Switch Video Definition (Resolution)</a>
		</h3>
		<div>
			<p>This group of check buttons show definitions available for the video. The definition characterizes its resolution (number of pixels on horizontal and vertical). A definition is coded here, as in television systems, with a number, representing the number of vertical pixels (height), and a "p", which is an abbreviation from <a href="http://en.wikipedia.org/wiki/Progressive_scan" rel="nofollow" target="_blank"><em>progressive scan</em></a>.</p>
			<p>The number of vertical pixels depends on the <a href="http://en.wikipedia.org/wiki/Display_aspect_ratio" rel="nofollow" target="_blank"><em>display aspect ratio</em></a> which is usually 4:3 or 16:9. Because the video needs to fit in your browser its actual size (when not in Full Screen mode) will be between 640 and 1024 horizontal pixels.</p>
		</div>
		
		<h3 class="ui-imagemap-title"
				data-left="6px" data-top="6px" data-width="134px" data-height="42px">
			<a href="#">Switch Video Plugin</a>
		</h3>
		<div>
			Use this two check buttons to switch between <a href="<?php echo site_url('about#next-share-video-plugins') ?>">the two NextShare plug-ins available</a>. Choose <em>HTML5</em> for <a href="<?php echo site_url('about#swarmplayer') ?>">SwarmPlayer</a> and <em>VLC</em> for <a href="<?php echo site_url('about#nextsharepc') ?>">NextSharePC</a>.
		</div>
		
		<h3 class="ui-imagemap-title"
				data-left="407px" data-top="315px" data-width="35px" data-height="34px">
			<a href="#"><em>Full Screen</em> button</a>
		</h3>
		<div>
			Displays the video on the whole screen without having to be limited by the web page.
		</div>
	</div>

</div>

<script type="text/javascript">
$(function() {
	$('#video-widget-titles').accordion({
		collapsible: true,
		autoHeight: false,
		active: false
	});

	$('#video-widget-imagemap').imagemap({

	});
});
</script>