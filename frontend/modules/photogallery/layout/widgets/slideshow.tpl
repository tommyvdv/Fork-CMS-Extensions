{* Images *}
	{option:widgetPhotogallerySlideshow.images}
		<div class="photogallerySlideshowWrapper photogallerySlideshowWrapperId{$widgetPhotogallerySlideshow.id}">
			
			{* Images *}
			<div class="flexslider" data-id="{$widgetPhotogallerySlideshow.id}">
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

							<img src="{$var|createimage:{$widgetPhotogallerySlideshow.images.set_id}:{$widgetPhotogallerySlideshow.images.filename}:{$widgetPhotogallerySlideshowResolution.width}:{$widgetPhotogallerySlideshowResolution.height}:{$widgetPhotogallerySlideshowResolution.method}}" />
							
							{* With internal link *}
							{option:widgetPhotogallerySlideshow.images.data.internal_link}
							</a>
							{/option:widgetPhotogallerySlideshow.images.data.internal_link}

							{* With external link *}
							{option:widgetPhotogallerySlideshow.images.data.external_link}
							</a>
							{/option:widgetPhotogallerySlideshow.images.data.external_link}

							<div class="caption">

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
			
		</div>
	{/option:widgetPhotogallerySlideshow.images}