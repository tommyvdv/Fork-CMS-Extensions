{* Title *}
	{option:blockPhotogalleryAlbumImage.title}
		<h3>{$blockPhotogalleryAlbumImage.title}</h3>
	{/option:blockPhotogalleryAlbumImage.title}

{* Meta *}
	<ul>
		<li>
			{* Written on *}
			{$blockPhotogalleryAlbumImage.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}

			{* Category*}
			{option:blockPhotogalleryAlbumImage.categories}
				{$lblIn} {$lblThe} {$lblCategory} 
				{iteration:blockPhotogalleryAlbumImage.categories}
					<a href="{$blockPhotogalleryAlbumImage.categories.full_url}" rel="tag" title="{$blockPhotogalleryAlbumImage.categories.title}">{$blockPhotogalleryAlbumImage.categories.title}</a>{option:!blockPhotogalleryAlbumImage.categories.last}, {/option:!blockPhotogalleryAlbumImage.categories.last}{option:blockPhotogalleryAlbumImage.categories.last}.{/option:blockPhotogalleryAlbumImage.categories.last}
				{/iteration:blockPhotogalleryAlbumImage.categories}
			{/option:blockPhotogalleryAlbumImage.categories}

			{* Tags*}
			{option:blockPhotogalleryAlbumImage.tags}
				{$lblWith} {$lblThe} {$lblTags}
				{iteration:blockPhotogalleryAlbumImage.tags}
					<a href="{$blockPhotogalleryAlbumImage.tags.full_url}" rel="tag" title="{$blockPhotogalleryAlbumImage.tags.name}">{$blockPhotogalleryAlbumImage.tags.name}</a>
					{option:!blockPhotogalleryAlbumImage.tags.last}, {/option:!blockPhotogalleryAlbumImage.tags.last}
					{option:blockPhotogalleryAlbumImage.tags.last}.{/option:blockPhotogalleryAlbumImage.tags.last}
				{/iteration:blockPhotogalleryAlbumImage.tags}
			{/option:blockPhotogalleryAlbumImage.tags}
		</li>
	</ul>
	

{* Text *}	
	{$blockPhotogalleryAlbumImage.text}


{* Image *}
	{* With internal link *}
	{option:blockPhotogalleryAlbumImage.data.internal_link}
		<a href="{$var|geturl:{$blockPhotogalleryAlbumImage.data.internal_link.page_id}}" class="linkedImage">
			{*<img src="{$var|createimagephotogallery:{$blockPhotogalleryAlbumImage.set_id}:{$blockPhotogalleryAlbumImage.filename}:{$modulePhotogalleryImageLargeResolution.width}:{$modulePhotogalleryImageLargeResolution.height}:{$modulePhotogalleryImageLargeResolution.method}}" />*}
			<img src="{$var|createimageresolution:{$blockPhotogalleryAlbumImage.set_id}:{$blockPhotogalleryAlbumImage.filename}:'large'}" alt="{$blockPhotogalleryAlbumImage.title}" />
		</a>
	{/option:blockPhotogalleryAlbumImage.data.internal_link}

	{* With external link *}
	{option:blockPhotogalleryAlbumImage.data.external_link}
		<a href="{$blockPhotogalleryAlbumImage.data.external_link.url}" class="linkedImage targetBlank">
			{*<img src="{$var|createimagephotogallery:{$blockPhotogalleryAlbumImage.set_id}:{$blockPhotogalleryAlbumImage.filename}:{$modulePhotogalleryImageLargeResolution.width}:{$modulePhotogalleryImageLargeResolution.height}:{$modulePhotogalleryImageLargeResolution.method}}" />*}
			<img src="{$var|createimageresolution:{$blockPhotogalleryAlbumImage.set_id}:{$blockPhotogalleryAlbumImage.filename}:'large'}" alt="{$blockPhotogalleryAlbumImage.title}" />
		</a>
	{/option:blockPhotogalleryAlbumImage.data.external_link}

	{* No link *}
	{option:!blockPhotogalleryAlbumImage.data.internal_link}
		{option:!blockPhotogalleryAlbumImage.data.external_link}
			{*<img src="{$var|creatphotogallery:{$blockPhotogalleryAlbumImage.set_id}:{$blockPhotogalleryAlbumImage.filename}:{$modulePhotogalleryImageLargeResolution.width}:{$modulePhotogalleryImageLargeResolution.height}:{$modulePhotogalleryImageLargeResolution.method}}" />*}
			<img src="{$var|createimageresolution:{$blockPhotogalleryAlbumImage.set_id}:{$blockPhotogalleryAlbumImage.filename}:'large'}" alt="{$blockPhotogalleryAlbumImage.title}" />
		{/option:!blockPhotogalleryAlbumImage.data.external_link}
	{/option:!blockPhotogalleryAlbumImage.data.internal_link}


{* Navigation *}
	<ul>
		{option:blockPhotogalleryAlbumImageNavigation.previous}
			<li class="previousLink">
				<a href="{$blockPhotogalleryAlbumImageNavigation.previous.url}" rel="prev">{$lblPreviousImage|ucfirst}: <em>{$blockPhotogalleryAlbumImageNavigation.previous.title}</em></a>
			</li>
		{/option:blockPhotogalleryAlbumImageNavigation.previous}
		{option:blockPhotogalleryAlbumImageNavigation.next}
			<li class="nextLink">
				<a href="{$blockPhotogalleryAlbumImageNavigation.next.url}" rel="next">{$lblNextImage|ucfirst}: <em>{$blockPhotogalleryAlbumImageNavigation.next.title}</em></a>
			</li>
		{/option:blockPhotogalleryAlbumImageNavigation.next}
	</ul>