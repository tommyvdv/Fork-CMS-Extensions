{* Images *}
	{option:widgetPhotogallerySlideshow.images}
		<div class="photoGallerySlideshowWrapper photoGallerySlideshowWrapperId{$widgetPhotogallerySlideshow.id}">
			
			{* Images *}
			<div class="photoGallerySlideshowAlbum" style="width:{$large_resolution.width}px;height:{$large_resolution.height}px">
				{iteration:widgetPhotogallerySlideshow.images}
					<div style="display:none;">
						{* With internal link *}
						{option:widgetPhotogallerySlideshow.images.data.internal_link}
							<a href="{$var|geturl:{$widgetPhotogallerySlideshow.images.data.internal_link.page_id}}" class="linkedImage">
								<img src="{$widgetPhotogallerySlideshow.images.large_url}" />
							</a>
						{/option:widgetPhotogallerySlideshow.images.data.internal_link}
		
						{* With external link *}
						{option:widgetPhotogallerySlideshow.images.data.external_link}
							<a href="{$widgetPhotogallerySlideshow.images.data.external_link.url}" class="linkedImage targetBlank">
								<img src="{$widgetPhotogallerySlideshow.images.large_url}" />
							</a>
						{/option:widgetPhotogallerySlideshow.images.data.external_link}
	
						{* No link *}
						{option:!widgetPhotogallerySlideshow.images.data.internal_link}
							{option:!widgetPhotogallerySlideshow.images.data.external_link}
								<img src="{$widgetPhotogallerySlideshow.images.large_url}" />
							{/option:!widgetPhotogallerySlideshow.images.data.external_link}
						{/option:!widgetPhotogallerySlideshow.images.data.internal_link}
					</div>
				{/iteration:widgetPhotogallerySlideshow.images}
			</div>
			
			{* Pager *}
				<div class="photoGallerySlideshowPager">
					<ul>
						{iteration:widgetPhotogallerySlideshow.images}
							<li><a href="#">{$widgetPhotogallerySlideshow.images.index}</a></li>
						{/iteration:widgetPhotogallerySlideshow.images}
					</ul>
				</div>

			{* Captions *}
				<div class="photoGallerySlideshowCaptions">
					{iteration:widgetPhotogallerySlideshow.images}
						{* Do not remove this div, you can change the class *}
						<div class="caption">
							{option:widgetPhotogallerySlideshow.images.title}
								<h3>{$widgetPhotogallerySlideshow.images.title}</h3>
							{/option:widgetPhotogallerySlideshow.images.title}
							{$widgetPhotogallerySlideshow.images.text}
						</div>
					{/iteration:widgetPhotogallerySlideshow.images}
				</div>
		</div>
	{/option:widgetPhotogallerySlideshow.images}