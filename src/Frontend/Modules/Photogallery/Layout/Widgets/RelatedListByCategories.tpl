{option:widgetPhotogalleryRelatedListByCategories}
	<ul class="photogalleryRelatedListByCategories">
		{iteration:widgetPhotogalleryRelatedListByCategories}
		<li>
			<a href="{$widgetPhotogalleryRelatedListByCategories.full_url}" title="{$widgetPhotogalleryRelatedListByCategories.title|htmlentities}">{$widgetPhotogalleryRelatedListByCategories.title}</a>
		</li>
		{/iteration:widgetPhotogalleryRelatedListByCategories}
	</ul>
{/option:widgetPhotogalleryRelatedListByCategories}

