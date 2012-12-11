{option:widgetPhotogalleryCategoryNavigation}
	<ul class="photogalleryCategoryNavigation">
		{iteration:widgetPhotogalleryCategoryNavigation}
		<li{option:widgetPhotogalleryCategoryNavigation.selected} class="selected"{/option:widgetPhotogalleryCategoryNavigation.selected}>
			<a href="{$widgetPhotogalleryCategoryNavigation.full_url}">{$widgetPhotogalleryCategoryNavigation.label}</a>
			{$var|parsewidget:'photogallery':'category_navigation':{$widgetPhotogalleryCategoryNavigation.id}}
		</li>
		{/iteration:widgetPhotogalleryCategoryNavigation}
	</ul>
{/option:widgetPhotogalleryCategoryNavigation}