{* Images *}
	{option:widgetPhotogallerySlideshow.images}
		<div class="js-photogallery-slideshow-wrapper photogallery-slideshow-wrapper photogallerys-slideshow-wrapper-id-{$widgetPhotogallerySlideshow.id}{option:widgetPhotogallerySlideshowNavigationNumbers} photogallery-slideshow-navigation-numbers{/option:widgetPhotogallerySlideshowNavigationNumbers}" data-id="{$widgetPhotogallerySlideshow.id}">
			
			{* Slides *}
			<div id="photogallery-flexslider-id-{$widgetPhotogallerySlideshow.id}"  class="flexslider photogallery-flexslider">

				<ul class="slides">
					{iteration:widgetPhotogallerySlideshow.images}
							
						<li>
							{* With internal link *}
							{option:widgetPhotogallerySlideshow.images.data.internal_link}
							<a href="{$var|geturl:{$widgetPhotogallerySlideshow.images.data.internal_link.page_id}}" class="linkedImage">
							{/option:widgetPhotogallerySlideshow.images.data.internal_link}

							{* With external link *}
							{option:widgetPhotogallerySlideshow.images.data.external_link}
							<a href="{$widgetPhotogallerySlideshow.images.data.external_link.url}" class="linkedImage targetBlank">
							{/option:widgetPhotogallerySlideshow.images.data.external_link}

							{*<img src="{$var|createimagephotogallery:{$widgetPhotogallerySlideshow.images.set_id}:{$widgetPhotogallerySlideshow.images.filename}:{$widgetPhotogallerySlideshowResolution.width}:{$widgetPhotogallerySlideshowResolution.height}:{$widgetPhotogallerySlideshowResolution.method}}" />*}

							<img src="{$var|createimageresolution:{$widgetPhotogallerySlideshow.images.set_id}:{$widgetPhotogallerySlideshow.images.filename}:{$widgetPhotogallerySlideshowResolution.resolution}}" />
							
							{* With internal link *}
							{option:widgetPhotogallerySlideshow.images.data.internal_link}
							</a>
							{/option:widgetPhotogallerySlideshow.images.data.internal_link}

							{* With external link *}
							{option:widgetPhotogallerySlideshow.images.data.external_link}
							</a>
							{/option:widgetPhotogallerySlideshow.images.data.external_link}

							<div class="caption photogallery-flexslider-caption">
								{option:widgetPhotogallerySlideshowShowCaption}
									{option:!widgetPhotogallerySlideshow.images.title_hidden}
										{option:widgetPhotogallerySlideshow.images.title}
											<h3>{$widgetPhotogallerySlideshow.images.title}</h3>
										{/option:widgetPhotogallerySlideshow.images.title}
									{/option:!widgetPhotogallerySlideshow.images.title_hidden}
									{$widgetPhotogallerySlideshow.images.text}
								{/option:widgetPhotogallerySlideshowShowCaption}
							</div>

						</li>
						
					{/iteration:widgetPhotogallerySlideshow.images}
				</ul>
			</div>


			{* Slides thumbnail navigation *}
			{option:widgetPhotogallerySlideshowNavigationThumnails}
			<div  id="photogallery-flexslider-navigation-id-{$widgetPhotogallerySlideshow.id}" class="flexslider photogallery-flexslider-thumbnail-navigation">
				<ul class="slides">
					{iteration:widgetPhotogallerySlideshow.images}
						<li>{*<img src="{$var|createimagephotogallery:{$widgetPhotogallerySlideshow.images.set_id}:{$widgetPhotogallerySlideshow.images.filename}:150:150:'crop'}" />*}<img src="{$var|createimageresolution:{$widgetPhotogallerySlideshow.images.set_id}:{$widgetPhotogallerySlideshow.images.filename}:'navigation_thumb'}" /></li>
					{/iteration:widgetPhotogallerySlideshow.images}
				</ul>
			</div>
			{/option:widgetPhotogallerySlideshowNavigationThumnails}

		</div>
	{/option:widgetPhotogallerySlideshow.images}