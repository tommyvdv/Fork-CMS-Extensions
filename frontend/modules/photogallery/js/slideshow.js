$(document).ready(function()
{
	$('.photoGallerySlideshowAlbum').each(function(index, value)
	{
		var elPager = $(this).parent().find('.photoGallerySlideshowPager');
		
	    $($(this)).cycle(
		{
			fx: 'fade',
			pager: elPager,
			activePagerClass: 'selected',
		    pagerAnchorBuilder: function(idx, slide)
			{ 
				return elPager.find('li:eq(' + idx + ') a');
			},
			after: function(currentImage, nextImage, options)
			{
				var captions = $(this).parent().parent().find('.photoGallerySlideshowCaptions').children().hide();
				var caption = $(captions[options.currSlide]);
				caption.show();
			}
		});
	}); 
});


/*!
* Simplest jQuery Slideshow Plugin v1.0
* @link http://github.com/mathiasbynens/Simplest-jQuery-Slideshow
* @author Mathias Bynens <http://mathiasbynens.be/>
*/
/*
;(function($) {
	$.fn.slideshow = function(options) {
		options = $.extend({
			timeout: 3000,
			speed: 400 // 'normal'
		}, options);
		// We loop through the selected elements, in case the slideshow was called on more than one element e.g. `$('.foo, .bar').slideShow();`
		return this.each(function() {
			// Inside the setInterval() block, `this` references the window object instead of the slideshow container element, so we store it inside a var
			var $elem = $(this);
			$elem.children().eq(0).appendTo($elem).show();
			// Iterate through the slides
			if($elem.children().length > 1)
			{
				setInterval(function() {
					$elem.children().eq(0)
					// Hide the current slide and append it to the end of the image stack
					.hide().appendTo($elem) // As of jQuery 1.3.2, .appendTo() returns the inserted element
					// Fade in the next slide
					.fadeIn(options.speed)
				}, options.timeout);
			}
			
		});
	};
})(jQuery);


$(document).ready(function()
{
	$('.photoGallerySlideshow li').hide().parent().slideshow({ timeout: 7000, speed: 600 });
});
*/
