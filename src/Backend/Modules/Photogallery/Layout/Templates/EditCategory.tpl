{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblPhotogallery|ucfirst}: {$msgEditCategory|sprintf:{$item.title}}</h2>
</div>

{form:editCategory}
    <div class="tabs">
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
            <li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
        </ul>

        <div id="tabContent">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td id="leftColumn">
                        <label for="title">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                        {$txtTitle} {$txtTitleError}

                        <div id="pageUrl">
                            <div class="oneLiner">
                                {option:detailURL}<p><span><a href="{$detailURL}">{$detailURL}/<span id="generatedUrl">{$item.url}</span></a></span></p>{/option:detailURL}
                                {option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
                            </div>
                        </div>

                        {option:categories_depth}
                            <div class="options">
                                {option:categories}
                                    <label for="parentId">{$lblParent|ucfirst}</label>
                                    {$ddmParentId} {$ddmParentIdError}
                                {/option:categories}
                                {option:!categories}
                                    {$msgNoParents|sprintf:{$var|geturl:'add_category'}}
                                {/option:!categories}
                            </div>
                        {/option:categories_depth}
                    </td>
                </tr>
            </table>
        </div>

        <div id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
        </div>
    </div>

    <div class="fullwidthOptions">
        {option:deleteAllowed}
            <a href="{$var|geturl:'delete_category'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
                <span>{$lblDelete|ucfirst}</span>
            </a>
            <div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
                <p>
                    {$msgConfirmDeleteCategory|sprintf:{$item.title}}
                </p>
            </div>
        {/option:deleteAllowed}
        <div class="buttonHolderRight">
            <input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
        </div>
    </div>
{/form:editCategory}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
