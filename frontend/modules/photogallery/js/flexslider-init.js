 $(window).load(function()
 {
    $('.flexslider').each(function()
    {
    	var $this = $(this);
    	var slideshowId = $this.data('id');
    	var slideshowSettings = jsFrontend.data.get('photogallery.slideshow_settings_' + slideshowId);
    	var controlNav = slideshowSettings.show_pagination == 'true';

    	$this.flexslider({
    		slideshowSpeed: slideshowSettings.slideshow_speed,
			animationSpeed: slideshowSettings.animation_speed,
			randomize: slideshowSettings.random == 'true',
			pauseOnHover: slideshowSettings.pause_on_hover,
			directionNav: slideshowSettings.show_arrows == 'true',
			controlNav: controlNav,
    	})
    })
 });