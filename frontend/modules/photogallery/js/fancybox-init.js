 $(window).load(function()
 {
    $('.js-photogallery-lightbox').each(function()
    {
    	var $this = $(this);
    	var lightboxId = $this.data('id');
    	var lightboxSettings = jsFrontend.data.get('photogallery.lightbox_settings_' + lightboxId);

    	var show_close_button = lightboxSettings.show_close_button == 'true'; 
    	var show_arrows = lightboxSettings.show_arrows == 'true';
    	var show_caption = lightboxSettings.show_caption == 'true';
    	var caption_type = lightboxSettings.caption_type;
    	var padding = parseInt(lightboxSettings.padding); 
    	var margin = parseInt(lightboxSettings.margin);
    	var modal = lightboxSettings.modal == 'true';
    	var close_click = lightboxSettings.close_click == 'true';
    	var media_helper = lightboxSettings.media_helper == 'true';
    	var show_hover_icon = lightboxSettings.show_hover_icon == 'true';
    	
    	var navigation_effect = lightboxSettings.navigation_effect;
    	var open_effect = lightboxSettings.open_effect;
    	var close_effect = lightboxSettings.close_effect;
		var play_speed = parseInt(lightboxSettings.play_speed);
		var loop = lightboxSettings.loop == 'true';

		var show_thumbnails = lightboxSettings.show_thumbnails == 'true';
		var thumbnails_position = lightboxSettings.thumbnails_position;
		var thumbnail_navigation_width = parseInt(lightboxSettings.thumbnail_navigation_width);
		var thumbnail_navigation_height = parseInt(lightboxSettings.thumbnail_navigation_height);

		var show_overlay = lightboxSettings.show_overlay == 'true';
		var overlay_color = lightboxSettings.overlay_color;

		var fancybox = $('a.js-photogallery-lightbox-' + lightboxId);

		var h = {};

		if(show_hover_icon)
		{
			fancybox.addClass('linkOverlay').linkIcon({background:{css:{'opacity': 0.65}}});
		}
	
		if(show_thumbnails)
		{
			h.thumbs = {
						width: thumbnail_navigation_width,
						height: thumbnail_navigation_height,
						position:  thumbnails_position
					};
		}

		if(show_overlay)
		{
			h.overlay = { css: {'background': overlay_color } };
		}
		else
		{
			h.overlay = null;
		}

		if(media_helper)
		{
			h.media = {};
		}

		if(show_caption)
		{
			h.title = {	type: caption_type };
		}
		else
		{
			h.title = null;
		}

		fancybox.fancybox({
			nextEffect: navigation_effect,
			prevEffect: navigation_effect,
			openEffect: open_effect,
			closeEffect: close_effect,
			closeBtn: show_close_button,
			playSpeed: play_speed,
			loop: loop,
			arrows: show_arrows,
			closeClick: close_click,
			modal: modal,
			padding: padding,
			margin: margin,
			tpl: {
				closeBtn : '<a title="Close" class="fancybox-item fancybox-close linkedImage" href="javascript:;"></a>',
				next     : '<a title="Next" class="fancybox-nav fancybox-next linkedImage" href="javascript:;"><span></span></a>',
				prev     : '<a title="Previous" class="fancybox-nav fancybox-prev linkedImage" href="javascript:;"><span></span></a>'
			},
			helpers: h,
			beforeShow: function()
			{
				// Get rich titles
				var currentElement = this.element;
				var next = $(currentElement).next();
				if(next.length && next.hasClass('photogallery-lighbox-caption')) this.title = next.html();

				// Disable right click
				$.fancybox.wrap.bind("contextmenu", function (e) {
					return false; 
				});
			}
		});

	 	var imageId = utils.url.getGetValue('{$actLightboxImage}');
		if(imageId) $this.find("a[data-image-id=" + imageId + "]").trigger('click');

	});   
 });