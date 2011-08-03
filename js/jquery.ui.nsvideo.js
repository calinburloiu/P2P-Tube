/*
 * jQuery UI NS-Video @VERSION
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

function padWithZeros(number, length)
{
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
	}

(function( $, undefined ) {

$.widget( "ui.nsvideo", {
	version: "@VERSION",
	options: {
		type: 'ns-html5',
		//src: 'http://10.38.128.248/devel/data/torrents/rtt01a_600p.ogv.tstream' // TODO no default src
		//src: 'http://10.38.128.248/devel/data/torrents/IndependentaRomaniei_240p.ogv.tstream' // TODO no default src
	},

	min: 0,

	_create: function() {
		var widget = this;
		
		widget.element
			.addClass( "ui-widget ui-widget-content ui-corner-all" );
		
		widget.$nsPlugin = $('<div class="ui-nsvideo-nsplugin"></div>')
			.appendTo(widget.element);
		widget.$progressContainer = $('<div class="ui-nsvideo-progress-container ui-widget-content ui-corner-top"></div>')
			.appendTo(widget.element);
		widget.$progress = $('<div class="ui-nsvideo-progress"></div>')
			.appendTo(widget.$progressContainer);
		widget.$loadedProgress = $('<div class="ui-nsvideo-loaded-progress"></div>')
			.appendTo(widget.$progress);
		widget.$controls = $('<div class="ui-nsvideo-controls ui-widget-content ui-corner-bottom"></div>')
			.appendTo(widget.element);
		
		widget.setVideo();
		
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
					max: 1000, //Math.floor(widget.$video[0].duration),
					slide: function(event, ui) {
						widget.$video[0].currentTime = 
							ui.value / 1000 * widget.$video[0].duration;
					}
			});
		
		// Play / Pause
		$('<button class="ui-nsvideo-play ui-nsvideo-control-left">Play / Pause</button>')
			.appendTo(widget.$controls)
			.button({
				text: false,
				icons: { primary: "ui-icon-play" }
			})
			.click(function() {
				widget.togglePlay();
			});
		
		// Time information (current and total)
		$('<div class="ui-nsvideo-time ui-nsvideo-control-left">--:-- / --:--</div>')
			.appendTo(widget.$controls);

		// Video definition buttonset
		if (typeof widget.options.src == 'object')
		{
			var $definitions = $('<form><div class="ui-nsvideo-definitions ui-nsvideo-control-right"></div></form>')
				.appendTo(widget.$controls);
			$definitions = $('.ui-nsvideo-definitions', $definitions[0]);
			$.each(widget.options.src, function(index, value) {
				id = widget.element.attr('id') + '-def-' + index;
				$('<input type="radio" id="' + id + '" name="definition" />')
					.appendTo($definitions)
					.attr('checked', (index == widget.options.definition))
					.click(function() {
						widget.setDefinition(index);
					});
				$('<label for="' + id + '">' + index + '</label>')
					.appendTo($definitions);
			});
			
			$definitions.buttonset();
		}
		
		// Volume
		$('<div class="ui-nsvideo-volume ui-nsvideo-control-right"></div>')
			.appendTo(widget.$controls)
			.slider({
				range: "min",
				min: 0,
				max: 100,
				slide: function(event, ui) {
					widget.unmuteHtml5();
					widget.$video[0].volume = ui.value / 100;
				}
			});
		
		// Toggle Mute
		$('<button class="ui-nsvideo-mute ui-nsvideo-control-right">Mute</button>')
			.appendTo(widget.$controls)
			.button({
				text: false,
				icons: { primary: "ui-icon-volume-on" }
			})
			.click(function() {
				widget.toggleMute();
			});
			
		// Clear fix helper
		$('<div class="ui-helper-clearfix"></div>')
			.appendTo(widget.$controls);
	},

	_destroy: function() {
		
	},

	_setOption: function( key, value ) {
		if ( key === "TODO" ) {
			
		}

		this._super( "_setOption", key, value );
	},
	
	setVideo: function() {
		widget = this;
		
		// Select video source.
		// If src option is string, that's the source.
		// If src is an object, properties are definitions and values are
		// sources.
		var src = widget.getCrtSrc();
		if (src == null)
			return widget;
		
		widget.$nsPlugin.html('');
		
		if (widget.options.type == 'ns-html5'
			|| widget.options.type == 'html5')
		{
			widget.$video = $('<video id="vid" src="' + src + '" width="800" height="450" preload="auto">'
				+'Error: Your browser does not support HTML5 or the video format!'
			+'</video>')
				.appendTo(widget.$nsPlugin)
				.bind({
					ended: function() {
						widget.pauseHtml5();
					},
					play: function() {
						widget.playHtml5();
					},
					pause: function() {
						widget.pauseHtml5();
					},
					timeupdate: function() {
						widget.refreshTimeHtml5();
					},
					progress: function() {
						widget.refreshLoadedProgressHtml5();
					},
					loadedmetadata: function() {
						widget.refreshTimeHtml5();
						widget.refreshVolumeHtml5();
					},
					seeked: function() {
						widget.playHtml5();
					},
					volumechange: function() {
						widget.refreshVolumeHtml5();
					}
				});
		}
	},
	
	playHtml5: function() {
		if (this.$video[0].paused)
			this.$video[0].play();
		
		$('button.ui-nsvideo-play', this.element[0])
			.button('option', 'icons', { primary: "ui-icon-pause" })
			.button('refresh');
		
		return this;
	},
	
	pauseHtml5: function() {
		if (!this.$video[0].paused)
			this.$video[0].pause();
		
		$('button.ui-nsvideo-play', this.element[0])
			.button('option', 'icons', { primary: "ui-icon-play" })
			.button('refresh');
		
		return this;
	},
	
	refreshTimeHtml5: function() {
		var widget = this;
		
		if (widget.$video[0].seeking)
			return widget;
		
		var crtTime = widget.$video[0].currentTime;
		var totTime = widget.$video[0].duration;
		
		// Refresh only at 0.1 s to save CPU time.
		var delta = crtTime - widget.lastTime;
		if (typeof widget.lastTime != "undefined" && delta >= 0 && delta < 0.1)
			return widget;
		widget.lastTime = crtTime;
		
		// Current time string
		var crtH = Math.floor(crtTime / 3600);
		var crtM = Math.floor((crtTime / 60) % 60);
		var crtS = Math.floor(crtTime % 60);
		var strCrtTime = 
			(crtH == 0 ? '' : (padWithZeros(crtH) + ':'))
			+ padWithZeros(crtM) + ':' + padWithZeros(crtS);
			
		// Total time string
		var totH = Math.floor(totTime / 3600);
		var totM = Math.floor((totTime / 60) % 60);
		var totS = Math.floor(totTime % 60);
		var strTotTime = 
			(totH == 0 || isNaN(totH) ? '' : (padWithZeros(totH) + ':'))
			+ padWithZeros(totM) + ':' + padWithZeros(totS);
		
		$('.ui-nsvideo-time', widget.element[0])
			.html('' + strCrtTime + ' / ' + strTotTime);
		
		// Update time progress slider.
		widget.refreshProgressHtml5();
		
		return widget;
	},
	
	muteHtml5: function() {
		if (!this.$video[0].muted)
			this.$video[0].muted = true;
		
		$('button.ui-nsvideo-mute', this.element[0])
			.button('option', 'icons', { primary: "ui-icon-volume-off" })
			.button('refresh');
		
		return this;
	},
	
	unmuteHtml5: function() {
		if (this.$video[0].muted)
			this.$video[0].muted = false;
		
		$('button.ui-nsvideo-mute', this.element[0])
			.button('option', 'icons', { primary: "ui-icon-volume-on" })
			.button('refresh');
		
		return this;
	},
	
	refreshVolumeHtml5: function() {
		var vol;
		
		if (this.$video[0].muted)
			vol = 0;
		else
			vol = Math.floor(this.$video[0].volume * 100);
		
		$('.ui-nsvideo-volume', this.element[0])
			.slider('value', vol);
		
		return this;
	},
	
	refreshProgressHtml5: function() {
		var crtTime = this.$video[0].currentTime;
		var totTime = this.$video[0].duration;
		var permilia;
		if (isNaN(totTime) || totTime == 0)
			permilia = 0
		else
			permilia = Math.floor(crtTime / totTime * 1000);
		
		$('.ui-nsvideo-progress', this.element[0])
			.slider('value', permilia);
		
		return this;
	},
	
	/**
	 * Supported for Firefox 4.0 or later.
	 */
	refreshLoadedProgressHtml5: function() {
		// Return if buffering status not available in browser.
		if (typeof this.$video[0].buffered == 'undefined'
			|| this.$video[0].buffered.length === 0)
			return this;
		
		var loadedTime = this.$video[0].buffered.end(0);
		var totTime = this.$video[0].duration;
		var percent;
		if (isNaN(totTime) || totTime == 0)
			percent = 0
		else
			percent = Math.floor(loadedTime / totTime * 100);
		
		$('.ui-nsvideo-loaded-progress', this.element[0])
			.progressbar('value', percent);
		
		return this;
	},
	
	togglePlay: function() {
		if (this.options.type.indexOf('html5') != -1)
		{
			if (this.$video[0].paused)
			{
				this.playHtml5();
			}
			else
			{
				this.pauseHtml5();
			}
		}
		else if (this.options.type == 'ns-vlc')
		{
			
		}
		
		return this;
	},
	
	toggleMute: function() {
		if (this.options.type.indexOf('html5') != -1)
		{
			if (!this.$video[0].muted)
			{
				this.muteHtml5();
			}
			else
			{
				this.unmuteHtml5();
			}
		}
		else if (this.options.type == 'ns-vlc')
		{
			
		}
		
		return this;
	},
	
	getCrtSrc: function() {
		var src;
		var widget = this;
		
		if (typeof widget.options.src == 'string')
			src = widget.options.src;
		else if (typeof widget.options.src == 'object')
		{
			if (typeof widget.options.definition == 'undefined')
				return null;
			
			if (typeof widget.options.src[ widget.options.definition ]
				== 'undefined')
				return null;
			
			src = widget.options.src[ widget.options.definition ];
		}
		
		if (widget.options.type == 'ns-html5')
			src = 'tribe://' + src;
		
		return src;
	},
	
	setDefinition: function(definition) {
		if (this.options.type.indexOf('html5') != -1)
		{
			this.options.definition = definition;
			this.setVideo();
		}
		
		return this;
	}
});

})( jQuery );