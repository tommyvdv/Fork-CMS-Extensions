{option:widgetPhotogalleryLightbox.images}
	<div class="photogalleryLightboxWrapper photogalleryLightboxId{$widgetPhotogalleryLightbox.id}">
	{* Title *}
		<h3>{$widgetPhotogalleryLightbox.title}</h3>
	
	{* Meta *}
		<ul>
			<li>
				{* Written on *}
				{$widgetPhotogalleryLightbox.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}

				{* Category*}
				{option:widgetPhotogalleryLightbox.categories}
					{$lblIn} {$lblThe} {$lblCategory} 
					{iteration:widgetPhotogalleryLightbox.categories}
						<a href="{$widgetPhotogalleryLightbox.categories.full_url}" rel="tag" title="{$widgetPhotogalleryLightbox.categories.title}">{$widgetPhotogalleryLightbox.categories.title}</a>{option:!widgetPhotogalleryLightbox.categories.last}, {/option:!widgetPhotogalleryLightbox.categories.last}{option:widgetPhotogalleryLightbox.categories.last}.{/option:widgetPhotogalleryLightbox.categories.last}
					{/iteration:widgetPhotogalleryLightbox.categories}
				{/option:widgetPhotogalleryLightbox.categories}
			
				{* Tags *}
				{option:widgetPhotogalleryLightbox.tags}
					{$lblWith} {$lblThe} {$lblTags}
					{iteration:widgetPhotogalleryLightbox.tags}
						<a href="{$widgetPhotogalleryLightbox.tags.full_url}" rel="tag" title="{$widgetPhotogalleryLightbox.tags.name}">{$widgetPhotogalleryLightbox.tags.name}</a>
						{option:!widgetPhotogalleryLightbox.tags.last}, {/option:!widgetPhotogalleryLightbox.tags.last}
						{option:widgetPhotogalleryLightbox.tags.last}.{/option:widgetPhotogalleryLightbox.tags.last}
					{/iteration:widgetPhotogalleryLightbox.tags}
				{/option:widgetPhotogalleryLightbox.tags}
			</li>
		</ul>
	
	{* Content *}
		{option:widgetPhotogalleryLightbox.introduction}
			{$widgetPhotogalleryLightbox.introduction}
		{/option:widgetPhotogalleryLightbox.introduction}
	
		{option:!widgetPhotogalleryLightbox.introduction}
			{$widgetPhotogalleryLightbox.text}
		{/option:!widgetPhotogalleryLightbox.introduction}
	
	{* Images *}
		{option:widgetPhotogalleryLightbox.images}
			<ul class="photogalleryLightbox">
				{iteration:widgetPhotogalleryLightbox.images}
				<li>
					<a href="{$var|createimage:{$widgetPhotogalleryLightbox.images.set_id}:{$widgetPhotogalleryLightbox.images.filename}:{$widgetPhotogalleryLightboxLargeResolution}}" rel="{$widgetPhotogalleryLightbox.id}" class="linkedImage linkOverlay">
						<img src="{$var|createimage:{$widgetPhotogalleryLightbox.images.set_id}:{$widgetPhotogalleryLightbox.images.filename}:{$widgetPhotogalleryLightboxThumbnailResolution}}
" />
					</a>
					
					{* Caption *}
					<div class="caption">
						{option:widgetPhotogalleryLightbox.images.title}<h3>{$widgetPhotogalleryLightbox.images.title}</h3>{/option:widgetPhotogalleryLightbox.images.title}
						{$widgetPhotogalleryLightbox.images.text}
					</div>
				</li>
				{/iteration:widgetPhotogalleryLightbox.images}
			</ul>
		{/option:widgetPhotogalleryLightbox.images}
	</div>
{/option:widgetPhotogalleryLightbox.images}