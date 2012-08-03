$(document).ready(function() {
	$(".photogalleryLightbox li a, .photogalleryDetailLightbox li a").fancybox({
		nextEffect: 'elastic', // elastic, fade or none. default: elastic
		prevEffect: 'elastic',
		closeBtn: false,
		closeClick: false,
		modal: false,
		tpl: {
			closeBtn: '<div title="Close" class="fancybox-item fancybox-close linkedImage"></div>',
			next: '<a title="Next" class="fancybox-item fancybox-next linkedImage"><span></span></a>',
			prev: '<a title="Previous" class="fancybox-item fancybox-prev linkedImage"><span></span></a>'
		},
		helpers:
		{ 
			media: {}, // Load media helper
			title: {
				type: 'outside' // float, inside, outside
			},
			overlay: {
				speedIn: 0,
				speedOut: 0,
				opacity: 0.85,
				css: {
					cursor: 'pointer',
					'background-color': 'rgba(0, 0, 0, 0.85)' //Browsers who don`t support rgba will fall back to default color value defined at CSS file
				},
				closeClick: true
			},
			buttons : {
				tpl: '<div id="fancybox-buttons"><ul><li><a class="btnPrev linkedImage" title="Previous" href="javascript:$.fancybox.prev();">Previous</a></li><li><a class="btnPlay linkedImage" title="Slideshow" href="javascript:$.fancybox.play();">Play</a></li><li><a class="btnNext linkedImage" title="Next" href="javascript:$.fancybox.next();">Next</a></li><li><a class="btnToggle linkedImage" title="Toggle size" href="javascript:$.fancybox.toggle();">Toggle</a></li><li><a class="btnClose linkedImage" title="Close" href="javascript:$.fancybox.close();">Close</a></li></ul></div>'
			},
			thumbs	: {
				width	: 50,
				height	: 50
			}
		},
		beforeShow: function()
		{
			// Get rich titles
			var currentElement = this.element;
			var next = $(currentElement).next();
			if(next.length && next.hasClass('caption')) this.title = next.html();
			//var currentIndex = this.index;
		}
	});

	// if actLightboxImage parameter is set, show image on page load
	var imageId = utils.url.getGetValue('{$actLightboxImage}');
	if(imageId) $(".photogalleryDetailLightbox li a[data-image_id=" + imageId + "]").trigger('click');
});