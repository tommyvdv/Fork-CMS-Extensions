{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblModuleSettings|ucfirst}: {$lblPhotogallery}</h2>
</div>

{form:settings}
    <div class="box">
        <div class="heading">
            <h3>{$lblCategories|ucfirst}</h3>
        </div>

        <div class="options">
            <p>{$msgHelpCategoriesDepth}</p>

            <label for="categoriesDepthStart">{$lblCategoriesDepthStart|ucfirst}</label>
            {$ddmCategoriesDepthStart}
            {$ddmCategoriesDepthStartError}

            <label for="categoriesDepth">{$lblCategoriesDepth}</label>
            {$ddmCategoriesDepth}
            {$ddmCategoriesDepthError}
        </div>
    </div>

    <div class="box">
        <div class="heading">
            <h3>{$lblAlbums|ucfirst}</h3>
        </div>

        <div class="options">
            <p>{$msgHelpAlbumsCategoriesDepth}</p>

            <label for="albumsCategoriesDepthStart">{$lblAlbumsCategoriesDepthStart|ucfirst}</label>
            {$ddmAlbumsCategoriesDepthStart}
            {$ddmAlbumsCategoriesDepthStartError}

            <label for="albumsCategoriesDepth">{$lblAlbumsCategoriesDepth}</label>
            {$ddmAlbumsCategoriesDepth}
            {$ddmAlbumsCategoriesDepthError}
        </div>
    </div>

    <div class="box">
        <div class="heading">
            <h3>{$lblResolutions|ucfirst}</h3>
        </div>
        <div class="options">
            <p>{$msgHelpWatermark}</p>
            <ul class="inputList p0">
                <li><label for="allowWatermark">{$chkAllowWatermark} {$lblAllowWatermark|ucfirst}</label></li>
            </ul>
        </div>
    </div>
    
    <div class="box">
        <div class="heading">
            <h3>{$lblPagination|ucfirst}</h3>
        </div>

        <div class="options">
            <label for="generalNumberOfItems">{$lblGeneralItemsPerPage|ucfirst}</label>
            {$ddmGeneralNumberOfItems}
            {$ddmGeneralNumberOfItemsError}

            <label for="noSpecificNumberOfItems">{$chkNoSpecificNumberOfItems} {$lblNoNumberOfItems|ucfirst}</label>
        </div>

        <div class="options">
            <ul class="inputList p0">
                <li><label for="specificNumberOfItems">{$chkSpecificNumberOfItems} {$lblSpecificNumberOfItems|ucfirst}</label></li>
            </ul>
        </div>

        <div class="js-checkbox-dependant hidden" data-dependant-on="specific_number_of_items">
            <div class="options">
                <label for="overviewAlbumsNumberOfItems">{$lblItemsPerPage|ucfirst}</label>
                {$ddmOverviewAlbumsNumberOfItems}
                {$ddmOverviewAlbumsNumberOfItemsError}

                <label for="noOverviewAlbumsNumberOfItems">{$chkNoOverviewAlbumsNumberOfItems} {$lblNoNumberOfItems|ucfirst}</label>
            </div>

            <div class="options">
                <label for="overviewCategoriesNumberOfItems">{$lblItemsPerCategoryPage|ucfirst}</label>
                {$ddmOverviewCategoriesNumberOfItems}
                {$ddmOverviewCategoriesNumberOfItemsError}

                <label for="noOverviewCategoriesNumberOfItems">{$chkNoOverviewCategoriesNumberOfItems} {$lblNoNumberOfItems|ucfirst}</label>
            </div>

            <div class="options">
                <label for="relatedListCategoriesNumberOfItems">{$msgNumItemsInRelatedCategoryList|ucfirst}</label>
                {$ddmRelatedListCategoriesNumberOfItems}
                {$ddmRelatedListCategoriesNumberOfItemsError}

                <label for="noRelatedListCategoriesNumberOfItems">{$chkNoRelatedListCategoriesNumberOfItems} {$lblNoNumberOfItems|ucfirst}</label>
            </div>

            <div class="options">
                <label for="relatedListTagsNumberOfItems">{$msgNumItemsInRelatedTagsList|ucfirst}</label>
                {$ddmRelatedListTagsNumberOfItems}
                {$ddmRelatedListTagsNumberOfItemsError}

                <label for="noRelatedListTagsNumberOfItems">{$chkNoRelatedListTagsNumberOfItems} {$lblNoNumberOfItems|ucfirst}</label>
            </div>

            <div class="options">
                <label for="relatedCategoriesNumberOfItems">{$msgNumItemsByRelatedCategories|ucfirst}</label>
                {$ddmRelatedCategoriesNumberOfItems}
                {$ddmRelatedCategoriesNumberOfItemsError}

                <label for="noRelatedCategoriesNumberOfItems">{$chkNoRelatedCategoriesNumberOfItems} {$lblNoNumberOfItems|ucfirst}</label>
            </div>

            <div class="options">
                <label for="relatedTagsNumberOfItems">{$msgNumItemsByRelatedTags|ucfirst}</label>
                {$ddmRelatedTagsNumberOfItems}
                {$ddmRelatedTagsNumberOfItemsError}

                <label for="noRelatedTagsNumberOfItems">{$chkNoRelatedTagsNumberOfItems} {$lblNoNumberOfItems|ucfirst}</label>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="heading">
            <h3>{$lblSEO}</h3>
        </div>
        <div class="options">
            <p>{$msgHelpPingServices}</p>
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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
