/*
 * jQuery UI Install NextShare Video Plugins 1.0.0 beta
 *
 * Copyright 2011, Călin-Andrei Burloiu
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 * 
 * This file constains code created by Riccardo Petrocco and Arno Bakker
 * from http://swarmplayer.p2p-next.org.
 *
 * Depends:
 *   jquery.ui.core.js
 *   jquery.ui.widget.js
 */
(function( $, undefined ) {

$.widget( "ui.nsinstall", {
	version: "1.0.0 beta",
	options: {
		type: "ns-html5",	// "ns-html5" or "ns-vlc",
		installLink: "",
		hideIfAlreadyInstalled: true,
		
		error: "none",
		msg: {
			"none": "Install ",
			"os not supported": " is not supported for your operating system",
			"browser not supported": " is not supported for your browser",
			"browser version not supported": " is not supported for your browser version",
			"already installed": " is installed"
		}
	},
	
	_create: function() {
		var widget = this;
		var platform = widget.platform();
		
		// Check platform support conditions and create content.
		// NextSharePC
		if (widget.options.type == "ns-vlc")
		{
			if (platform.osName !== 'MacOS' && platform.osName !== 'Windows')
			{
				widget.options.error = "os not supported";
			}
			else
			{
				if (platform.browserName == 'Firefox')
				{
					if (platform.browserVersion < 3.5)
						widget.options.error = "browser version not supported";
				}
				else if (platform.browserName == 'Internet Explorer')
				{
					if (platform.browserVersion < 7.0)
					{
						widget.options.error = "browser version not supported";	
					}
				}
				else
					widget.options.error = "browser not supported";
			}
			
			if (widget.isNextSharePCAlreadyInstalled())
				widget.options.error = "already installed";
			
			if (widget.options.error !== "already installed"
					|| (widget.options.error === "already installed"
					&& ! widget.options.hideIfAlreadyInstalled) )
				widget._createNextSharePCInstall();
		}
		// SwarmPlayer
		else if (widget.options.type == "ns-html5")
		{
			if (platform.osName !== 'MacOS' && platform.osName !== 'Windows'
				&& platform.osName !== 'Ubuntu Linux'
				&& platform.osName !== 'Linux')
			{
				widget.options.error = "os not supported";
			}
			else
			{
				if (platform.browserName == 'Firefox')
				{
					if (platform.browserVersion < 3.5)
						widget.options.error = "browser version not supported";
				}
				else if (platform.browserName == 'Internet Explorer')
				{
					if (platform.browserVersion < 7.0)
					{
						widget.options.error = "browser version not supported";	
					}
				}
				else
					widget.options.error = "browser not supported";
			}
			
			if (widget.isSwarmPlayerAlreadyInstalled())
				widget.options.error = "already installed";
			
			if (widget.options.error !== "already installed"
					|| (widget.options.error === "already installed"
					&& ! widget.options.hideIfAlreadyInstalled) )
				widget._createSwarmPlayerInstall();
		}
	},
	
	_createSwarmPlayerInstall: function() {
		var widget = this;
		
		if (widget.options.error == 'none')
		{
			var $installLink = $('<a id="install-swarmplayer" href="'
					+ widget.options.installLink + '">'
					+ widget.options.msg[widget.options.error] + ' SwarmPlayer Plugin</a>')
				.appendTo(widget.element);
			
			$installLink
				.button({
		            icons: {
		                primary: "ui-icon-arrowthickstop-1-s"
		            }
		        });
		}
		else if (widget.options.error == 'already installed')
		{
			$box
				.html('<div class="ui-widget">'
						+ '<div style="padding: 0 .7em;" class="ui-state-highlight ui-corner-all">' 
						+ '<p style="text-align: center"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>' 
						+ '<span id="install-swarmplayer-msg"></span></p>'
					+ '</div>'
				+ '</div>');
			
			var msg = 'SwarmPlayer ' + widget.options.msg[widget.options.error];
			$('#install-swarmplayer-msg').html(msg);
		}
		else
		{
			var $box = $('<div id="install-swarmplayer"></div>')
				.appendTo(widget.element);
			
			$box
				.html('<div class="ui-widget">'
						+ '<div style="padding: 0 .7em;" class="ui-state-error ui-corner-all">' 
						+ '<p style="text-align: center"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>' 
						+ '<span id="install-swarmplayer-msg"></span></p>'
					+ '</div>'
				+ '</div>');
			
			var msg = 'SwarmPlayer ' + widget.options.msg[widget.options.error];
			$('#install-swarmplayer-msg').html(msg);
		}
		
	},
	
	_createNextSharePCInstall: function() {
		var widget = this;
		
		if (widget.options.error == 'none')
		{
			var $installLink = $('<a id="install-nextsharepc" href="'
					+ widget.options.installLink + '">'
					+ widget.options.msg[widget.options.error] + ' NextSharePC Plugin</a>')
				.appendTo(widget.element);
			
			$installLink
				.button({
		            icons: {
		                primary: "ui-icon-arrowthickstop-1-s"
		            }
		        });
		}
		else if (widget.options.error == 'already installed')
		{
			var $box = $('<div id="install-nextsharepc"></div>')
				.appendTo(widget.element);
			
			$box
			.html('<div class="ui-widget">'
					+ '<div style="padding: 0 .7em;" class="ui-state-highlight ui-corner-all">' 
					+ '<p style="text-align: center"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>' 
					+ '<span id="install-nextsharepc-msg"></span></p>'
				+ '</div>'
			+ '</div>');
			
			var msg = 'NextSharePC ' + widget.options.msg[widget.options.error];
			$('#install-nextsharepc-msg').html(msg);
		}
		else
		{
			var $box = $('<div id="install-nextsharepc"></div>')
				.appendTo(widget.element);
			
			$box
				.html('<div class="ui-widget">'
						+ '<div style="padding: 0 .7em;" class="ui-state-error ui-corner-all">' 
						+ '<p style="text-align: center"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>' 
						+ '<span id="install-nextsharepc-msg"></span></p>'
					+ '</div>'
				+ '</div>');
			
			var msg = 'NextSharePC ' + widget.options.msg[widget.options.error];
			$('#install-nextsharepc-msg').html(msg);
		}
	},
	
	_destroy: function() {
		this.element.html('');
	},
	
	_setOption: function( key, value ) {
		// TODO
		if ( key === "TODO" ) {
			
		}

		this._super( "_setOption", key, value );
	},
	
	isNextSharePCAlreadyInstalled: function() {
		for (var i=0; i<navigator.plugins.length; i++)
		{
			if (navigator.plugins.item(i).name.indexOf('NextSharePC') != -1)
				return true;
		}
		
		return false;
	},
	
	isSwarmPlayerAlreadyInstalled: function() {
		return false;
	},
	
	platform: function() {
		var browserName="Unknown";
		var browserVersion=-1;
		var osName="Unknown";
		var archName="Unknown";
		
		if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent))
		{ 
			var ffbrowserVersion=new Number(RegExp.$1);
			browserName = "Firefox";
			browserVersion = ffbrowserVersion;
		}
		else if (/MSIE (\d+\.\d+);/.test(navigator.userAgent))
		{ 
			var iebrowserVersion=new Number(RegExp.$1);
			browserName = "Internet Explorer";
			browserVersion = iebrowserVersion;
		}
		else if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)) 
		{
		    // Must come before Safari, Chrome says it's Safari too. 
			var chbrowserVersion=new Number(RegExp.$1);
			browserName = "Chrome";
			browserVersion = chbrowserVersion;
		}
		else if (/Safari[\/\s](\d+\.\d+)/.test(navigator.userAgent))
		{ 
			var sfbrowserVersion=new Number(RegExp.$1);
			browserName = "Safari";
			browserVersion = sfbrowserVersion;
		}
		else if (/Iceweasel[\/\s](\d+\.\d+)/.test(navigator.userAgent))
		{ 
		    // Iceweasel should be compatible with Firefox
			var ffbrowserVersion=new Number(RegExp.$1);
			browserName = "Firefox";
			browserVersion = ffbrowserVersion;
		}
		else if (/Namoroka[\/\s](\d+\.\d+)/.test(navigator.userAgent))
		{ 
		    // Namoroka should be compatible with Firefox
			var ffbrowserVersion=new Number(RegExp.$1);
			browserName = "Firefox";
			browserVersion = ffbrowserVersion;
		}


		if (navigator.userAgent.indexOf("Win")!=-1) osName="Windows";
		else if (navigator.userAgent.indexOf("Mac")!=-1) osName="MacOS";
		else if (navigator.userAgent.indexOf("Ubuntu")!=-1) osName="Ubuntu Linux";
		else if (navigator.userAgent.indexOf("Linux")!=-1) osName="Linux";
		else if (navigator.userAgent.indexOf("X11")!=-1) osName="UNIX";
		
		if (navigator.userAgent.indexOf("Intel Mac")!=-1) archName="Intel";
		else if (navigator.userAgent.indexOf("PPC Mac")!=-1) archName="PowerPC";
		else if (navigator.userAgent.indexOf("Linux i686")!=-1) archName="i686";
		else if (navigator.userAgent.indexOf("Linux x86_64")!=-1) archName="x86_64";

		return {browserName: browserName, browserVersion: browserVersion, osName: osName, archName: archName};
	}
});

})( jQuery );