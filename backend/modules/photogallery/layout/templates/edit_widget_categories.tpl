{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPhotogallery|ucfirst}: {$lblEditWidget|sprintf:{$lblCategories}}</h2>
</div>

{form:editWidget}
	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblLarge|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="largeWidth">{$lblWidth|ucfirst}</label>
				{$txtLargeWidth} {$txtLargeWidthError}
			</p>
			<p>
				<label for="largeHeight">{$lblHeight|ucfirst}</label>
				{$txtLargeHeight} {$txtLargeHeightError}
			</p>
			
			<p>
				<label for="largeMehod">{$lblResizeMethod|ucfirst}</label>
				{$ddmLargeMethod} {$ddmLargeMethodError}
			</p>
		</div>
	</div>
	
	<div class="fullwidthOptions">
		{option:item.allow_delete}
		<a href="{$var|geturl:'delete_extra'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDeleteExtra}
			</p>
		</div>
		{/option:item.allow_delete}
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSaveWidget|ucfirst}" />
		</div>
	</div>
{/form:editWidget}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}