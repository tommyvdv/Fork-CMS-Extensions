{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPhotogallery|ucfirst}: {$lblAddWidgetType|sprintf:{$lblRelatedByCategories}}</h2>
</div>

{form:addWidget}
	
	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblThumbnail|ucfirst}</h3>
		</div>
	
		<div class="options labelWidthLong">
			<p>
				<label for="thumbnailWidth">{$lblWidth|ucfirst}</label>
				{$txtThumbnailWidth} {$txtThumbnailWidthError}
			</p>
			<p>
				<label for="thumbnailHeight">{$lblHeight|ucfirst}</label>
				{$txtThumbnailHeight} {$txtThumbnailHeightError}
			</p>
			
			<p>
				<label for="thumbnailMehod">{$lblResizeMethod|ucfirst}</label>
				{$ddmThumbnailMethod} {$ddmThumbnailMethodError}
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