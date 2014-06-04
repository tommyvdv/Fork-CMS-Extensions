{* Title *}
	<h3>{$blockPhotogalleryAlbum.title}</h3>

{* Meta *}
	<ul>
		<li>
			{* Written on *}
			{$lblPublishedOn|ucfirst} {$blockPhotogalleryAlbum.publish_on|date:{$dateFormatLong}:{$LANGUAGE}} {$lblAt} {$blockPhotogalleryAlbum.publish_on|date:{$timeFormat}:{$LANGUAGE}}
			
			{* Category*}
			{option:blockPhotogalleryAlbum.categories}
				{$lblIn} {$lblThe} {$lblCategory} 
				{iteration:blockPhotogalleryAlbum.categories}
					<a href="{$blockPhotogalleryAlbum.categories.full_url}" rel="tag" title="{$blockPhotogalleryAlbum.categories.title}">{$blockPhotogalleryAlbum.categories.title}</a>{option:!blockPhotogalleryAlbum.categories.last}, {/option:!blockPhotogalleryAlbum.categories.last}{option:blockPhotogalleryAlbum.categories.last}.{/option:blockPhotogalleryAlbum.categories.last}
				{/iteration:blockPhotogalleryAlbum.categories}
			{/option:blockPhotogalleryAlbum.categories}
			
			{* Tags*}
			{option:blockPhotogalleryAlbum.tags}
				{$lblWith} {$lblThe} {$lblTags}
				{iteration:blockPhotogalleryAlbum.tags}
					<a href="{$blockPhotogalleryAlbum.tags.full_url}" rel="tag" title="{$blockPhotogalleryAlbum.tags.name}">{$blockPhotogalleryAlbum.tags.name}</a>
					{option:!blockPhotogalleryAlbum.tags.last}, {/option:!blockPhotogalleryAlbum.tags.last}
					{option:blockPhotogalleryAlbum.tags.last}.{/option:blockPhotogalleryAlbum.tags.last}
				{/iteration:blockPhotogalleryAlbum.tags}
			{/option:blockPhotogalleryAlbum.tags}
		</li>
	</ul>


{* Content *}
	{option:blockPhotogalleryAlbum.text}
		{$blockPhotogalleryAlbum.text}
	{/option:blockPhotogalleryAlbum.text}

	{option:!blockPhotogalleryAlbum.text}
		{$blockPhotogalleryAlbum.introduction}
	{/option:!blockPhotogalleryAlbum.text}


{* Lightbox *}
	{option:lightbox}
		{option:blockPhotogalleryAlbum.images}
			<ul class="js-photogallery-lightbox photogallery-list" data-id="{$blockPhotogalleryAlbum.data.extra_id}">
				{iteration:blockPhotogalleryAlbum.images}
				<li>
					{option:!blockPhotogalleryAlbum.images.data.external_link}
						<a data-image-id="{$blockPhotogalleryAlbum.images.id}" href="{$var|createimagephotogallery:{$blockPhotogalleryAlbum.images.set_id}:{$blockPhotogalleryAlbum.images.filename}:{$modulePhotogalleryDetailLargeResolution.width}:{$modulePhotogalleryDetailLargeResolution.height}:{$modulePhotogalleryDetailLargeResolution.method}}" rel="{$blockPhotogalleryAlbum.id}" class="linkedImage linkOverlay js-photogallery-lightbox-{$blockPhotogalleryAlbum.data.extra_id}" title="{$blockPhotogalleryAlbum.title}">
					{/option:!blockPhotogalleryAlbum.images.data.external_link}
					{option:blockPhotogalleryAlbum.images.data.external_link}
						<a data-image-id="{$blockPhotogalleryAlbum.images.id}" href="{$blockPhotogalleryAlbum.images.data.external_link.url}" rel="{$blockPhotogalleryAlbum.id}" class="linkedImage linkOverlay" title="{$blockPhotogalleryAlbum.title}">
					{/option:blockPhotogalleryAlbum.images.data.external_link}
						{*<img src="{$var|createimagephotogallery:{$blockPhotogalleryAlbum.images.set_id}:{$blockPhotogalleryAlbum.images.filename}:{$modulePhotogalleryDetailThumbnailResolution.width}:{$modulePhotogalleryDetailThumbnailResolution.height}:{$modulePhotogalleryDetailThumbnailResolution.method}}" />*}
						<img src="{$var|createimageresolution:{$blockPhotogalleryAlbum.images.set_id}:{$blockPhotogalleryAlbum.images.filename}:'detail_thumbnail'}" alt="{$blockPhotogalleryAlbum.title}" />
					</a>
					<div class="photogallery-lightbox-caption">
						{option:!blockPhotogalleryAlbum.images.title_hidden}
							{option:blockPhotogalleryAlbum.images.title}
								<h3>{$blockPhotogalleryAlbum.images.title}</h3>
							{/option:blockPhotogalleryAlbum.images.title}
						{$blockPhotogalleryAlbum.images.text}
						{/option:!blockPhotogalleryAlbum.images.title_hidden}
					</div>
				</li>
				{/iteration:blockPhotogalleryAlbum.images}
			</ul>
		{/option:blockPhotogalleryAlbum.images}
	{/option:lightbox}


{* Paged *}
	{option:paged}
		{option:blockPhotogalleryAlbum.images}
			<ul class="photogallery-list">
				{iteration:blockPhotogalleryAlbum.images}
				<li>
					<a href="{$blockPhotogalleryAlbum.images.full_url}" rel="{$blockPhotogalleryAlbum.id}" class="linkedImage" title="{$blockPhotogalleryAlbum.image.title}">
						{*<img src="{$var|createimagephotogallery:{$blockPhotogalleryAlbum.images.set_id}:{$blockPhotogalleryAlbum.images.filename}:{$modulePhotogalleryDetailThumbnailResolution.width}:{$modulePhotogalleryDetailThumbnailResolution.height}:{$modulePhotogalleryDetailThumbnailResolution.method}}" />*}
						<img src="{$var|createimageresolution:{$blockPhotogalleryAlbum.images.set_id}:{$blockPhotogalleryAlbum.images.filename}:'detail_thumbnail'}" alt="{$blockPhotogalleryAlbum.title}" />
					</a>
				</li>
				{/iteration:blockPhotogalleryAlbum.images}
			</ul>
		{/option:blockPhotogalleryAlbum.images}
	{/option:paged}


{* Navigation *}
	<ul class="photogalleryDetailAlbumsNavigation">
		{option:blockPhotogalleryAlbumNavigation.previous}
			<li class="previousLink">
				<a href="{$blockPhotogalleryAlbumNavigation.previous.url}" rel="prev">{$lblPreviousAlbum|ucfirst}: <em>{$blockPhotogalleryAlbumNavigation.previous.title}</em></a>
			</li>
		{/option:blockPhotogalleryAlbumNavigation.previous}
		{option:blockPhotogalleryAlbumNavigation.next}
			<li class="nextLink">
				<a href="{$blockPhotogalleryAlbumNavigation.next.url}" rel="next">{$lblNextAlbum|ucfirst}: <em>{$blockPhotogalleryAlbumNavigation.next.title}</em></a>
			</li>
		{/option:blockPhotogalleryAlbumNavigation.next}
	</ul>