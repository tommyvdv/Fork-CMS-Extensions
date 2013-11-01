{option:!blockPhotogalleryCategoryView}
	{option:blockPhotogalleryCategories}
		<h1>{$lblAllCategories|ucfirst}</h1>

		{iteration:blockPhotogalleryCategories}

			{* Title *}
				<h2>
					<a href="{$blockPhotogalleryCategories.full_url}">
						{$blockPhotogalleryCategories.label}

					</a>
				</h2>

			{* Albums *}
				{option:blockPhotogalleryCategories.albums}
					{iteration:blockPhotogalleryCategories.albums}
					
						{* Title *}
							<h3>
								<a href="{$blockPhotogalleryCategories.albums.full_url}" title="{$blockPhotogalleryCategories.albums.title}">{$blockPhotogalleryCategories.albums.title}</a>
							</h3>
						
						{* Meta *}
							<ul>
								<li>
									{* Written on *}
									{$lblPublishedOn} {$blockPhotogalleryCategories.albums.publish_on|date:{$dateFormatLong}:{$LANGUAGE}} {$lblAt} {$blockPhotogalleryCategories.albums.publish_on|date:{$timeFormat}:{$LANGUAGE}}

									{* Category*}
									{option:blockPhotogalleryCategories.albums.categories}
										{$lblInTheCategory}
										{iteration:blockPhotogalleryCategories.albums.categories}
											<a href="{$blockPhotogalleryCategories.albums.categories.full_url}" rel="tag" title="{$blockPhotogalleryCategories.albums.categories.title}">{$blockPhotogalleryCategories.albums.categories.title}</a>{option:!blockPhotogalleryCategories.albums.categories.last}, {/option:!blockPhotogalleryCategories.albums.categories.last}{option:blockPhotogalleryCategories.albums.categories.last}.{/option:blockPhotogalleryCategories.albums.categories.last}
										{/iteration:blockPhotogalleryCategories.albums.categories}
									{/option:blockPhotogalleryCategories.albums.categories}

									{* Tags*}
									{option:blockPhotogalleryCategories.albums.tags}
										{$lblWithTheTags}
										{iteration:blockPhotogalleryCategories.albums.tags}
											<a href="{$blockPhotogalleryCategories.albums.tags.full_url}" rel="tag" title="{$blockPhotogalleryCategories.albums.tags.name}">{$blockPhotogalleryCategories.albums.tags.name}</a>{option:!blockPhotogalleryCategories.albums.tags.last}, {/option:!blockPhotogalleryCategories.albums.tags.last}{option:blockPhotogalleryCategories.albums.tags.last}.{/option:blockPhotogalleryCategories.albums.tags.last}
										{/iteration:blockPhotogalleryCategories.albums.tags}
									{/option:blockPhotogalleryCategories.albums.tags}
								</li>
							</ul>
						
						{* Image *}
							<a href="{$blockPhotogalleryCategories.albums.full_url}" class="linkedImage" title="{$blockPhotogalleryCategories.albums.title}">
								<img src="{$var|createimagephotogallery:{$blockPhotogalleryCategories.albums.set_id}:{$blockPhotogalleryCategories.albums.image.filename}:{$modulePhotogalleryCategoryThumbnailResolution.width}:{$modulePhotogalleryCategoryThumbnailResolution.height}:{$modulePhotogalleryCategoryThumbnailResolution.method}}" />

							</a>
					
					{/iteration:blockPhotogalleryCategories.albums}
				{/option:blockPhotogalleryCategories.albums}


		{/iteration:blockPhotogalleryCategories}

	{/option:blockPhotogalleryCategories}
{/option:!blockPhotogalleryCategoryView}

{option:!blockPhotogalleryCategoriesView}
	{option:blockPhotogalleryCategoryView}
	
		<h1>{$lblOnCategory|sprintf:{$blockPhotogalleryCategory.label}|ucfirst}</h1>
		
		{option:!blockPhotogalleryCategoryAlbums}
			<p>{$lblNoItems}</p>
		{/option:!blockPhotogalleryCategoryAlbums}
		
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
							{$lblPublishedOn|ucfirst} {$blockPhotogalleryCategoryAlbums.publish_on|date:{$dateFormatLong}:{$LANGUAGE}} {$lblAt} {$blockPhotogalleryCategoryAlbums.publish_on|date:{$timeFormat}:{$LANGUAGE}}


							{* Category*}
							{option:blockPhotogalleryCategoryAlbums.categories}
								{$lblInTheCategory}
								{iteration:blockPhotogalleryCategoryAlbums.categories}
									<a href="{$blockPhotogalleryCategoryAlbums.categories.full_url}" rel="tag" title="{$blockPhotogalleryCategoryAlbums.categories.title}">{$blockPhotogalleryCategoryAlbums.categories.title}</a>{option:!blockPhotogalleryCategoryAlbums.categories.last}, {/option:!blockPhotogalleryCategoryAlbums.categories.last}{option:blockPhotogalleryCategoryAlbums.categories.last}.{/option:blockPhotogalleryCategoryAlbums.categories.last}
								{/iteration:blockPhotogalleryCategoryAlbums.categories}
							{/option:blockPhotogalleryCategoryAlbums.categories}

							{* Tags*}
							{option:blockPhotogalleryCategoryAlbums.tags}
								{$lblWithTheTags}
								{iteration:blockPhotogalleryCategoryAlbums.tags}
									<a href="{$blockPhotogalleryCategoryAlbums.tags.full_url}" rel="tag" title="{$blockPhotogalleryCategoryAlbums.tags.name}">{$blockPhotogalleryCategoryAlbums.tags.name}</a>{option:!blockPhotogalleryCategoryAlbums.tags.last}, {/option:!blockPhotogalleryCategoryAlbums.tags.last}{option:blockPhotogalleryCategoryAlbums.tags.last}.{/option:blockPhotogalleryCategoryAlbums.tags.last}
								{/iteration:blockPhotogalleryCategoryAlbums.tags}
							{/option:blockPhotogalleryCategoryAlbums.tags}
						</li>
					</ul>
				
				{* Image *}
					<a href="{$blockPhotogalleryCategoryAlbums.full_url}" class="linkedImage" title="{$blockPhotogalleryCategoryAlbums.image.title}">
	<img src="{$var|createimagephotogallery:{$blockPhotogalleryCategoryAlbums.set_id}:{$blockPhotogalleryCategoryAlbums.image.filename}:{$modulePhotogalleryCategoryThumbnailResolution.width}:{$modulePhotogalleryCategoryThumbnailResolution.height}:{$modulePhotogalleryCategoryThumbnailResolution.method}}" />					</a>
			
			{/iteration:blockPhotogalleryCategoryAlbums}
		{/option:blockPhotogalleryCategoryAlbums}

		{* Pagination *}
			{include:core/layout/templates/pagination.tpl}

	{/option:blockPhotogalleryCategoryView}
{/option:!blockPhotogalleryCategoriesView}