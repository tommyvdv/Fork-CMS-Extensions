<nav class="sideNavigation">
	<h4>
		<a href="{$var|geturlforblock:'photogallery':'category'}">{$lblPhotogalleryCategoryNavigation}</a>
	</h4>

	{$widgetPhotogalleryCategoryNavigation}

</nav>

{*
	widget loads category_navigation.tpl
	model uses category_navigation_children.tpl

	makes it a little easier to read

	can also be called:

	{* Subcategories *}
			<h4>{$lblSubcategories}</h4>
			{$var|parsewidget:'photogallery':'category_navigation':{$blockPhotogalleryCategory.id}}
*}