// NextSharePC plugin controls
// Author: Calin-Andrei Burloiu, calin.burloiu@gmail.com
// 2011

updateInterval = 500;

function getURLParam( name )
{
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( window.location.href );
	if( results == null )
		return "";
	else
		return results[1];
}

function pad(number, length) 
{
	var str = '' + number;
	while (str.length < length) 
	{
		str = '0' + str;
	}

	return str;
}

function getPluginWidth()
{
	return 800;
}

function getPluginHeight()
{
	return 600;
}

function getSliderWidth()
{
	return $("#nsSlider").width();
}

function updateTime(afterSlider)
{
	var len = document.vlc.input.length;
	var pos;
	if(afterSlider)
	{
		var val = 1.0 * $("#nsSlider").slider("option", "value");
		pos = val / (getSliderWidth() - 1);
	}
	else
		pos = document.vlc.input.position;
	var time = Math.round(pos * len);
	var crt_s = Math.floor(time / 1000 % 60);
	var crt_min = Math.floor((time / 1000) / 60);
	var tot_s = Math.floor(len / 1000 % 60);
	var tot_min = Math.floor((len / 1000) / 60);

	if(isNaN(crt_s)) crt_s = 0;
	if(isNaN(crt_min)) crt_min = 0;
	if(isNaN(tot_s)) tot_s = 0;
	if(isNaN(tot_min)) tot_min = 0;

	$("#nsTime").html("" + pad(crt_min, 2) + ":" + pad(crt_s, 2)
		+ " / " + pad(tot_min, 2) + ":" + pad(tot_s, 2));
}

function update()
{
	var val = Math.round(document.vlc.input.position * (getSliderWidth() - 1));

	$("#nsSlider").slider({ value: val });
	
	updateTime(false);
	
	timerHandle = setTimeout("update()", updateInterval);
}

function displayNextSharePC(torrent)
{
	//document.write('popârțac');
	//return;
	
	if (navigator.appName == "Netscape")
	{
		document.write('<embed type="application/x-ns-stream"');
		document.write('name="vlc"');
		document.write('id="vlc"');
		document.write('autoplay="no" loop="no" width="');
		document.write('' + getPluginWidth());
		document.write('" height="');
		document.write('' + getPluginHeight());
		document.write('"');
		document.write('target="');
		document.write(torrent);
		document.write('" />');
	}
	else
	{
		document.write('<object classid="clsid:1800B8AF-4E33-43C0-AFC7-894433C13538" ');
		document.write('codebase="http://trial.p2p-next.org/download/SwarmPlugin_IE_1.0.4.cab"');
		document.write('width="');
		document.write(getPluginWidth());
		document.write('" height="');
		document.write(getPluginHeight());
		document.write('" id="vlc" name="vlc" events="True" target="">');
		document.write('<param name="Src" value="');
		document.write(torrent);
		document.write('" />');
		document.write('<param name="ShowDisplay" value="True" />');
		document.write('<param name="Loop" value="False" />');
		document.write('<param name="AutoPlay" value="True" />');
		document.write('<param name="Toolbar" value="True" />');
		document.write('</object>');
	}
	
	document.write('<table id="nsTable"><tr>'
		+ '<td id="nsPlaybackCell"><input type=button value="Play" onClick="play();" />'
		+ '<input type=button value="Pause" onClick="pause();" />'
		+ '<input type=button value="Stop" onclick="stop();" /></td>'
		+ '<td id="nsTimeCell"><span id="nsTime">-</span></td>'
		+ '<td><div id="nsVol"></div></td>'
		+ '<td><input type=button value="Fullscreen" onclick="fullscreen();" /></td></tr>'
		+ '<tr><td colspan="4"><div id="nsSlider"></div></td></tr>'
		+ '</table>');

	return true;
}

function onSliderStop(event, ui)
{
	var val = $("#nsSlider").slider("option", "value");
	var s = 1.0 * val / (getSliderWidth() - 1);
	
	//document.vlc.playlist.seek(s, false);
	//document.vlc.video.toggleFullscreen();
	
	try {
		if(s == 0)
			document.vlc.input.position = 0.0000001;
		else if(s == 1)
			document.vlc.input.position = 0.9999999;
		else
			document.vlc.input.position = s;
	}
	catch(err) {
		alert(err.message + ": " + err.description);
	}
	
	timerHandle = setTimeout("update()", updateInterval);
}

function onSliderSlide(event, ui)
{
	updateTime(true);
	
	clearTimeout(timerHandle);
}

function onVolChange(event, ui)
{
	var val = $("#nsVol").slider("option", "value");
	
	document.vlc.audio.volume = val;
}

function loadControls()
{
	if(document.vlc == null)
		return;
	
	$("nsTable").css("width", getPluginWidth());
	
	// Progress Slider
	$("#nsSlider").slider({ animate: true });
	$("#nsSlider").slider({ min: 0 });
	$("#nsSlider").slider({ max: (getPluginWidth() - 1) });
	$("#nsSlider").slider({
		stop: onSliderStop,
		slide: onSliderSlide
	});
	$("#nsSlider").css("width", getPluginWidth());// TODO
	$("#nsSlider").slider();
	
	// Volume Slider
	$("#nsVol").slider({ animate: true });
	$("#nsVol").slider({ min: 0 });
	$("#nsVol").slider({ max: 200 });
	$("#nsVol").slider({ value: document.vlc.audio.volume });
	$("#nsVol").slider({
		change: onVolChange,
	});
	$("#nsVol").css("width", 72);
	$("#nsVol").slider();
	
	timerHandle = setTimeout("update()", updateInterval);
}

function play()
{
	document.vlc.playlist.play();
	timerHandle = setTimeout("update()", updateInterval);
}

function pause()
{
	document.vlc.playlist.togglePause();
	clearTimeout(timerHandle);
}

function stop()
{
	document.vlc.playlist.stop();
	clearTimeout(timerHandle);
}

function fullscreen()
{
	document.vlc.video.toggleFullscreen();
}
