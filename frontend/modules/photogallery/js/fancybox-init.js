$(document).ready(function() {
	$(".photogalleryLightbox li a, .photogalleryDetailLightbox li a").fancybox({
		nextEffect: 'none', // elastic, fade or none. default: elastic
		prevEffect: 'none',
		closeBtn: false,
		closeClick: false,
		modal: false, // false is default
		tpl: {
			closeBtn : '<a title="Close" class="fancybox-item fancybox-close linkedImage" href="javascript:;"></a>',
			next     : '<a title="Next" class="fancybox-nav fancybox-next linkedImage" href="javascript:;"><span></span></a>',
			prev     : '<a title="Previous" class="fancybox-nav fancybox-prev linkedImage" href="javascript:;"><span></span></a>'
		},
		helpers:
		{ 
			media: {}, // Load media helper
			title: {
				type: 'outside' // 'float', 'inside', 'outside' or 'over'
			},

			overlay: {
				showEarly : false,
				speedIn: 0,
				speedOut: 0,
				opacity: 1,
				css: {
					cursor: 'pointer',
					'background': 'rgba(255, 255, 255, 0.85)' //Browsers who don`t support rgba will fall back to default color value defined at CSS file
				},
				closeClick: true
			}
			/*
			buttons : {
				tpl : '<div id="fancybox-buttons"><ul><li><a class="btnPrev linkedImage" title="Previous" href="javascript:;"></a></li><li><a class="btnPlay linkedImage" title="Start slideshow" href="javascript:;"></a></li><li><a class="btnNext linkedImage" title="Next" href="javascript:;"></a></li><li><a class="btnToggle linkedImage" title="Toggle size" href="javascript:;"></a></li><li><a class="btnClose linkedImage" title="Close" href="javascript:jQuery.fancybox.close();"></a></li></ul></div>'
			},
			*/
		
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

	// if actLightboxImage parameter is set, show image on page load
	var imageId = utils.url.getGetValue('{$actLightboxImage}');
	if(imageId) $(".photogalleryDetailLightbox li a[data-image-id=" + imageId + "]").trigger('click');
});