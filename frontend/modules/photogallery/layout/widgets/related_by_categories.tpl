{option:widgetPhotogalleryRelatedByCategories}
	<div class="photogalleryRelatedByCategoriesWrapper photogalleryRelatedByCategoriesId{$widgetPhotogalleryRelatedByCategories.id}">
		<ul class="photogalleryRelatedByCategories">
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
