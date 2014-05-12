 $(window).load(function()
 {
    $('.js-photogallery-slideshow-wrapper').each(function()
    {
    	var $this = $(this);
    	var slideshowId = $this.data('id');
    	var slideshowSettings = jsFrontend.data.get('photogallery.slideshow_settings_' + slideshowId);
    	
        var controlNav = slideshowSettings.show_pagination == 'true';
        var controlType = slideshowSettings.pagination_type;
        var controlTypeIsThumbnails = (controlType == 'thumbnails');
        var slideshow = true;
        var animationLoop = true;
        var sync = '';
        var randomize = slideshowSettings.random == 'true';
        var directionNav = slideshowSettings.show_arrows == 'true';
        var slider = '#photogallery-flexslider-id-' + slideshowId;
        var pauseOnHover = slideshowSettings.pause_on_hover == 'true';

        var animation = slideshowSettings.animation;
        var slideshow_item_width = parseInt(slideshowSettings.slideshow_item_width);

        if(jQuery.type(slideshow_item_width) == 'number')
        {
            if( slideshow_item_width > 0) animation = 'slide';
        }

        if(controlTypeIsThumbnails)
        {
            controlNav = false;
            slideshow = false;
            animationLoop = false;
            sync = '#photogallery-flexslider-navigation-id-' + slideshowId;
            randomize = false;
            directionNav = directionNav;
            pauseOnHover = false;

            $(sync).flexslider({
                animation: "slide",
                controlNav: controlNav,
                animationLoop: animationLoop,
                slideshow: slideshow,
                itemWidth: 150,
                itemMargin: 5,
                asNavFor: slider
            });
        }

    	$(slider).flexslider({
    		slideshowSpeed: slideshowSettings.slideshow_speed,
			animationSpeed: slideshowSettings.animation_speed,
			randomize: randomize,
			pauseOnHover: pauseOnHover,
			directionNav: directionNav,
			controlNav: controlNav,
            sync: sync,
            animationLoop: animationLoop,
            slideshow: slideshow,
            animation: animation,
            itemWidth: slideshow_item_width
    	});

    })
 });