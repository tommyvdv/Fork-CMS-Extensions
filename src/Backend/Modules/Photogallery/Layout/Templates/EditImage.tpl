{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblPhotogallery|ucfirst}: {$msgEditImage}</h2>
    
    <div class="buttonHolderRight">
        <a href="{$var|geturl:'edit'}&amp;id={$album_id}#tabImages" class="button icon iconBack"><span>{$lblBack|ucfirst}</span></a>
    </div>
    
</div>

{form:edit}
    <label for="title">{$lblTitle|ucfirst}</label>
    {$txtTitle} {$txtTitleError}

    <div id="pageUrl">
        <div class="oneLiner">
            {option:detailURL}<p><span><a href="{$detailURL}/{$item.url}">{$detailURL}/<span id="generatedUrl">{$record.url}</span></a></span></p>{/option:detailURL}
            {option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
        </div>
    </div>
        <div class="tabs">
            <ul>
                <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
                <li><a href="#tabLink">{$lblLink|ucfirst}</a></li>
                <li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
            </ul>

            <div id="tabContent">
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td id="leftColumn">
                            <div class="box">
                                <div class="heading">
                                    <h3>{$lblContent|ucfirst}</h3>
                                </div>
                                <div class="optionsRTE">
                                    {$txtText} {$txtTextError}
                                </div>
                            </div>
                        </td>

                        <td id="sidebar">


                            <div class="box">
                                <div class="heading">
                                    <h3>{$lblSettings|ucfirst}</h3>
                                </div>

                                <div class="options">
                                    <ul class="inputList pb0">
                                        <li>{$chkTitleHidden} {$chkTitleHiddenError} <label for="titleHidden">{$lblHideTitle|ucfirst}</label></li>
                                    </ul>
                                </div>
                            </div>


                            <div class="box">
                                <div class="heading">
                                    <h3>{$lblStatus|ucfirst}</h3>
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
                            </div>
                    
                    
                            <div class="box">
                                <div class="heading">
                                    <h3>{$lblPreview|ucfirst}</h3>
                                </div>

                                <div class="options">
                                    <p>
                                        {$previewImageHTML}
                                    </p>
                                </div>
                            </div>

                        </td>
                    </tr>
                </table>
            </div>

            
            <div id="tabLink">
                {$rbtLinkError}
                <ul class="inputList radiobuttonFieldCombo pb0">
                    {iteration:link}
                        <li>
                            <label for="{$link.id}">{$link.rbtLink} {$link.label}</label>
                            {option:link.isInternal}
                                    {$ddmInternalLink} {$ddmInternalLinkError}
                            {/option:link.isInternal}

                            {option:link.isExternal}
                                    {$txtExternalLink} {$txtExternalLinkError}
                            {/option:link.isExternal}

                            {option:link.isEmbed}
                                    {$txtEmbed} {$txtEmbedError}
                            {/option:link.isEmbed}

                            {option:link.isIframe}
                                    {$txtIframe} {$txtIframeError}
                            {/option:link.isIframe}
                        </li>
                    {/iteration:link}
                </ul>
            </div>
            
            
            <div id="tabSEO">
                {include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
            </div>
        </div>

        <div class="fullwidthOptions">
            <a href="{$var|geturl:'delete_image'}&amp;id={$record.id}&amp;album_id={$album_id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
                <span>{$lblDelete|ucfirst}</span>
            </a>
            <div class="buttonHolderRight">
                <input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
            </div>
        </div>

        <div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
            <p>
                {$msgConfirmDelete|sprintf:{$record.title}}
            </p>
        </div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
