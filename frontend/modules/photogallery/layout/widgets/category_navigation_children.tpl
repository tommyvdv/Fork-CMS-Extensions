{* navigation *}
{option:navigation}
	<ul>
		{iteration:navigation}
			<li class="{option:navigation.selected}selected{/option:navigation.selected}">
				<a href="{$navigation.link}" title="{$navigation.navigation_title}"{option:navigation.nofollow} rel="nofollow"{/option:navigation.nofollow}>{$navigation.navigation_title}</a>
				{option:navigation.selected}{$navigation.children}{/option:navigation.selected}
			</li>
		{/iteration:navigation}
	</ul>
{/option:navigation}