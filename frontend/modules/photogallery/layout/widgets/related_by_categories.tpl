{option:widgetPhotogalleryRelatedByCategories}
	<div class="photoGalleryRelatedByCategoriesWrapper photoGalleryRelatedByCategoriesId{$widgetPhotogalleryRelatedByCategories.id}">
		<ul class="photoGalleryRelatedByCategories">
			{iteration:widgetPhotogalleryRelatedByCategories}
			<li>
				<a href="{$widgetPhotogalleryRelatedByCategories.full_url}" rel="{$widgetPhotogalleryRelatedByCategories.id}" class="linkedImage" title="{$widgetPhotogalleryRelatedByCategories.image.title}">
					<img src="{$widgetPhotogalleryRelatedByCategories.image.thumbnail_url}" alt="{$widgetPhotogalleryRelatedByCategories.image.title}" title="{$widgetPhotogalleryRelatedByCategories.image.title}" />
				</a>
			</li>
			{/iteration:widgetPhotogalleryRelatedByCategories}
		</ul>
	</div>
{/option:widgetPhotogalleryRelatedByCategories}
