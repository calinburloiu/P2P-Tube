/*
 * jQuery UI Thumbs 1.0.0 beta
 * 
 * Creates an image thumbnail slideshow for an image tag when mouse is over.
 *
 * Copyright 2011, Călin-Andrei Burloiu
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * Depends:
 *   jquery.ui.core.js
 *   jquery.ui.widget.js
 */
(function( $, undefined ) {

$.widget( "ui.thumbs", {
	version: "1.0.0 beta",
	options: {
		// in milliseconds
		period: 667,
		src: []
	},
	
	_create: function() {
		var widget = this;
		
		if (typeof(widget.element.data('src')) != 'undefined')
			widget.options.src =
					widget.options.src.concat(widget.element.data('src'));
				
		widget.index = 0;
		widget.count = widget.options.src.length;
		widget.defaultSrc = widget.element.attr('src');
		
		widget._preloadImages();
		
		widget.element
			.mouseover(function() {
				// Initial frame
				widget.index = 0;
				widget.element.attr('src', widget.options.src[0]);
				
				widget.intervalHandler = setInterval(function() {
					widget.index = (widget.index + 1) % widget.count;
					widget.element.attr('src',
							widget.options.src[widget.index]);
				}, widget.options.period);
				
			})
			.mouseout(function() {
				clearInterval(widget.intervalHandler);
				widget.element.attr('src', widget.defaultSrc);
			});
	},
	
	_preloadImages: function() {
		var images = new Array();
		for (var i = 0; i < this.options.src.length; i++)
		{
			images[i] = new Image();
			images[i].src = this.options.src[i];
		}
	}
	
});

})( jQuery );

