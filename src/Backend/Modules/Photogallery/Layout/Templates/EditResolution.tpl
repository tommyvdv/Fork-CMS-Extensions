{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblProducts|ucfirst}: {option:record.allow_edit}{$lblEditResolution}{/option:record.allow_edit}{option:!record.allow_edit}{$lblDetailsResolution}{/option:!record.allow_edit}</h2>

    <div class="buttonHolderRight">
        <a href="{$var|geturl:'resolutions'}" class="button icon iconBack"><span>{$lblBack|ucfirst}</span></a>
    </div>
</div>

{form:editResolution}

    <div class="generalMessage infoMessage content singleMessage hidden js-warning-method">
        <p>{$msgWarningMethodNotApplicable}</p>
    </div>

    <div class="generalMessage infoMessage content singleMessage hidden js-warning-watermark">
        <p>{$msgWarningWatermarkNotAllowedGlobally}</p>
    </div>
    
    <p>
        <label for="kind">{$lblKind|ucfirst}</label>
        {$txtKind} {$txtKindError}
    </p>

    <table width="100%">
        <tr>
            <td id="leftColumn">
                <div class="box horizontal">
                    <div class="heading">
                        <h3>{$lblResolution|ucfirst}</h3>
                    </div>
                
                    <div class="options labelWidthLong">
                        <p>
                            <label for="thumbnailWidth">{$lblWidth|ucfirst}</label>
                            {$txtWidth} {$chkWidthNull} {$txtWidthError}
                        </p>

                        <p>
                            <label for="thumbnailHeight">{$lblHeight|ucfirst}</label>
                            {$txtHeight} {$chkHeightNull} {$txtHeightError}
                        </p>
                        
                        <p>
                            <label for="thumbnailMehod">{$lblResizeMethod|ucfirst}</label>
                            {$ddmMethod} {$ddmMethodError}
                        </p>

                        <p>
                            <label for="allow_watermark">{$lblAllowWatermark|ucfirst}</label>
                            {$chkAllowWatermark}
                        </p>
                    </div>
                </div>
            </td>

            <td id="sidebar">
                <div class="box {option:!record.allow_watermark}hidden{/option:!record.allow_watermark} js-dependant-allowwatermark">
                    <div class="heading">
                        <h3>{$lblWatermark|ucfirst}</h3>
                    </div>
                    <div class="options clearfix">
                        {option:record.watermark}
                            <p class="watermarkHolder">
                                <img src="{$FRONTEND_FILES_URL}/products/watermarks/source/{$record.watermark}" width="100%" alt="{$lblWatermark|ucfirst}" />
                                <label for="deleteWatermark">{$chkDeleteWatermark} {$lblDelete|ucfirst}</label>
                                {$chkDeleteWatermarkError}
                            </p>
                        {/option:record.watermark}
                        <p>
                            <label for="watermark">{$lblWatermark|ucfirst}</label>
                            {$fileWatermark} {$fileWatermarkError}
                        </p>
                    </div>
                    <div class="options labelWidthLong">
                        <p>
                            <label for="position">{$lblPosition|ucfirst}</label>
                        </p>
                        <div class="positions">
                            {iteration:position}
                                <div class="position-cell">{$position.rbtPosition}</div>
                            {/iteration:position}
                        </div>
                    </div>
                    <div class="options labelWidthLong pixelInput">
                        <p>
                            <label for="watermarkPadding">{$lblWatermarkPadding|ucfirst}</label>
                            {$txtWatermarkPadding}<span class="unit">{$lblPixels}</span> {$txtWatermarkPaddingError}
                        </p>
                    </div>
                </div>

                <div class="box">
                    <div class="heading">
                        <h3>{$lblRegenerate|ucfirst}</h3>
                    </div>
                    <div class="options labelWidthLong">
                        <p>
                            <label for="regenerate">{$lblRegenerate|ucfirst}</label>
                            {$chkRegenerate}
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="fullwidthOptions">
        {option:record.allow_delete}
            <a href="{$var|geturl:'delete_resolution'}&amp;id={$record.id}" data-message-id="confirmDeleteResolution" class="askConfirmation button linkButton icon iconDelete">
                <span>{$lblDelete|ucfirst}</span>
            </a>
        {/option:record.allow_delete}
        <div class="buttonHolderRight">
            {option:record.allow_edit}
                <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
            {/option:record.allow_edit}
        </div>
    </div>
{/form:editResolution}

<div id="confirmDeleteResolution" title="{$lblDelete|ucfirst}?" style="display: none;">
    <p>
        {$msgConfirmDelete|sprintf:{$record.kind}
    </p>
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
