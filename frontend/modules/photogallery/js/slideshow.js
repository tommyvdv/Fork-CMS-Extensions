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
				if(elPager.length) return elPager.find('li:eq(' + idx + ') a');
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