{* Albums *}
	{option:displayAlbums}

		{option:modulePhotogalleryAlbums}
			{iteration:modulePhotogalleryAlbums}

				{* Title *}
					<h3>
						<a href="{$modulePhotogalleryAlbums.full_url}" title="{$modulePhotogalleryAlbums.title}">{$modulePhotogalleryAlbums.title}</a>
					</h3>

				{* Meta *}
					<ul>
						<li>
							{* Written on *}
							{$modulePhotogalleryAlbums.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}

							{* Category*}
							{option:modulePhotogalleryAlbums.categories}
								{$lblIn} {$lblThe} {$lblCategory} 
								{iteration:modulePhotogalleryAlbums.categories}
									<a href="{$modulePhotogalleryAlbums.categories.full_url}" rel="tag" title="{$modulePhotogalleryAlbums.categories.title}">{$modulePhotogalleryAlbums.categories.title}</a>{option:!modulePhotogalleryAlbums.categories.last}, {/option:!modulePhotogalleryAlbums.categories.last}{option:modulePhotogalleryAlbums.categories.last}.{/option:modulePhotogalleryAlbums.categories.last}
								{/iteration:modulePhotogalleryAlbums.categories}
							{/option:modulePhotogalleryAlbums.categories}

							{* Tags*}
							{option:modulePhotogalleryAlbums.tags}
								{$lblWith} {$lblThe} {$lblTags}
								{iteration:modulePhotogalleryAlbums.tags}
									<a href="{$modulePhotogalleryAlbums.tags.full_url}" rel="tag" title="{$modulePhotogalleryAlbums.tags.name}">{$modulePhotogalleryAlbums.tags.name}</a>{option:!modulePhotogalleryAlbums.tags.last}, {/option:!modulePhotogalleryAlbums.tags.last}{option:modulePhotogalleryAlbums.tags.last}.{/option:modulePhotogalleryAlbums.tags.last}
								{/iteration:modulePhotogalleryAlbums.tags}
							{/option:modulePhotogalleryAlbums.tags}
						</li>
					</ul>

				{* Content *}
					{option:!modulePhotogalleryAlbums.introduction}{$modulePhotogalleryAlbums.text}{/option:!modulePhotogalleryAlbums.introduction}
					{option:modulePhotogalleryAlbums.introduction}{$modulePhotogalleryAlbums.introduction}{/option:modulePhotogalleryAlbums.introduction}

				{* Image *}
					<a href="{$modulePhotogalleryAlbums.full_url}"  class="linkedImage" title="{$modulePhotogalleryAlbums.image.title}">
						<img src="{$modulePhotogalleryAlbums.image.thumbnail_url}" alt="{$modulePhotogalleryAlbums.image.title}" title="{$modulePhotogalleryAlbums.image.title}" />
					</a>

			{/iteration:modulePhotogalleryAlbums}
			
			{* RSS link *}
				<p><a id="RSSfeed" href="{$var|geturlforblock:'photogallery':'rss'}">{$lblSubscribeToTheRSSFeed|ucfirst}</a></p>
				
		{/option:modulePhotogalleryAlbums}
	{/option:displayAlbums}

{* Categories *}
	{option:displayCategories}
		{option:modulePhotogalleryCategories}
			{iteration:modulePhotogalleryCategories}

				{* Title *}
					<h3>
						<a href="{$modulePhotogalleryCategories.full_url}" title="{$modulePhotogalleryCategories.label}">{$modulePhotogalleryCategories.label}</a>
					</h3>

				{* Image *}
					<a href="{$modulePhotogalleryCategories.full_url}"  class="linkedImage" title="{$modulePhotogalleryCategories.label}">
						<img src="{$modulePhotogalleryCategories.filename_url}" alt="{$modulePhotogalleryCategories.image.title}" title="{$modulePhotogalleryCategories.label}" />
					</a>

			{/iteration:modulePhotogalleryCategories}
			
			{* RSS link *}
				<p><a id="RSSfeed" href="{$var|geturlforblock:'photogallery':'rss'}">{$lblSubscribeToTheRSSFeed|ucfirst}</a></p>
				
		{/option:modulePhotogalleryCategories}
	{/option:displayCategories}