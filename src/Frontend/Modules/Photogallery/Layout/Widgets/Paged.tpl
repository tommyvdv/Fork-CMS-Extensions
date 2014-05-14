{option:widgetPhotogalleryPaged.images}
	<div class="photogalleryPagedWrapper photogalleryPagedId{$widgetPhotogalleryPaged.id}">
	{* Title *}
		<h3>{$widgetPhotogalleryPaged.title}</h3>
	
	{* Meta *}
		<ul>
			<li>
				{* Written on *}
				{$widgetPhotogalleryPaged.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}

				{* Category*}
				{option:widgetPhotogalleryPaged.categories}
					{$lblIn} {$lblThe} {$lblCategory} 
					{iteration:widgetPhotogalleryPaged.categories}
						<a href="{$widgetPhotogalleryPaged.categories.full_url}" rel="tag" title="{$widgetPhotogalleryPaged.categories.title}">{$widgetPhotogalleryPaged.categories.title}</a>{option:!widgetPhotogalleryPaged.categories.last}, {/option:!widgetPhotogalleryPaged.categories.last}{option:widgetPhotogalleryPaged.categories.last}.{/option:widgetPhotogalleryPaged.categories.last}
					{/iteration:widgetPhotogalleryPaged.categories}
				{/option:widgetPhotogalleryPaged.categories}
			
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
		<ul class="photogalleryPaged">
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