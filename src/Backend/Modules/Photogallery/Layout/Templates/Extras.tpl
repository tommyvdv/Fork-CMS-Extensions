{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblPhotogallery|ucfirst}: {$lblExtras}</h2>
    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add_widget_choose'}" class="button icon iconAdd" title="{$lblAddWidget|ucfirst}">
            <span>{$lblAddWidget|ucfirst}</span>
        </a>
    </div>
</div>

{option:dataGrid}
    <div class="dataGridHolder">
        {$dataGrid}
    </div>
{/option:dataGrid}

{option:!dataGrid}
    <p>{$msgNoWidgets|sprintf:{$var|geturl:'add_widget_choose'}}</p>
{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
