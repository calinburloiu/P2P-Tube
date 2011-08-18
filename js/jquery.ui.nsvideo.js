/*
 * jQuery UI NS-Video 1.0.0 beta
 *
 * Copyright 2011, Călin-Andrei Burloiu
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 *
 * Depends:
 *   jquery.ui.core.js
 *   jquery.ui.widget.js
 */
(function( $, undefined ) {

$.widget( "ui.nsvideo", {
	version: "1.0.0 beta",
	options: {
		type: 'ns-html5',
		srcIndex: 0,
		width: 0,
		height: 0,
		minWidth: 0,
		maxWidth: 0,
		showState: true,
		refreshInterval: 0.1,	// seconds
		autoplay: false,
		initialDuration: "--:--"
	},
	
	_create: function() {
		var widget = this;
		
		widget.element
			.addClass( "ui-widget ui-widget-content ui-corner-all" );
		
		widget.$videoContainer = $('<div class="ui-nsvideo-nsplugin"></div>')
			.appendTo(widget.element);
		widget.$progressContainer = $('<div class="ui-nsvideo-progress-container ui-widget-content ui-corner-top"></div>')
			.appendTo(widget.element);
		widget.$progress = $('<div class="ui-nsvideo-progress"></div>')
			.appendTo(widget.$progressContainer);
		widget.$loadedProgress = $('<div class="ui-nsvideo-loaded-progress"></div>')
			.appendTo(widget.$progress);
		widget.$controls = $('<div class="ui-nsvideo-controls ui-widget-content ui-corner-bottom"></div>')
			.appendTo(widget.element);
		
		widget.video();
		
		// Time progress slider with load progress also
		widget.$loadedProgress
			// TODO an object that inherits progressbar should be used in order to customize min value.
			.progressbar({
				value: 0
			});
		widget.$progress
			.slider({
					value: 0,
					min: 0,
					max: 1000,
					slide: function(event, ui) {
						widget.videoPlugin('crtTime', [ui.value]);
						widget.videoPlugin('refreshTime');
						widget.videoPlugin('refreshState');
					}
			});
		
		// Play / Pause
		$('<button class="ui-nsvideo-play ui-nsvideo-button ui-nsvideo-control-left">Play / Pause</button>')
			.appendTo(widget.$controls)
			.button({
				text: false,
				icons: { primary: "ui-icon-play" }
			})
			.click(function() {
				widget.videoPlugin('togglePlay');
			});
		
		// Time information (current and total)
		widget.$time = $('<div class="ui-nsvideo-time ui-nsvideo-text ui-nsvideo-control-left">00:00 / ' + widget.options.initialDuration + '</div>')
			.appendTo(widget.$controls);
			
		// Full screen
		$('<button class="ui-nsvideo-fullscreen ui-nsvideo-button ui-nsvideo-control-right">Full Screen</button>')
			.appendTo(widget.$controls)
			.button({
				text: false,
				icons: { primary: "ui-icon-arrow-4-diag" }
			})
			.click(function() {
				widget.videoPlugin('fullscreen');
			});

		// Video format buttonset
		if (typeof widget.options.src == 'object')
		{
			var $formats = $('<form><div class="ui-nsvideo-formats ui-nsvideo-control-right"></div></form>')
				.appendTo(widget.$controls);
			$formats = $('.ui-nsvideo-formats', $formats[0]);
			$.each(widget.options.src, function(index, value) {
				id = widget.element.attr('id') + '-format-' + index;
				definition = value.res.substring(value.res.indexOf('x')+1)+'p';
				$('<input type="radio" id="' + id + '" name="format" />')
					.appendTo($formats)
					.attr('checked', (index == widget.options.srcIndex))
					.click(function() {
						widget.videoPlugin('pause');
						widget.srcIndex(index);
					});
				$('<label for="' + id + '">' + definition + '</label>')
					.appendTo($formats);
			});
			
			$formats.buttonset();
		}
		
		// Volume
		$('<div class="ui-nsvideo-volume ui-nsvideo-control-right"></div>')
			.appendTo(widget.$controls)
			.slider({
				range: "min",
				min: 0,
				max: 100,
				slide: function(event, ui) {
					widget.videoPlugin('volume', [ui.value]);
				}
			});
		
		// Toggle Mute
		$('<button class="ui-nsvideo-mute ui-nsvideo-button ui-nsvideo-control-right">Mute</button>')
			.appendTo(widget.$controls)
			.button({
				text: false,
				icons: { primary: "ui-icon-volume-on" }
			})
			.click(function() {
				widget.videoPlugin('toggleMute');
			});
			
		// Status information
		widget.$stateText = $('<div class="ui-nsvideo-text ui-nsvideo-control-right">...</div>')
			.appendTo(widget.$controls)
			.css('cursor', 'pointer')
			.click(function() {
				widget.videoPlugin('refreshAll');
			});
		if (! widget.options.showState)
			widget.$stateText.hide();
		
		// Clear fix helper
		$('<div class="ui-helper-clearfix"></div>')
			.appendTo(widget.$controls);
	},

	_destroy: function() {
		
	},

	_setOption: function( key, value ) {
		// TODO
		if ( key === "TODO" ) {
			
		}

		this._super( "_setOption", key, value );
	},
	
	_leadingZeros: function(number, length) {
		if (!length)
			length = 2;
		
		var str = '';
		
		if (isNaN(number))
		{
			for (var i=0; i<length; i++)
				str += '-';
			return str;
		}
		
		str += number;
		while (str.length < length) 
		{
			str = '0' + str;
		}

		return str;
	},
	
	video: function() {
		widget = this;
		
		// Select video source.
		// If src option is string, that's the source.
		// If src is an object, there is a list of associative arrays, each
		// one having the mandatory keys "src" and "res" (resolution).
		var src = widget.crtSrc();
		if (src == null)
			return widget;
		
		widget.$videoContainer.html('');
		
		var width = widget.options.width;
		var height = widget.options.height;
		
		// HTML5
		if (widget.options.type == 'ns-html5'
			|| widget.options.type == 'html5')
		{
			widget.$video = $('<video id="' + widget.element.attr('id') + '-video" src="' + src + '"' + (width == 0 ? '' : ' width="' + width + '"') + (height == 0 ? '' : ' height="' + height + '"') + ' preload="auto"' + (widget.options.autoplay ? ' autoplay="autoplay"' : '') + '>'
				+'Error: Your browser does not support HTML5 or the video format!'
			+'</video>')
				.appendTo(widget.$videoContainer)
				.bind({
					ended: function() {
						widget.html5.pause();
						widget.html5.refreshState();
					},
					play: function() {
						widget.html5.play();
						widget.html5.refreshState();
					},
					pause: function() {
						widget.html5.pause();
						widget.html5.refreshState();
					},
					timeupdate: function() {
						widget.html5.refreshTime();
					},
					progress: function() {
						widget.html5.refreshLoadedProgress();
					},
					loadedmetadata: function() {
						widget.html5.refreshTime();
						widget.html5.refreshVolume();
						widget.html5.refreshState();
						widget._setWidgetWidth();
					},
					seeked: function() {
						widget.html5.play();
						widget.html5.refreshState();
					},
					volumechange: function() {
						widget.html5.refreshVolume();
					},
					
					loadstart: function() {
						widget.html5.refreshState();
					},
					suspend: function() {
						widget.html5.refreshState();
					},
					abort: function() {
						widget.html5.refreshState();
					},
					error: function() {
						widget.html5.refreshState();
					},
					emptied: function() {
						widget.html5.refreshState();
					},
					stalled: function() {
						widget.html5.refreshState();
					},
					loadeddata: function() {
						widget.html5.refreshState();
					},
					waiting: function() {
						widget.html5.refreshState();
					},
					seeking: function() {
						widget.html5.refreshState();
					},
				});
		}
		// VLC
		else if (widget.options.type == 'ns-vlc'
			|| widget.options.type == 'vlc')
		{
			var embedType;
			if (widget.options.type == 'ns-vlc')
				embedType = 'application/x-ns-stream';
			else
				embedType = 'application/x-vlc-plugin';
			
			if (navigator.appName == "Netscape")
			{
				widget.$video = $('<embed type="' + embedType + '" name="vlcVideo" id="' + widget.element.attr('id') + '-video" autoplay="' + (widget.options.autoplay ? 'yes' : 'no') + '" loop="no" width="' + (width == 0 ? '640' : width) + '" height="' + (height == 0 ? '480' : height) + '" target="' + src + '" />')
					.appendTo(widget.$videoContainer);
			}
			else
			{
				widget.$video = $('<object classid="clsid:1800B8AF-4E33-43C0-AFC7-894433C13538" width="' + (width == 0 ? '640' : width) + '" height="' + (height == 0 ? '480' : height) + '" id="' + widget.element.attr('id') + '-video" name="vlcVideo" events="True" target="">'
						+ '<param name="Src" value="' + src + '" />'
						+ '<param name="ShowDisplay" value="True" />'
						+ '<param name="Loop" value="False" />'
						+ '<param name="AutoPlay" value="' + (widget.options.autoplay ? 'True' : 'False') + '" />'
						+ '<param name="Toolbar" value="False" />'
					+ '</object>')
					.appendTo(widget.$videoContainer);
			}
		}
		
		//  BUG: this if is because of a NS-VLC bug.
		if (widget.options.type != 'ns-vlc')
			widget.$video.css('position', 'relative');
		
		// Adjust video size for auto-resizing within ranges minWidth and
		// maxWidth.
		if ( (width == 0 || height == 0)
			&& (widget.options.minWidth != 0 && widget.options.maxWidth != 0
				|| this.options.type.indexOf('vlc') != -1) )
		{
			widget.adjustVideoSizeFromMeta();
		}
		
		// Initialize video plugin
		widget.$video.ready(function() {
			widget.videoPlugin('init');
		});
		
		widget._setWidgetWidth();
	},
	
	adjustVideoSizeFromMeta: function() {
		var widget = this;
		
		if (typeof widget.options.src == 'object'
			&& (typeof widget.options.src[ widget.options.srcIndex ].res)
				!= 'undefined'
			&& (typeof widget.options.src[ widget.options.srcIndex ].dar)
				!= 'undefined')
		{
			var resolution = widget.options.src[ widget.options.srcIndex ].res;
			var dar = widget.options.src[ widget.options.srcIndex ].dar;
			var darL = parseInt(
				dar.substring(0, dar.indexOf(':')));
			var darR = parseInt(
				dar.substring(dar.indexOf(':') + 1));
			var videoHeight = parseInt(
				resolution.substring(resolution.indexOf('x') + 1));
			var videoWidth = Math.round(videoHeight * darL / darR);
			
			// Video width must be between minWidth and maxWidth pixels.
			if (widget.options.minWidth != 0 && widget.options.maxWidth != 0)
			{
				if (videoWidth > widget.options.maxWidth)
				{
					videoHeight = Math.round(widget.options.maxWidth / videoWidth
						* videoHeight);
					videoWidth = widget.options.maxWidth;
				}
				else if (videoWidth < widget.options.minWidth)
				{
					videoHeight = Math.round(widget.options.minWidth / videoWidth
						* videoHeight);
					videoWidth = widget.options.minWidth;
				}
			}
			
			widget.$video.css('width', videoWidth);
			widget.$video.css('height', videoHeight);
		}
	},
	
	_setWidgetWidth: function() {
		if (widget.$video.width() < 640)
		{
			widget.element.css('width',
							640 + 8 + 'px');
			widget.$video.css('left', 
				Math.round(widget.$videoContainer.width()/2 
				- widget.$video.width()/2)
				+ 'px');
		}
		else
		{
			widget.element.css('width',
							widget.$video.width() + 8 + 'px');
			widget.$video.css('left', '0');
		}
		
		this._trigger('resize');
	},
	
	setPlayButton: function() {
		$('button.ui-nsvideo-play', widget.element[0])
			.button('option', 'icons', { primary: "ui-icon-play" })
			.button('refresh');
	},
	setPauseButton: function() {
		$('button.ui-nsvideo-play', widget.element[0])
			.button('option', 'icons', { primary: "ui-icon-pause" })
			.button('refresh');
	},
	setMuteButton: function() {
		$('button.ui-nsvideo-mute', widget.element[0])
			.button('option', 'icons', { primary: "ui-icon-volume-off" })
			.button('refresh');
	},
	setUnmuteButton: function() {
		$('button.ui-nsvideo-mute', widget.element[0])
			.button('option', 'icons', { primary: "ui-icon-volume-on" })
			.button('refresh');
	},
	setTimeText: function(text) {
		//$('.ui-nsvideo-time', widget.element[0])
		this.$time
			.html(text);
	},
	setVolumeSlider: function(vol) {
		$('.ui-nsvideo-volume', widget.element[0])
			.slider('value', vol);
	},
	setProgressSlider: function(prog) {
		$('.ui-nsvideo-progress', widget.element[0])
			.slider('value', prog);
	},
	setLoadedProgressSlider: function(prog) {
		$('.ui-nsvideo-loaded-progress', widget.element[0])
			.progressbar('value', prog);
	},
	
	videoPlugin: function(method, args) {
		if (typeof args == 'undefined')
			args = [];
		var videoPlugin = null;
		
		if (this.options.type.indexOf('html5') != -1)
		{
			videoPlugin = this.html5;
		}
		else if (this.options.type.indexOf('vlc') != -1)
		{
			videoPlugin = this.vlc;
		}
		
		if (videoPlugin)
			return videoPlugin[method].apply(this, args);
		
		return null;
	},
	
	srcIndex: function(srcIndex) {
		var widget = this;
		
		if (typeof srcIndex == 'undefined')
			return widget.options.srcIndex;
		
		widget.options.srcIndex = srcIndex;
		
		// Refresh.
		widget.video();
		
		return widget;
	},
	
	type: function(type) {
		var widget = this;
		
		if (typeof type == 'undefined')
			return widget.options.type;
		
		widget.videoPlugin('pause');
		if (widget.vlc.timerHandle)
			clearTimeout(widget.vlc.timerHandle);
		
		widget.options.type = type;
		widget.video();
		
		// Initialize video plugin
		widget.$video.ready(function() {
			widget.videoPlugin('init');
		});
		
		return widget;
	},
	
	crtSrc: function() {
		var src;
		var widget = this;
		
		if (typeof widget.options.src == 'string')
			src = widget.options.src;
		else if (typeof widget.options.src == 'object')
		{
			if (typeof widget.options.srcIndex == 'undefined')
				return null;
			
			if (typeof widget.options.src[ widget.options.srcIndex ].src
				== 'undefined')
				return null;
			
			src = widget.options.src[ widget.options.srcIndex ].src;
		}
		
		if (widget.options.type == 'ns-html5')
			src = 'tribe://' + src;
		
		return src;
	},
	
	html5: {
		widget: this,
		//lastTime: null,
		
		ERR_STATES: {
			MEDIA_ERR_ABORTED: [1, "error: aborted"],
			MEDIA_ERR_NETWORK: [2, "network error"],
			MEDIA_ERR_DECODE: [3, "decode error"],
			MEDIA_ERR_SRC_NOT_SUPPORTED: [4, "error: source not supported"]
		},
		
		NETWORK_STATES: {
			NETWORK_EMPTY: [0, "no data from network"],
			NETWORK_IDLE: [1, ""],
			NETWORK_LOADING: [2, "loading..."],
			NETWORK_LOADED: [3, "loading completed"],
			NETWORK_NO_SOURCE: [4, "network: no source"]
		},
		
		READY_STATES: {
			HAVE_NOTHING: [0, "please wait..."],
			HAVE_METADATA: [1, ""],
			HAVE_CURRENT_DATA: [2, ""],
			HAVE_FUTURE_DATA: [3, ""],
			HAVE_ENOUGH_DATA: [4, "have enough data"]
		},
		
		init: function() {
			//widget.html5.refreshAll();
			
			widget.html5.refreshState();
			
			//if (widget.options.autoplay)
			//	widget.html5.play();
		},
		
		togglePlay: function() {
			if (widget.$video[0].paused)
			{
				widget.html5.play();
			}
			else
			{
				widget.html5.pause();
			}
		},

		play: function() {
			if (widget.$video[0].paused)
				widget.$video[0].play();
			
			widget.setPauseButton();
			
			return widget;
		},
		
		pause: function() {
			if (!widget.$video[0].paused)
				widget.$video[0].pause();
			
			widget.setPlayButton();

			return widget;
		},
		
		toggleMute: function() {
			if (!widget.$video[0].muted)
			{
				widget.html5.mute();
			}
			else
			{
				widget.html5.unmute();
			}
		},
		  
		mute: function() {
			if (!widget.$video[0].muted)
				widget.$video[0].muted = true;
			
			widget.setMuteButton();
			
			return widget;
		},
		
		unmute: function() {
			if (widget.$video[0].muted)
				widget.$video[0].muted = false;
			
			widget.setUnmuteButton();
			
			return widget;
		},
		
		/**
		* Volume value is expressed in percents.
		*/
		volume: function(vol) {
			if (typeof vol == 'undefined')
				return Math.round(widget.$video[0].volume * 100);
			
			widget.html5.unmute();
			widget.$video[0].volume = vol / 100;
			
			return widget;
		},
		
		/**
		 * Seek position is a value between 0 and 1000.
		 */
		crtTime: function(pos) {
			// getter
			if (typeof pos == 'undefined')
			{
				var crtTime = widget.$video[0].currentTime;
				var totTime = widget.$video[0].duration;
				if (isNaN(totTime) || totTime == 0)
					return 0;
				else
					return Math.round(crtTime / totTime * 1000.0);
			}
			
			// setter
			widget.$video[0].currentTime = 
				pos / 1000 * widget.$video[0].duration;
		},
		  
		refreshAll: function() {
			widget.html5.refreshState();
			widget.html5.refreshVolume();
			widget.html5.refreshLoadedProgress();
			widget.html5.refreshTime();
		},
		
		refreshTime: function() {
			if (widget.$video[0].seeking)
				return widget;
			
			var crtTime = widget.$video[0].currentTime;
			var totTime = widget.$video[0].duration;
			
			// Refresh only at refreshInterval seconds to save CPU time.
			var delta = crtTime - widget.html5.lastTime;
			if (typeof widget.html5.lastTime !== "undefined"
				&& delta >= 0 && delta < widget.options.refreshInterval)
				return widget;
			widget.html5.lastTime = crtTime;
			
			// Current time string
			var crtH = Math.floor(crtTime / 3600);
			var crtM = Math.floor((crtTime / 60) % 60);
			var crtS = Math.floor(crtTime % 60);
			var strCrtTime = 
				(crtH == 0 ? '' : (widget._leadingZeros(crtH) + ':'))
				+ widget._leadingZeros(crtM) + ':' + widget._leadingZeros(crtS);
				
			// Total time string
			var totH = Math.floor(totTime / 3600);
			var totM = Math.floor((totTime / 60) % 60);
			var totS = Math.floor(totTime % 60);
			var strTotTime = 
				(totH == 0 || isNaN(totH) ? '' : (widget._leadingZeros(totH) + ':'))
				+ widget._leadingZeros(totM) + ':' + widget._leadingZeros(totS);
			
			widget.setTimeText('' + strCrtTime + ' / ' + strTotTime);
			
			// Update time progress slider.
			widget.html5.refreshProgress();
			
			return widget;
		},
		
		_state: function(type, code) {
			var r;
			$.each(widget.html5[type + '_STATES'], function(index, value) {
				if ('' + code == '' + value[0])
				{
					r = value;
					return false;
				}
			});
			
			return r;
		},
		
		refreshState: function() {
			var err = "";
			var normal = "";
			var network = "";
			var ready = "";
			var state = "";
			
			if (widget.$video[0].error)
				err = widget.html5._state('ERR',
										  widget.$video[0].error.code)[1];
			  
			if (! widget.$video[0].paused)
				normal = "playing...";
			else
			{
				normal = "paused";
			}
			if (widget.$video[0].seeking)
				normal = "seeking";
			if (widget.$video[0].ended)
				normal = "ended";
			
			network = widget.html5._state('NETWORK',
									widget.$video[0].networkState)[1]; 
			
			ready = widget.html5._state('READY',
									widget.$video[0].readyState)[1];
			
			if (err !== "")
				state = err;
			else
			{
				state = normal;
				
				if (normal !== "" && (network !== "" || ready !== "") )
					state += ' / ';
				
				if (network !== "")
					state += network;
				
				if (network !== "" && ready !== "")
					state += ' / ';
				
				if (ready !== "")
					state += ready;
			}
			
			widget.$stateText
				.html(state);
			
			return widget;
		},

		refreshVolume: function() {
			var vol;
			
			if (widget.$video[0].muted)
				vol = 0;
			else
				vol = Math.floor(widget.$video[0].volume * 100);
			
			widget.setVolumeSlider(vol);
			
			return widget;
		},
		
		refreshProgress: function() {
			widget.setProgressSlider(widget.html5.crtTime());
			
			return widget;
		},
		
		/**
		* Supported for Firefox 4.0 or later.
		*/
		refreshLoadedProgress: function() {
			// Return if buffering status not available in browser.
			if (typeof widget.$video[0].buffered == 'undefined'
				|| widget.$video[0].buffered.length === 0)
				return widget;
			
			var loadedTime = widget.$video[0].buffered.end(0);
			var totTime = widget.$video[0].duration;
			var percent;
			if (isNaN(totTime) || totTime == 0)
				percent = 0
			else
				percent = Math.floor(loadedTime / totTime * 100);
			
			widget.setLoadedProgressSlider(percent);
			
			return widget;
		},

		fullscreen: function() {
			alert('Your web browser does not support switching to full screen in HTML5 mode with this button. You can switch to full screen manually by right clicking on the video and choosing "Full Screen" from the popup menu.');
		}
	},
	
	vlc: {
		widget: this,
		timerHandle: null,
		idleRefreshInterval: 1, // seconds
		
		STATES: {
			IDLE: [0, "idle"],
			OPENING: [1, "opening..."],
			BUFFERING: [2, "buffering..."],
			PLAYING: [3, "playing..."],
			PAUSED: [4, "paused"],
			STOPPING: [5, "stopping..."],
			ENDED: [6, "ended"],
			ERROR: [7, "error!"]
		},
		
		init: function() {
			if (widget.options.autoplay)
				widget.vlc.play();
			widget.vlc.refreshAll();
		},
		
		togglePlay: function() {
			if (! widget.$video[0].playlist.isPlaying)
			{
				widget.vlc.play();
			}
			else
			{
				widget.vlc.pause();
			}
		},
		
		play: function() {
			if (! widget.$video[0].playlist.isPlaying)
				widget.$video[0].playlist.play();
			
			widget.setPauseButton();
			
			// Schedule information refreshment at refreshInterval seconds.
			if (! widget.vlc.timerHandle)
				widget.vlc.timerHandle = setTimeout(widget.vlc.refreshHandler, 
										widget.options.refreshInterval * 1000);
				
			widget.vlc.refreshState();
			
			return widget;
		},
		
		pause: function() {
			if (widget.$video[0].playlist.isPlaying)
				widget.$video[0].playlist.togglePause();
			
			widget.setPlayButton();
			
			// Cancel information refreshment scheduling.
			clearTimeout(widget.vlc.timerHandle);
			widget.vlc.timerHandle = null;
			
			widget.vlc.refreshState();

			return widget;
		},
		
		toggleMute: function() {
			if (! widget.$video[0].audio.mute)
			{
				widget.vlc.mute();
			}
			else
			{
				widget.vlc.unmute();
			}
		},
		  
		mute: function() {
			if (! widget.$video[0].audio.mute)
				widget.$video[0].audio.toggleMute();
			
			widget.setMuteButton();
			
			widget.vlc.refreshVolume();
			
			return widget;
		},
		
		unmute: function() {
			if (widget.$video[0].audio.mute)
				widget.$video[0].audio.toggleMute();
			
			widget.setUnmuteButton();
			
			widget.vlc.refreshVolume();
			
			return widget;
		},
		
		/**
		* Volume value is expressed in percents.
		*/
		volume: function(vol) {
			if (typeof vol == 'undefined')
				return Math.round(widget.$video[0].audio.volume);
			
			widget.vlc.unmute();
			widget.$video[0].audio.volume = vol;
			
			return widget;
		},
		
		/**
		 * Seek position is a value between 0 and 1000.
		 */
		crtTime: function(pos) {
			// getter
			if (typeof pos == 'undefined')
			{
				var crtTime = widget.$video[0].input.time;
				var totTime = widget.$video[0].input.length;
				if (isNaN(totTime) || totTime == 0)
					return 0;
				else
					return Math.round(crtTime / totTime * 1000.0);
			}
			
			// setter
			widget.$video[0].input.time = 
				pos / 1000 * widget.$video[0].input.length;
				
			widget.vlc.refreshState();
		},
		
		/**
		 * Timeout callback called at refreshInterval during playing in order
		 * to refresh information.
		 */
		refreshHandler: function() {
			if (widget.$video[0].input.state
				== widget.vlc.STATES.PLAYING[0])
			{
				widget.vlc.refreshTime();
				widget.vlc.timerHandle = setTimeout(widget.vlc.refreshHandler, 
										widget.options.refreshInterval * 1000);
			}
			else
			{
				if (widget.$video[0].input.state == widget.vlc.STATES.ENDED[0])
				{
					widget.vlc.pause();
					try {
						widget.$video[0].playlist.stop();
					} catch(e) {
						console.log('Exception: ' + e);
					}
				}
				
				widget.vlc.refreshTime();
				widget.vlc.timerHandle = setTimeout(widget.vlc.refreshHandler, 
										widget.vlc.idleRefreshInterval * 1000);
			}
			
			widget.vlc.refreshState();
		},
		
		refreshAll: function() {
			widget.vlc.refreshState();
			widget.vlc.refreshVolume();
			widget.vlc.refreshLoadedProgress();
			
			try {
				widget.vlc.refreshTime();
			} catch(e) {
				console.log('Exception: ' + e);
				widget.$time.html('00:00 / ' + widget.options.initialDuration);
			}
		},
		
		refreshTime: function() {
			// TODO while seeking (maybe not necessary for VLC)
// 			if (widget.$video[0].seeking)
// 				return widget;
			
			// Time values in seconds.
			var crtTime = widget.$video[0].input.time / 1000.0;
			var totTime = widget.$video[0].input.length / 1000.0;
			//var crtTime = widget.$video[0].input.position * totTime;
			
			// Current time string
			var crtH = Math.floor(crtTime / 3600);
			var crtM = Math.floor((crtTime / 60) % 60);
			var crtS = Math.floor(crtTime % 60);
			var strCrtTime = 
				(crtH == 0 ? '' : (widget._leadingZeros(crtH) + ':'))
				+ widget._leadingZeros(crtM) + ':' + widget._leadingZeros(crtS);
				
			// Total time string
			var totH = Math.floor(totTime / 3600);
			var totM = Math.floor((totTime / 60) % 60);
			var totS = Math.floor(totTime % 60);
			var strTotTime = 
				(totH == 0 || isNaN(totH) ? '' : (widget._leadingZeros(totH) + ':'))
				+ widget._leadingZeros(totM) + ':' + widget._leadingZeros(totS);
			
			widget.setTimeText('' + strCrtTime + ' / ' + strTotTime);
			
			// Update time progress slider.
			widget.vlc.refreshProgress();
			
			return widget;
		},
		
		_state: function(code) {
			var r;
			$.each(widget.vlc.STATES, function(index, value) {
				if ('' + code == '' + value[0])
				{
					r = value;
					return false;
				}
			});
			
			return r;
		},
		
		refreshState: function() {
			widget.$stateText
				.html(widget.vlc._state(widget.$video[0].input.state)[1]);
				
			return widget;
		},

		refreshVolume: function() {
			var vol;
			
			if (widget.$video[0].audio.mute)
				vol = 0;
			else
				vol = Math.floor(widget.$video[0].audio.volume);
			
			widget.setVolumeSlider(vol);
			
			return widget;
		},
		
		refreshProgress: function() {
			widget.setProgressSlider(widget.vlc.crtTime());
			
			return widget;
		},
		
		/**
		* Not supported for VLC.
		*/
		refreshLoadedProgress: function() {
			// TODO Currently not possible through VLC API.
			
			return widget;
		},

		fullscreen: function() {
			widget.$video[0].video.toggleFullscreen();
		}
	}
});

})( jQuery );