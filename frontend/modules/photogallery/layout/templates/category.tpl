{option:blockPhotogalleryCategoryAlbums}
	{iteration:blockPhotogalleryCategoryAlbums}
	
		{* Title *}
			<h3>
				<a href="{$blockPhotogalleryCategoryAlbums.full_url}" title="{$blockPhotogalleryCategoryAlbums.title}">{$blockPhotogalleryCategoryAlbums.title}</a>
			</h3>
		
		{* Meta *}
			<ul>
				<li>
					{* Written on *}
					{$blockPhotogalleryCategoryAlbums.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}

					{* Category*}
					{option:blockPhotogalleryCategoryAlbums.categories}
						{$lblIn} {$lblThe} {$lblCategory} 
						{iteration:blockPhotogalleryCategoryAlbums.categories}
							<a href="{$blockPhotogalleryCategoryAlbums.categories.full_url}" rel="tag" title="{$blockPhotogalleryCategoryAlbums.categories.title}">{$blockPhotogalleryCategoryAlbums.categories.title}</a>{option:!blockPhotogalleryCategoryAlbums.categories.last}, {/option:!blockPhotogalleryCategoryAlbums.categories.last}{option:blockPhotogalleryCategoryAlbums.categories.last}.{/option:blockPhotogalleryCategoryAlbums.categories.last}
						{/iteration:blockPhotogalleryCategoryAlbums.categories}
					{/option:blockPhotogalleryCategoryAlbums.categories}

					{* Tags*}
					{option:blockPhotogalleryCategoryAlbums.tags}
						{$lblWith} {$lblThe} {$lblTags}
						{iteration:blockPhotogalleryCategoryAlbums.tags}
							<a href="{$blockPhotogalleryCategoryAlbums.tags.full_url}" rel="tag" title="{$blockPhotogalleryCategoryAlbums.tags.name}">{$blockPhotogalleryCategoryAlbums.tags.name}</a>
							{option:!blockPhotogalleryCategoryAlbums.tags.last}, {/option:!blockPhotogalleryCategoryAlbums.tags.last}
							{option:blockPhotogalleryCategoryAlbums.tags.last}.{/option:blockPhotogalleryCategoryAlbums.tags.last}
						{/iteration:blockPhotogalleryCategoryAlbums.tags}
					{/option:blockPhotogalleryCategoryAlbums.tags}
				</li>
			</ul>
		
		{* Image *}
			<a href="{$blockPhotogalleryCategoryAlbums.full_url}" class="linkedImage" title="{$blockPhotogalleryCategoryAlbums.image.title}">
				<img src="{$blockPhotogalleryCategoryAlbums.image.thumbnail_url}" alt="{$blockPhotogalleryCategoryAlbums.image.title}" title="{$blockPhotogalleryCategoryAlbums.image.title}" />
			</a>
	
	{/iteration:blockPhotogalleryCategoryAlbums}
{/option:blockPhotogalleryCategoryAlbums}