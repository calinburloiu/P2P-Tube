<!-- OBSOLETE -->

<div id="vlc_container">No VLC</div>
<table id="nsTable">
	<tr><td id="nsPlaybackCell"><input type=button value="Play" onClick="play();" />
	<input type=button value="Pause" onClick="pause();" />
	<input type=button value="Stop" onclick="stop();" /></td>
	<td id="nsTimeCell"><span id="nsTime">-</span></td>
	<td><div id="nsVol"></div></td>
	<td><input type=button value="Fullscreen" onclick="fullscreen();" /></td></tr>
	<tr><td colspan="4"><div id="nsSlider"></div></td></tr>
</table>

<script type="text/javascript">
	if (navigator.appName == "Netscape")
	{
		$('#vlc_container').html(
			'<embed type="application/x-ns-stream" name="vlc" id="vlc" autoplay="no" loop="no" width="' + getPluginWidth() + '" height="' + getPluginHeight() + '" target="<?php echo $url ?>" />');
	}
	else
	{
		$('$vlc_container').html(
			'<object classid="clsid:1800B8AF-4E33-43C0-AFC7-894433C13538" codebase="http://trial.p2p-next.org/download/SwarmPlugin_IE_1.0.4.cab" width="' + getPluginWidth() + '" height="' + getPluginHeight() + '" id="vlc" name="vlc" events="True" target="">'
			+ '<param name="Src" value="<?php echo $url ?>" />'
			+ '<param name="ShowDisplay" value="True" />'
			+ '<param name="Loop" value="False" />'
			+ '<param name="AutoPlay" value="False" />'
			+ '<param name="Toolbar" value="True" />'
			+ '</object>');
	}
	
	loadControls();
</script>