{* Images *}
	{option:widgetPhotogallerySlideshow.images}
		<div class="photogallerySlideshowWrapper">	
			<div class="photogallerySlideshow" id="photogallerySlideshowAlbum{$widgetPhotogallerySlideshow.id}ExtraId{$data.extra_id}" style="width:{$large_resolution.width}px;height:{$large_resolution.height}px">
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
			<div class="photogallerySlideshowCaptions">
				{iteration:widgetPhotogallerySlideshow.images}			
					<div class="caption" id="photogallerySlideshowAlbumImageCaption{$widgetPhotogallerySlideshow.images.id}ExtraId{$data.extra_id}">
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
		<div class="photogallerySlideshowAlbumCaptionDisplay" id="photogallerySlideshowAlbumCaptionDisplay{$widgetPhotogallerySlideshow.id}ExtraId{$data.extra_id}">
			{* Caption will come here *}
		</div>

{* Javascript *}
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
		    $('#photogallerySlideshowAlbum{$widgetPhotogallerySlideshow.id}ExtraId{$data.extra_id}').cycle({
				fx: 'fade',
				before: function()
				{
					var id = $(this).data('id');
					var extra_id = $(this).data('extra_id')
					var caption = $('#photogallerySlideshowAlbumImageCaption' + id + 'ExtraId' + extra_id).html();
					$('#photogallerySlideshowAlbumCaptionDisplay{$widgetPhotogallerySlideshow.id}ExtraId{$data.extra_id}').html(caption);
				}
			});
		});
	</script>
