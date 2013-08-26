{option:!photogalleryWidgetFilterHideWidget}
	{form:photogalleryWidgetFilterForm}

		<div id="photogalleryWidgetFilterForm">

			{* categories *}
			<p>
				<label for="categories">{$lblCategories|ucfirst}</label>
				{$ddmCategories} {$ddmCategoriesError}
			</p>

			{* tags *}
			<p>
				<label for="tags">{$lblTags|ucfirst}</label>
				{$txtTags} {$txtTagsError}
			</p>

			{* title *}
			<p>
				<label for="title">{$lblTitle|ucfirst}</label>
				{$txtTitle} {$txtTitleError}
			</p>

			<p>
				<label for="images">{$chkImages} {$lblImages|ucfirst}</label>
				{$chkImagesError}
			</p>

			{* published after *}
			<p>
				<label for="publishedAfter">{$lblPublishedAfter|ucfirst}</label>
				{$txtPublishedAfter} {$txtPublishedAfterError}
			</p>

			{* published before *}
			<p>
				<label for="publishedBefore">{$lblPublishedBefore|ucfirst}</label>
				{$txtPublishedBefore} {$txtPublishedBeforeError}
			</p>

			{* submit *}
			<div class="fullwidthOptions">
				<div>
					<input id="submitButton" type="submit" name="submit" value="{$lblFilter|ucfirst}" />
				</div>
			</div>

		</div>

	{/form:photogalleryWidgetFilterForm}
{/option:!photogalleryWidgetFilterHideWidget}