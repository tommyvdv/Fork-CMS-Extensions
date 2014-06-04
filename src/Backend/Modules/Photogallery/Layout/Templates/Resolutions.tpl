{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblProducts|ucfirst}: {$lblResolutions}</h2>
    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add_resolution'}" class="button icon iconAdd" title="{$lblAddResolution|ucfirst}">
            <span>{$lblAddResolution|ucfirst}</span>
        </a>
    </div>
</div>

{option:dataGrid}
    <div class="dataGridHolder">
        {$dataGrid}
    </div>
{/option:dataGrid}

{option:!dataGrid}
    <p>{$msgNoResolutions|sprintf:{$var|geturl:'add_resolution'}}</p>
{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
