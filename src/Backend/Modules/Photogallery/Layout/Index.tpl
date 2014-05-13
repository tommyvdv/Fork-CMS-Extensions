{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblPhotogallery|ucfirst}: {$lblAlbums}</h2>
    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
            <span>{$lblAdd|ucfirst}</span>
        </a>
    </div>
</div>

<div class="dataGridHolder">
    {form:filter}
        <div class="dataFilter">
            <table cellspacing="0" cellpadding="0" border="0">
                <tbody>
                    <tr>
                        <td>
                            <div class="options">
                                <p>
                                    <label for="title">{$lblTitle|ucfirst}</label>
                                    {$txtTitle} {$txtTitleError}
                                </p>
                            </div>
                        </td>

                        <td>
                            <div class="options">
                                <p>
                                    <label for="hidden">{$lblPublish|ucfirst}</label>
                                    {$ddmHidden} {$ddmHiddenError}
                                </p>
                            </div>
                        </td>
    
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="99">
                            <div class="options">
                                <div class="buttonHolder">
                                    <input id="search" class="inputButton button mainButton" type="submit" name="search" value="{$lblUpdateFilter|ucfirst}" />
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    {/form:filter}

    {option:dataGrid}
        <form action="{$var|geturl:'mass_action'}" method="get" class="submitWithLink">
            <div>
                <input type="hidden" name="offset" value="{$offset}" />
                <input type="hidden" name="order" value="{$order}" />
                <input type="hidden" name="sort" value="{$sort}" />
                <input type="hidden" name="name" value="{$name}" />
                <input type="hidden" name="hidden" value="{$hidden}" />
            </div>
            {$dataGrid}
        </form>
    {/option:dataGrid}
</div>

{option:!dataGrid}
    <h3>{$lblAlbums|ucfirst}</h3>
    {option:filter}<p>{$msgNoItemsFilter|sprintf:{$var|geturl:'add'}}</p>{/option:filter}
    {option:!filter}<p>{$msgNoAlbums|sprintf:{$var|geturl:'add'}}</p>{/option:!filter}
{/option:!dataGrid}

<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
    <p>{$msgConfirmMassDelete}</p>
</div>

<div id="confirmHidden" title="{$lblHidden|ucfirst}?" style="display: none;">
    <p>{$msgConfirmMassHidden}</p>
</div>

<div id="confirmPublished" title="{$lblPublished|ucfirst}?" style="display: none;">
    <p>{$msgConfirmMassPublish}</p>
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
