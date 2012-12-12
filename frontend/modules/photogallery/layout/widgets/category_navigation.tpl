{option:widgetPhotogalleryCategoryNavigation}
	<ul class="{option:!widgetPhotogalleryCategoryNavigationParentId}photogalleryCategoryNavigation{/option:!widgetPhotogalleryCategoryNavigationParentId} categories">
		{iteration:widgetPhotogalleryCategoryNavigation}
		<li class="category {option:widgetPhotogalleryCategoryNavigation.selected}selected{/option:widgetPhotogalleryCategoryNavigation.selected}">
			<a href="{$widgetPhotogalleryCategoryNavigation.full_url}">{$widgetPhotogalleryCategoryNavigation.label}</a>
			{option:widgetPhotogalleryCategoryNavigation.items}
				<ul class="products">
					{iteration:widgetPhotogalleryCategoryNavigation.items}
						<li class="product {option:widgetPhotogalleryCategoryNavigation.items.selected}selected{/option:widgetPhotogalleryCategoryNavigation.items.selected}">
							<a href="{$widgetPhotogalleryCategoryNavigation.items.full_url}">{$widgetPhotogalleryCategoryNavigation.items.title}</a>
						</li>
					{/iteration:widgetPhotogalleryCategoryNavigation.items}
				</ul>
			{/option:widgetPhotogalleryCategoryNavigation.items}
			{$var|parsewidget:'photogallery':'category_navigation':{$widgetPhotogalleryCategoryNavigation.id}}
		</li>
		{/iteration:widgetPhotogalleryCategoryNavigation}
	</ul>
{/option:widgetPhotogalleryCategoryNavigation}