(function($) {
	$.fn.linkIcon = function(options)
	{	
		// Options
		var opts = $.extend(true, {
			a: {
				css:{
					'position': 'relative'
					//'display': 'inline-block'
				}
			},
			container:{ 
				css:{
					'margin-top': '0',
					'margin-left': '0'
				},
				klasse: 'linkOverlayContainer'
			},
			background:{ 
				css:{
					'opacity': .65
				},
				klasse: 'linkOverlayBackground'
			},
			icon:{ 
				css:{},
				klasse: 'linkOverlayIcon'
			}
		}, options);
		
		// Store $(this)
		var $this = $(this);
		
		// Event
		$this.hover(function ()
		{				
			// First image
			var image = $('img:first', this);
			
			// Creating elements
			var elContainer = $('<div>').addClass(opts.container.klasse);
			var elBackground = $('<div>').addClass(opts.background.klasse);
			var elIcon = $('<div>').addClass(opts.icon.klasse);
			
			// Setting size
			elContainer.width(image.width()).height(image.height());

			// Styling
			//$this.css(opts.a.css);
			elContainer.css(opts.container.css);
			elBackground.css(opts.background.css);
			elIcon.css(opts.icon.css);
			
			// Prepending
			$(this).prepend(elContainer);
			elContainer.prepend(elIcon);
			elContainer.prepend(elBackground);
		},
		function ()
		{
			// Remove container
			$this.find('div.' + opts.container.klasse).remove();
		}); 
	};

})(jQuery);