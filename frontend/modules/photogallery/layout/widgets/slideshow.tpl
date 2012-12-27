{* Images *}
	{option:widgetPhotogallerySlideshow.images}
		<div class="photogallerySlideshowWrapper photogallerySlideshowWrapperId{$widgetPhotogallerySlideshow.id}">
			
			{* Images *}
			<div class="flexslider">
				<ul class="slides">
					{iteration:widgetPhotogallerySlideshow.images}
							
							{* With internal link *}
							{option:widgetPhotogallerySlideshow.images.data.internal_link}
							<li>
								<a href="{$var|geturl:{$widgetPhotogallerySlideshow.images.data.internal_link.page_id}}" class="linkedImage">
									<img src="{$var|createimage:{$widgetPhotogallerySlideshow.images.set_id}:{$widgetPhotogallerySlideshow.images.filename}:{$widgetPhotogallerySlideshowResolution}}" />
								</a>

								<div class="caption">
									{option:widgetPhotogallerySlideshow.images.title}
										<h3>{$widgetPhotogallerySlideshow.images.title}</h3>
									{/option:widgetPhotogallerySlideshow.images.title}
									{$widgetPhotogallerySlideshow.images.text}
								</div>

							</li>
							{/option:widgetPhotogallerySlideshow.images.data.internal_link}
			
							{* With external link *}
							{option:widgetPhotogallerySlideshow.images.data.external_link}
							<li>
								<a href="{$widgetPhotogallerySlideshow.images.data.external_link.url}" class="linkedImage targetBlank">
									<img src="{$var|createimage:{$widgetPhotogallerySlideshow.images.set_id}:{$widgetPhotogallerySlideshow.images.filename}:{$widgetPhotogallerySlideshowResolution}}" />
								</a>

								<div class="caption">
									{option:widgetPhotogallerySlideshow.images.title}
										<h3>{$widgetPhotogallerySlideshow.images.title}</h3>
									{/option:widgetPhotogallerySlideshow.images.title}
									{$widgetPhotogallerySlideshow.images.text}
								</div>

							</li>
							{/option:widgetPhotogallerySlideshow.images.data.external_link}
		
							{* No link *}
							{option:!widgetPhotogallerySlideshow.images.data.internal_link}
								{option:!widgetPhotogallerySlideshow.images.data.external_link}
									<li>

										<img src="{$var|createimage:{$widgetPhotogallerySlideshow.images.set_id}:{$widgetPhotogallerySlideshow.images.filename}:{$widgetPhotogallerySlideshowResolution}}" />

										<div class="caption">
											{option:widgetPhotogallerySlideshow.images.title}
												<h3>{$widgetPhotogallerySlideshow.images.title}</h3>
											{/option:widgetPhotogallerySlideshow.images.title}
											{$widgetPhotogallerySlideshow.images.text}
										</div>

									</li>
								{/option:!widgetPhotogallerySlideshow.images.data.external_link}
							{/option:!widgetPhotogallerySlideshow.images.data.internal_link}
					{/iteration:widgetPhotogallerySlideshow.images}
				</ul>
			</div>
			
			{* Pager
				<div class="photogallerySlideshowPager">
					<ul>
						{iteration:widgetPhotogallerySlideshow.images}
							<li><a href="#">{$widgetPhotogallerySlideshow.images.index}</a></li>
						{/iteration:widgetPhotogallerySlideshow.images}
					</ul>
				</div>
			*}

			
		</div>
	{/option:widgetPhotogallerySlideshow.images}