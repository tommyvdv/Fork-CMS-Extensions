{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblProducts|ucfirst}: {$lblAddResolution}</h2>
</div>

{form:addResolution}
    
    <p>
        <label for="kind">{$lblKind|ucfirst}</label>
        {$txtKind} {$txtKindError}
    </p>

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
        </div>
    </div>

    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddResolution|ucfirst}" />
        </div>
    </div>
{/form:addResolution}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
