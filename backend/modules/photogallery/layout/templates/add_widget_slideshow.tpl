{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPhotogallery|ucfirst}: {$lblAddWidgetType|sprintf:{$lblSlideshow}}</h2>
</div>

{form:addWidget}
	
	{include:{$BACKEND_MODULES_PATH}/photogallery/layout/templates/widget_form_content_slideshow.tpl}
	
	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddWidget|ucfirst}" />
		</div>
	</div>
{/form:addWidget}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}