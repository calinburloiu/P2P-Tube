<div id="video-widget-imagemap">
	<div><div class="ui-imagemap-image" style="margin: 0 auto; margin-bottom: 1em">
		<img src="<?php echo site_url('img/nsvideo-widget.jpg') ?>" alt="NextShare Video Widget Screenshot" width="446" height="353" />
	</div></div>
	
	<div id="video-widget-titles">
		<h3><a href="#" class="ui-imagemap-title"
			data-left="5px" data-top="44px" data-width="433px" data-height="246px"
			>Watching Area</a></h3>
		<div>
			Here you can watch the video.
		</div>
		
		<h3><a href="#" class="ui-imagemap-title"
			data-left="3px" data-top="314px" data-width="32px" data-height="34px"
			><em>Play</em> / <em>Pause</em> button</a></h3>
		<div>
			Toggle playing and pausing the video.
		</div>
		
		<h3><a href="#" class="ui-imagemap-title"
			data-left="41px" data-top="321px" data-width="39px" data-height="21px"
			>Current Time</a></h3>
		<div>
			...
		</div>
		
		<h3><a href="#" class="ui-imagemap-title"
			data-left="86px" data-top="323px" data-width="57px" data-height="19px"
			>Total Time</a></h3>
		<div>
			...
		</div>
		
		<h3><a href="#" class="ui-imagemap-title"
			data-left="2px" data-top="295px" data-width="439px" data-height="22px"
			>Time Progress Slider</a></h3>
		<div>
			...
		</div>
		
		<h3><a href="#" class="ui-imagemap-title"
			data-left="213px" data-top="320px" data-width="80px" data-height="24px"
			><em>Volume</em> Slider</a></h3>
		<div>
			...
		</div>
		
		<h3><a href="#" class="ui-imagemap-title"
			data-left="181px" data-top="313px" data-width="32px" data-height="36px"
			><em>Mute</em> button</a></h3>
		<div>
			...
		</div>
		
		<h3><a href="#" class="ui-imagemap-title"
			data-left="293px" data-top="313px" data-width="119px" data-height="37px"
			>Switch Video Definition (Resolution)</a></h3>
		<div>
			...
		</div>
		
		<h3><a href="#" class="ui-imagemap-title"
			data-left="6px" data-top="6px" data-width="134px" data-height="42px"
			>Switch Video Plugin</a></h3>
		<div>
			...
		</div>
		
		<h3><a href="#" class="ui-imagemap-title"
			data-left="407px" data-top="315px" data-width="35px" data-height="34px"
			><em>Full Screen</em> button</a></h3>
		<div>
			...
		</div>
	</div>

</div>

<script type="text/javascript">
$(function() {
	$('#video-widget-titles').accordion({
		collapsible: true,
		active: false
	});

	$('#video-widget-imagemap').imagemap({

	});
});
</script>