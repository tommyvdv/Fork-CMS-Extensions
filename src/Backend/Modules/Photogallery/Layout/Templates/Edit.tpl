{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblPhotogallery|ucfirst}: {$msgEditAlbum|sprintf:{$record.title}}</h2>
    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add_images_choose'}&amp;album_id={$record.id}" class="button icon iconAdd" title="{$lblAddImages|ucfirst}">
            <span>{$lblUploadImages|ucfirst}</span>
        </a>
    </div>
</div>


{form:edit}
    
    <label for="title">{$lblTitle|ucfirst}</label>
    {$txtTitle} {$txtTitleError}
    
    
    <div id="pageUrl">
        <div class="oneLiner">
            {option:detailURL}<p><span><a href="{$detailURL}/{$record.url}">{$detailURL}/<span id="generatedUrl">{$record.url}</span></a></span></p>{/option:detailURL}
            {option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
        </div>
    </div>

    <div class="tabs">
        
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
            <li><a href="#tabImages">{$lblImages|ucfirst} ({$record.num_images_not_hidden}/{$record.num_images})</a></li>
            <li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
        </ul>

        <div id="tabContent">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td id="leftColumn">

                        {* Main content *}
                        <div class="box">
                            <div class="heading">
                                <h3>{$lblMainContent|ucfirst}</h3>
                            </div>
                            <div class="optionsRTE">
                                {$txtText} {$txtTextError}
                            </div>
                        </div>

                        {* Summary *}
                        <div class="box">
                            <div class="heading">
                                <div class="oneLiner">
                                    <h3>{$lblSummary|ucfirst}</h3>
                                    <abbr class="help">(?)</abbr>
                                    <div class="tooltip" style="display: none;">
                                        <p>{$msgHelpSummary}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="optionsRTE">
                                {$txtIntroduction} {$txtIntroductionError}
                            </div>
                        </div>
                        
                        {* Categories *}
                        {option:categories}
                            <div class="box">
                                <div class="heading">
                                    <h3>{$lblCategories|ucfirst}</h3>
                                </div>
                                <div class="options">
                                    <label for="categoryId">{$lblCategory|ucfirst}</label>
                                    {$ddmCategories} {$ddmCategoriesError}
                                 </div>
                            </div>
                        {/option:categories}
                        
                    </td>

                    <td id="sidebar">
                        <div id="publishOptions" class="box">
                            <div class="heading">
                                <h3>{$lblPublish|ucfirst}</h3>
                            </div>

                            <div class="options">
                                <ul class="inputList">
                                    {iteration:hidden}
                                        <li>
                                            {$hidden.rbtHidden}
                                            <label for="{$hidden.id}">{$hidden.label}</label>
                                        </li>
                                    {/iteration:hidden}
                                </ul>
                            </div>
                            
                            <div class="options">
                                <p class="p0"><label for="publishOnDate">{$lblPublishOn|ucfirst}</label></p>
                                <div class="oneLiner">
                                    <p>
                                        {$txtPublishOnDate} {$txtPublishOnDateError}
                                    </p>
                                    <p>
                                        <label for="publishOnTime">{$lblAt}</label>
                                    </p>
                                    <p>
                                        {$txtPublishOnTime} {$txtPublishOnTimeError}
                                    </p>
                                </div>
                            </div>

                            <div class="options">
                                <ul class="inputList pb0">
                                    <li>
                                        {$chkShowInAlbums} <label for="showInAlbums">{$lblShowInAlbums|ucfirst}</label>
                                        <span class="helpTxt">{$msgHelpShowInAlbums}</span>
                                    </li>
                                </ul>
                            </div>
                            
                             
                        </div>
                        
                        <div class="box">
                            <div class="heading">
                                <h3>{$lblMetaData|ucfirst}</h3>
                            </div>
                            <div class="options">
                                <label for="tags">{$lblTags|ucfirst}</label>
                                {$txtTags} {$txtTagsError}
                            </div>
                        </div>
                        
                        <div class="box">
                            <div class="heading">
                                <h3>{$chkNew} <label for="new">{$lblNew|ucfirst}</label></h3>
                            </div>
                            <div class="options toggleNew">
                                <p>
                                    <label for="newDateFrom">{$lblFrom|ucfirst}</label>
                                    {$txtNewDateFrom} {$txtNewDateFromError}

                                    <label for="newDateUntil">{$lblUntil}</label>
                                    {$txtNewDateUntil} {$txtNewDateUntilError}
                                </p>
                            </div>
                        </div>
                        
                    </td>
                </tr>
            </table>
        </div>

        <div id="tabImages">
            
            {option:dataGrid}
                <div class="dataGridHolder">
                    {$dataGrid}
                </div>
            {/option:dataGrid}

            {option:!dataGrid}
                <p class="p0">{$msgNoImages|sprintf:{$add_images_choose_url}}</p>
            {/option:!dataGrid}
            
        </div>
        
        <div id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
        </div>
        
    </div>

    <div class="fullwidthOptions">
        <a href="{$var|geturl:'delete'}&amp;id={$record.id}" data-message-id="confirmDeleteAlbum" class="askConfirmation button linkButton icon iconDelete">
            <span>{$lblDelete|ucfirst}</span>
        </a>
        <div class="buttonHolderRight">
            <input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
        </div>
    </div>

    <div id="confirmDeleteAlbum" title="{$lblDelete|ucfirst}?" style="display: none;">
        <p>
            {$msgConfirmDelete|sprintf:{$record.title}}
        </p>
    </div>
    
    
    <div id="addCategoryDialog" class="forkForms" title="{$lblAddCategory|ucfirst}" style="display: none;">
        <div id="templateList">
            <p>
                <label for="categoryTitle">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                <input type="text" name="categoryTitle" id="categoryTitle" class="inputText" maxlength="255" />
                <span class="formError" id="categoryTitleError" style="display: none;">{$errFieldIsRequired|ucfirst}</span>
            </p>
        </div>
    </div>
    
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
