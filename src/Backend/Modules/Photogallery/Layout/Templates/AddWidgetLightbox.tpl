{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblPhotogallery|ucfirst}: {$lblAddWidgetType|sprintf:{$lblLightbox}}</h2>
</div>

{form:addWidget}
    
    {include:{$BACKEND_MODULES_PATH}/photogallery/layout/templates/Partials/WidgetFormContentLightbox.tpl}

    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddWidget|ucfirst}" />
        </div>
    </div>
{/form:addWidget}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
