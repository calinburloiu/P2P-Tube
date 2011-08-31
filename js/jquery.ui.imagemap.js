/*
 * jQuery UI ImageMap 1.0.0 beta
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

$.widget( "ui.imagemap", {
	version: "1.0.0 beta",
	options: {

	},
	
	_create: function() {
		var widget = this;
		
		$('div.ui-imagemap-image', widget.element)
			.css('position', 'relative')
			.css('width', $('div.ui-imagemap-image > img', widget.element).css('width'))
			.css('height', $('div.ui-imagemap-image > img', widget.element).css('height'))
			;
		
		widget.$marker = $('<div></div>')
			.appendTo('div.ui-imagemap-image', widget.element);
		widget.$marker
			.hide()
			.css('position', 'absolute')
			.css('outline', '2px solid red')
			.css('background', 'transparent');
		
		$('.ui-imagemap-title', widget.element)
			.click(function() {
				var $title = $(this);
				
				if (typeof $title.data('top') == 'undefined')
					return false;
				if (typeof $title.data('left') == 'undefined')
					return false;
				
				var top = $title.data('top');
				var left = $title.data('left');
				var width, height;
				
				if (typeof $title.data('width') == 'undefined')
					width = '1px';
				else
					width = $title.data('width');
				if (typeof $title.data('height') == 'undefined')
					height = '1px';
				else
					height = $title.data('height');
				
				widget.$marker
					.show()
					.css('top', top)
					.css('left', left)
					.css('width', width)
					.css('height', height);
				
			});
	},

	_destroy: function() {
		this.element.html('');
	},
	
	_setOption: function( key, value ) {
		// TODO
		if ( key === "TODO" ) {
			
		}

		this._super( "_setOption", key, value );
	}
});

})( jQuery );