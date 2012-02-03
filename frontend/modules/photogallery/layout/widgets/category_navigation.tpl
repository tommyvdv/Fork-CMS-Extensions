{option:widgetPhotogalleryCategoryNavigation}
	<ul class="photogalleryCategoryNavigation">
		{iteration:widgetPhotogalleryCategoryNavigation}
		<li{option:widgetPhotogalleryCategoryNavigation.selected} class="selected"{/option:widgetPhotogalleryCategoryNavigation.selected}>
			<a href="{$widgetPhotogalleryCategoryNavigation.full_url}">{$widgetPhotogalleryCategoryNavigation.label}</a>
			{option:widgetPhotogalleryCategoryNavigation.selected}
				{option:widgetPhotogalleryCategoryNavigation.items}
					<ul>
						{iteration:widgetPhotogalleryCategoryNavigation.items}
							<li{option:widgetPhotogalleryCategoryNavigation.items.selected} class="selected"{/option:widgetPhotogalleryCategoryNavigation.items.selected}>
								<a href="{$widgetPhotogalleryCategoryNavigation.items.full_url}">{$widgetPhotogalleryCategoryNavigation.items.title}</a>
							</li>
						{/iteration:widgetPhotogalleryCategoryNavigation.items}
					</ul>
				{/option:widgetPhotogalleryCategoryNavigation.items}
			{/option:widgetPhotogalleryCategoryNavigation.selected}
		</li>
		{/iteration:widgetPhotogalleryCategoryNavigation}
	</ul>
{/option:widgetPhotogalleryCategoryNavigation}