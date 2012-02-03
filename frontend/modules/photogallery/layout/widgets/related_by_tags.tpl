{option:widgetPhotogalleryRelatedByTags}
	<div class="photoGalleryRelatedByTagsrapper photoGalleryRelatedByTagsId{$widgetPhotogalleryRelatedByTags.id}">
		<ul class="photoGalleryRelatedByTagss">
			{iteration:widgetPhotogalleryRelatedByTags}
			<li>
				<a href="{$widgetPhotogalleryRelatedByTags.full_url}" rel="{$widgetPhotogalleryRelatedByTags.id}" class="linkedImage" title="{$widgetPhotogalleryRelatedByTags.image.title}">
					<img src="{$widgetPhotogalleryRelatedByTags.image.thumbnail_url}" alt="{$widgetPhotogalleryRelatedByTags.image.title}" title="{$widgetPhotogalleryRelatedByTags.image.title}" />
				</a>
			</li>
			{/iteration:widgetPhotogalleryRelatedByTags}
		</ul>
	</div>
{/option:widgetPhotogalleryRelatedByTags}
