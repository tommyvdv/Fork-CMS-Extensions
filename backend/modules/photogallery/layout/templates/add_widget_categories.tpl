{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPhotogallery|ucfirst}: {$lblAddWidgetType|sprintf:{$lblCategories}}</h2>
</div>

{form:addWidget}
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
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddWidget|ucfirst}" />
		</div>
	</div>
{/form:addWidget}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}