{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblPhotogallery}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$lblInterface|ucfirst}</h3>
		</div>

		<div class="options">
			<label for="categoriesDepthStart">{$lblCategoriesDepthStart|ucfirst}</label>
			{$ddmCategoriesDepthStart}
			{$ddmCategoriesDepthStartError}
		</div>

		<div class="options">
			<label for="categoriesDepth">{$lblCategoriesDepth|ucfirst}</label>
			{$ddmCategoriesDepth}
			{$ddmCategoriesDepthError}
		</div>
	</div>
	
	<div class="box">
		<div class="heading">
			<h3>{$lblPagination|ucfirst}</h3>
		</div>

		<div class="options">
			<label for="overviewAlbumsNumberOfItems">{$lblItemsPerPage|ucfirst}</label>
			{$ddmOverviewAlbumsNumberOfItems}
			{$ddmOverviewAlbumsNumberOfItemsError}
		</div>

		<div class="options">
			<label for="overviewCategoriesNumberOfItems">{$lblItemsPerCategoryPage|ucfirst}</label>
			{$ddmOverviewCategoriesNumberOfItems}
			{$ddmOverviewCategoriesNumberOfItemsError}
		</div>

		<div class="options">
			<label for="relatedListCategoriesNumberOfItems">{$msgNumItemsInRelatedCategoryList|ucfirst}</label>
			{$ddmRelatedListCategoriesNumberOfItems}
			{$ddmRelatedListCategoriesNumberOfItemsError}
		</div>

		<div class="options">
			<label for="relatedListTagsNumberOfItems">{$msgNumItemsInRelatedTagsList|ucfirst}</label>
			{$ddmRelatedListTagsNumberOfItems}
			{$ddmRelatedListTagsNumberOfItemsError}
		</div>

		<div class="options">
			<label for="relatedCategoriesNumberOfItems">{$msgNumItemsByRelatedCategories|ucfirst}</label>
			{$ddmRelatedCategoriesNumberOfItems}
			{$ddmRelatedCategoriesNumberOfItemsError}
		</div>

		<div class="options">
			<label for="relatedTagsNumberOfItems">{$msgNumItemsByRelatedTags|ucfirst}</label>
			{$ddmRelatedTagsNumberOfItems}
			{$ddmRelatedTagsNumberOfItemsError}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblSEO}</h3>
		</div>
		<div class="options">
			<p>{$msgHelpPingServices}:</p>
			<ul class="inputList p0">
				<li><label for="pingServices">{$chkPingServices} {$lblPingBlogServices|ucfirst}</label></li>
			</ul>
		</div>
	</div>

	<div class="box">
		<div class="horizontal">
			<div class="heading">
				<h3>{$lblRSSFeed|ucfirst}</h3>
			</div>
			<div class="options">
				<label for="rssTitle">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
				{$txtRssTitle} {$txtRssTitleError}
				<span class="helpTxt">{$msgHelpRSSTitle}</span>
			</div>
			<div class="options">
				<label for="rssDescription">{$lblDescription|ucfirst}</label>
				{$txtRssDescription} {$txtRssDescriptionError}
				<span class="helpTxt">{$msgHelpRSSDescription}</span>
			</div>
			<div class="options">
				<label for="feedburnerUrl">{$lblFeedburnerURL|ucfirst}</label>
				{$txtFeedburnerUrl} {$txtFeedburnerUrlError}
				<span class="helpTxt">{$msgHelpFeedburnerURL}</span>
			</div>
			<div class="options">
				<p>{$msgHelpMeta}:</p>
				<ul class="inputList p0">
					<li><label for="rssMeta">{$chkRssMeta} {$lblMetaInformation|ucfirst}</label></li>
				</ul>
			</div>
		</div>
	</div>
	
	
	
	<div class="box">
		<div class="horizontal">
			<div class="heading">
				<h3>{$lblLicense|ucfirst}</h3>
			</div>
			
			<div class="options">
				<label for="licenseKey">{$lblKey|ucfirst}</label>
				{$txtLicenseKey} {$txtLicenseKeyError}
			</div>
			<div class="options">
				<label for="licenseName">{$lblName|ucfirst}</label>
				{$txtLicenseName} {$txtLicenseNameError}
			</div>
			<div class="options">
				<label for="licenseDomain">{$lblDomain|ucfirst}</label>
				{$txtLicenseDomain} {$txtLicenseDomainError}
			</div>
			
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}