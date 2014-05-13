{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    
    <h2>{$lblPhotogallery|ucfirst}: {$lblAddWidgetChoose}</h2>
</div>

{form:choose}

    <div class="box">
        <div class="heading">
            <h3>{$lblWidgetTypes|ucfirst}</h3>
        </div>
            <div class="options">
                <ul>
                    {iteration:options}
                        <li>
                            {$options.rbtOptions}
                            <label for="{$options.id}">{$options.label}</label>
                        </li>
                    {/iteration:options}
                </ul>
        </div>
    </div>

    <div class="fullwidthOptions">
        <a href="{$var|geturl:'extras'}}" class="button linkButton">
            <span>{$lblCancel|ucfirst}</span>
        </a>
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblNext|ucfirst}" />
        </div>
    </div>
 
{/form:choose}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
