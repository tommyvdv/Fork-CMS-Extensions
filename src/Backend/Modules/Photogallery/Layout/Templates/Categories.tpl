{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    {option:category}
        <h2>{$lblPhotogallery|ucfirst}: {$lblCategoriesForParent|sprintf:{$category.title}}</h2>
    {/option:category}
    {option:!category}
        <h2>{$lblPhotogallery|ucfirst}: {$lblCategories}</h2>
    {/option:!category}
    <div class="buttonHolderRight">
        {option:category}
            {option:add_allowed}
                <a href="{$addToParentURL}" class="button icon iconAdd"><span>{$lblAddCategoryToParent|sprintf:{$category.title}|ucfirst}</span></a>
            {/option:add_allowed}
        {/option:category}
        {option:!category}
            {option:add_allowed}
                <a href="{$var|geturl:'add_category'}" class="button icon iconAdd"><span>{$lblAddCategory|ucfirst}</span></a>
            {/option:add_allowed}
        {/option:!category}
    </div>
</div>

{option:breadcrumbs}
    <div class="wizard">
        <ul>
            {iteration:breadcrumbs}
                <li class="{option:breadcrumbs.beforeSelected}beforeSelected {/option:breadcrumbs.beforeSelected}{option:breadcrumbs.selected}selected {/option:breadcrumbs.selected}"><a href="{$var|geturl:'categories'}{option:!breadcrumbs.root}&amp;category_id={$breadcrumbs.id}{/option:!breadcrumbs.root}"><b>{$breadcrumbs.title}</b></a></li>
            {/iteration:breadcrumbs}
        </ul>
    </div>
{/option:breadcrumbs}

{option:dataGrid}
    <div class="dataGridHolder">
        {$dataGrid}
    </div>
{/option:dataGrid}
{option:!dataGrid}
    {option:category}
        {$msgNoCategories|sprintf:{$addToParentURL}}
        
    {/option:category}
    {option:!category}
        {$msgNoCategories|sprintf:{$var|geturl:'add_category'}}
    {/option:!category}
{/option:!dataGrid}

{option:isGod}
    {option:debug}
        depth: {$depth|dump}<br/>
        start_depth: {$start_depth|dump}<br/>
        limit_depth: {$limit_depth|dump}<br/>
        add_allowed: {$add_allowed|dump}<br/>
        add_child_allowed: {$add_child_allowed|dump}<br/>
    {/option:debug}
{/option:isGod}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
