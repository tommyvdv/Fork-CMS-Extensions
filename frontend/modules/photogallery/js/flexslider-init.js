 $(window).load(function()
 {
    $('.photogallerySlideshowWrapper').each(function()
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
        var slider = '#flexslider' + slideshowId;
        var pauseOnHover = slideshowSettings.pause_on_hover;

        if(controlTypeIsThumbnails)
        {
            controlNav = false;
            slideshow = false;
            animationLoop = false;
            sync = '#flexsliderNavigation' + slideshowId;
            randomize = false;
            directionNav = false;
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
    	});

    })
 });