{option:widgetPhotogalleryCategories}
	{iteration:widgetPhotogalleryCategories}

		{* Title *}
			<h3>
				<a href="{$widgetPhotogalleryCategories.full_url}" title="{$widgetPhotogalleryCategories.label}">{$widgetPhotogalleryCategories.label}</a>
			</h3>

		{* Image *}
			<a href="{$widgetPhotogalleryCategories.full_url}"  class="linkedImage" title="{$widgetPhotogalleryCategories.label}">
				<img src="{$widgetPhotogalleryCategories.filename_url}" alt="{$widgetPhotogalleryCategories.image.title}" title="{$widgetPhotogalleryCategories.label}" />
			</a>

	{/iteration:widgetPhotogalleryCategories}
{/option:widgetPhotogalleryCategories}


