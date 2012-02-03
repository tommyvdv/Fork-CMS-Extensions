{* Images *}
	{option:widgetPhotogallerySlideshow.images}
		<div class="photoGallerySlideshowWrapper">	
			<div class="photoGallerySlideshow" id="photoGallerySlideshowAlbum{$widgetPhotogallerySlideshow.id}ExtraId{$data.extra_id}" style="width:{$large_resolution.width}px;height:{$large_resolution.height}px">
				{iteration:widgetPhotogallerySlideshow.images}
		
					{* With internal link *}
					{option:widgetPhotogallerySlideshow.images.data.internal_link}
						<a href="{$var|geturl:{$widgetPhotogallerySlideshow.images.data.internal_link.page_id}}" class="linkedImage" data-extra_id="{$data.extra_id}" data-id="{$widgetPhotogallerySlideshow.images.id}">
							<img src="{$widgetPhotogallerySlideshow.images.large_url}" />
						</a>
					{/option:widgetPhotogallerySlideshow.images.data.internal_link}
		
					{* With external link *}
					{option:widgetPhotogallerySlideshow.images.data.external_link}
						<a href="{$widgetPhotogallerySlideshow.images.data.external_link.url}" class="linkedImage targetBlank" data-extra_id="{$data.extra_id}" data-id="{$widgetPhotogallerySlideshow.images.id}">
							<img src="{$widgetPhotogallerySlideshow.images.large_url}" />
						</a>
					{/option:widgetPhotogallerySlideshow.images.data.external_link}
	
					{* No link *}
					{option:!widgetPhotogallerySlideshow.images.data.internal_link}
						{option:!widgetPhotogallerySlideshow.images.data.external_link}
							<img src="{$widgetPhotogallerySlideshow.images.large_url}" data-extra_id="{$data.extra_id}" data-id="{$widgetPhotogallerySlideshow.images.id}" />
						{/option:!widgetPhotogallerySlideshow.images.data.external_link}
					{/option:!widgetPhotogallerySlideshow.images.data.internal_link}
	
				{/iteration:widgetPhotogallerySlideshow.images}
			</div>
		
			{* Captions *}
			<div class="photoGallerySlideshowCaptions">
				{iteration:widgetPhotogallerySlideshow.images}			
					<div class="caption" id="photoGallerySlideshowAlbumImageCaption{$widgetPhotogallerySlideshow.images.id}ExtraId{$data.extra_id}">
						{option:widgetPhotogallerySlideshow.images.title}
							<h3>{$widgetPhotogallerySlideshow.images.title}</h3>
						{/option:widgetPhotogallerySlideshow.images.title}
						{$widgetPhotogallerySlideshow.images.text}
					</div>
				{/iteration:widgetPhotogallerySlideshow.images}
			</div>	
		</div>
	{/option:widgetPhotogallerySlideshow.images}
	
	{* Caption display *}
		<div class="photoGallerySlideshowAlbumCaptionDisplay" id="photoGallerySlideshowAlbumCaptionDisplay{$widgetPhotogallerySlideshow.id}ExtraId{$data.extra_id}">
			{* Caption will come here *}
		</div>

{* Javascript *}
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
		    $('#photoGallerySlideshowAlbum{$widgetPhotogallerySlideshow.id}ExtraId{$data.extra_id}').cycle({
				fx: 'fade',
				before: function()
				{
					var id = $(this).data('id');
					var extra_id = $(this).data('extra_id')
					var caption = $('#photoGallerySlideshowAlbumImageCaption' + id + 'ExtraId' + extra_id).html();
					$('#photoGallerySlideshowAlbumCaptionDisplay{$widgetPhotogallerySlideshow.id}ExtraId{$data.extra_id}').html(caption);
				}
			});
		});
	</script>
