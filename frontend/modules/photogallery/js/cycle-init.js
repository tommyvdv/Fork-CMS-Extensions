$(document).ready(function()
{
	$('.photogallerySlideshowAlbum').each(function(index, value)
	{
		var elPager = $(this).parent().find('.photogallerySlideshowPager ul');
		
	    $($(this)).cycle(
		{
			fx: 'fade',
			pager: elPager,
			activePagerClass: 'selected',
		    pagerAnchorBuilder: function(idx, slide)
			{ 
				if(elPager.length > 0) return elPager.find('li:eq(' + idx + ') a');
			},
			after: function(currentImage, nextImage, options)
			{
				var captions = $(this).parent().parent().find('.photogallerySlideshowCaptions').children().hide();
				var caption = $(captions[options.currSlide]);
				caption.show();
			}
		});
	}); 
});