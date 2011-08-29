<div class="ui-widget ui-widget-content ui-widget-header ui-corner-all">
	<div id="container-install-swarmplayer" class="container-install"></div>
	<div id="container-install-nextsharepc" class="container-install"></div>
</div>

<script type="text/javascript">
	$(function() {
		var msgLocalized = {
			"none": "<?php echo $this->lang->line('ui_install') ?>",
			"os not supported": "<?php echo $this->lang->line('ui_install_os_not_supported') ?>",
			"browser not supported": "<?php echo $this->lang->line('ui_install_browser_not_supported') ?>",
			"browser version not supported": "<?php echo $this->lang->line('ui_install_browser_version_not_supported') ?>",
			"already installed": "<?php echo $this->lang->line('ui_install_already_installed') ?>"
		};

        $('#container-install-swarmplayer')
        	.nsinstall({
				"type": "ns-html5",
				"installLink": "<?php echo site_url('install-plugins#SwarmPlayer') ?>",
				hideIfAlreadyInstalled: false,
				msg: msgLocalized
        	});

        $('#container-install-nextsharepc')
	    	.nsinstall({
				"type": "ns-vlc",
				"installLink": "<?php echo site_url('install-plugins#NextSharePC') ?>",
				hideIfAlreadyInstalled: false,
				msg: msgLocalized
	    	});
	});
</script>