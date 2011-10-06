/*
 * jQuery UI AJAX Links Maker 1.0.0 beta
 * 
 * Transforms normal anchors into AJAX anchors. The AJAX URL is took from
 * href HTML attribute. Anchors to be transformed are found within elements
 * listed in option linkSelectors. The AJAX content retrieved from the server
 * is placed in the option target.
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

$.widget( "ui.ajaxLinksMaker", {
	version: "1.0.0 beta",
	options: {
		
	},
	
	_create: function() {
		var widget = this;
		
		for (i in widget.options.linkSelectors)
		{
			var selector = widget.options.linkSelectors[i];

			$(selector + ' a', widget[0])
				.each(function(index) {
					var url = $(this).attr('href');
					
					if (typeof(url) == 'undefined')
						return;
					
					$(this)
						.click(function(event) {
							event.preventDefault();
							
							$.post(
									url,
									function(data) {
										$(widget.options.target).html(data);
									});
						});
				});
				
		}
	}
	
});

})( jQuery );