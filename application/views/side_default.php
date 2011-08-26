<div class="ui-widget ui-widget-content ui-widget-header ui-corner-all">
	<div id="container-install-swarmplayer" class="container-install">
		<a id="install-swarmplayer" href="<?php echo site_url('install-plugins#SwarmPlayer') ?>">Install SwarmPlayer Plugin</a>
	</div>
	<div id="container-install-nextsharepc" class="container-install">
		<a id="install-nextsharepc" href="<?php echo site_url('install-plugins#NextSharePC') ?>">Install NextSharePC Plugin</a>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		$('#install-swarmplayer')
			.button({
	            icons: {
	                primary: "ui-icon-arrowthickstop-1-s"
	            }
	        });
		$('#install-nextsharepc')
			.button({
	            icons: {
	                primary: "ui-icon-arrowthickstop-1-s"
	            }
	        });
	});
</script>