{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblPhotogallery|ucfirst}: {$lblAddCategory}</h2>
</div>

{form:addCategory}
    <div class="tabs">
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
            <li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
        </ul>

        <div id="tabContent">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td id="leftColumn">
                        <label for="title">{$lblTitle|ucfirst}</label>
                        {$txtTitle} {$txtTitleError}

                        <div id="pageUrl">
                            <div class="oneLiner">
                                {option:detailURL}<p><span><a href="{$detailURL}">{$detailURL}/<span id="generatedUrl"></span></a></span></p>{/option:detailURL}
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
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddCategory|ucfirst}" />
        </div>
    </div>
{/form:addCategory}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
