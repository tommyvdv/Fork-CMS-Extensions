{option:widgetPhotogalleryPaged.images}
	<div class="photoGalleryPagedWrapper photoGalleryPagedId{$widgetPhotogalleryPaged.id}">
	{* Title *}
		<h3>{$widgetPhotogalleryPaged.title}</h3>
	
	{* Meta *}
		<ul>
			<li>
				{* Written on *}
				{$widgetPhotogalleryPaged.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}

				{* Category *}
				{option:widgetPhotogalleryPaged.category_title}
					{$lblIn} {$lblThe} {$lblCategory} 
					<a href="{$widgetPhotogalleryPaged.category_full_url}" title="{$widgetPhotogalleryPaged.category_title}">{$widgetPhotogalleryPaged.category_title}</a>
					{option:!widgetPhotogalleryPaged.tags}.{/option:!widgetPhotogalleryPaged.tags}
				{/option:widgetPhotogalleryPaged.category_title}
			
				{* Tags *}
				{option:widgetPhotogalleryPaged.tags}
					{$lblWith} {$lblThe} {$lblTags}
					{iteration:widgetPhotogalleryPaged.tags}
						<a href="{$widgetPhotogalleryPaged.tags.full_url}" rel="tag" title="{$widgetPhotogalleryPaged.tags.name}">{$widgetPhotogalleryPaged.tags.name}</a>
						{option:!widgetPhotogalleryPaged.tags.last}, {/option:!widgetPhotogalleryPaged.tags.last}
						{option:widgetPhotogalleryPaged.tags.last}.{/option:widgetPhotogalleryPaged.tags.last}
					{/iteration:widgetPhotogalleryPaged.tags}
				{/option:widgetPhotogalleryPaged.tags}
			</li>
		</ul>
	
	{* Content *}
		{option:widgetPhotogalleryPaged.introduction}
			{$widgetPhotogalleryPaged.introduction}
		{/option:widgetPhotogalleryPaged.introduction}
	
		{option:!widgetPhotogalleryPaged.introduction}
			{$widgetPhotogalleryPaged.text}
		{/option:!widgetPhotogalleryPaged.introduction}
	
	{* Images *}
		<ul class="photoGalleryPaged">
			{iteration:widgetPhotogalleryPaged.images}
			<li>
				<a href="{$widgetPhotogalleryPaged.images.full_url}" rel="{$widgetPhotogalleryPaged.id}" class="linkedImage" title="{$widgetPhotogalleryPaged.images.title}">
					<img src="{$widgetPhotogalleryPaged.images.thumbnail_url}" alt="{$widgetPhotogalleryPaged.images.title}" title="{$widgetPhotogalleryPaged.images.title}" />
				</a>
			</li>
			{/iteration:widgetPhotogalleryPaged.images}
		</ul>
	</div>
{/option:widgetPhotogalleryPaged.images}