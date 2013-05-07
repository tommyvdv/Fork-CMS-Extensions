 $(window).load(function()
 {
    $('.photogalleryLightbox').each(function()
    {
    	var $this = $(this);
    	var lightboxId = $this.data('id');
    	var lightboxSettings = jsFrontend.data.get('photogallery.lightbox_settings_' + lightboxId);
    	
    	var show_close_button = lightboxSettings.show_close_button == 'true';  /* OK */ 
    	var show_arrows = lightboxSettings.show_arrows == 'true'; /* OK */ 
    	var show_caption = lightboxSettings.show_caption == 'true';
    	var caption_type = lightboxSettings.caption_type; /* OK */ 
    	var padding = parseInt(lightboxSettings.padding); /* OK */ 
    	var margin = parseInt(lightboxSettings.margin); /* OK */ 
    	var modal = lightboxSettings.modal == 'true'; /* OK */ 
    	var close_click = lightboxSettings.close_click == 'true'; /* OK */ 
    	var media_helper = lightboxSettings.media_helper == 'true'; /* OK */
    	
    	var navigation_effect = lightboxSettings.navigation_effect;  /* OK */
    	var open_effect = lightboxSettings.open_effect; /* OK */
    	var close_effect = lightboxSettings.close_effect; /* OK */
		var play_speed = parseInt(lightboxSettings.play_speed); /* OK */
		var loop = lightboxSettings.loop == 'true'; /* OK */

		var show_thumbnails = lightboxSettings.show_thumbnails == 'true'; /* OK */
		var thumbnails_position = lightboxSettings.thumbnails_position; /* OK */
		var thumbnail_navigation_width = parseInt(lightboxSettings.thumbnail_navigation_width); /* OK */
		var thumbnail_navigation_height = parseInt(lightboxSettings.thumbnail_navigation_height); /* OK */

		var show_overlay = lightboxSettings.show_overlay == 'true'; /* OK */
		/* niet meer nodig */ var overlay_opacity = parseInt(lightboxSettings.overlay_opacity);
		var overlay_color = lightboxSettings.overlay_color; /* OK */

		var fancybox = $(this).find('a');

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
			}
		});

		fancybox.fancybox({
			helpers : {
				overlay : null
			}
		});

		if(show_overlay) {
			fancybox.fancybox({
				helpers : {
					overlay: {
						css: {
							'background': overlay_color,
						}
					}
				}
			});
		}

		if(media_helper) {
			fancybox.fancybox({
				helpers : {
					media: {}
				}
			});
		}

		if(show_thumbnails) {
			fancybox.fancybox({
				helpers : {
					thumbs: {
						width: thumbnail_navigation_width,
						height: thumbnail_navigation_height,
						position:  thumbnails_position
					}
				}
			});
		}

		fancybox.fancybox({
			helpers : {
				title : null
			}
		});

		if(show_caption)
		{
			fancybox.fancybox({
				helpers : {
					title: {
						type: caption_type
					}
				},
				beforeShow: function()
				{
					// Get rich titles
					var currentElement = this.element;
					var next = $(currentElement).next();
					if(next.length && next.hasClass('caption')) this.title = next.html();
					//var currentIndex = this.index;

					/* Disable right click */
					$.fancybox.wrap.bind("contextmenu", function (e) {
						return false; 
					});
				}
			});
		}

	});   
 });