{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	
	<h2>{$lblPhotogallery|ucfirst}: {$lblAddImagesForAlbum|sprintf:{$record.title}}</h2>
	
	{option:zip_upload}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_images_upload_zip'}&amp;album_id={$record.id}" class="button" title="{$lblAdd|ucfirst}">
			<span>{$lblUploadZipFile|ucfirst}</span>
		</a>
	</div>
	{/option:zip_upload}
	
</div>

<div class="wizard">
	<ul>
		<li><a href="{$var|geturl:'edit'}&amp;id={$record.id}"><b><span>1.</span> {$lblWizardInformation|ucfirst}</b></a></li>
		<li><b><span>2.</span> {$lblWizardAddImages|ucfirst}</b></li>
	</ul>
</div>

{form:add}
	
	<div class="content">
		
		<div class="box">
			<div class="heading">
				<h3>{$lblImages|ucfirst}</h3>
			</div>
			<div class="content">
				{iteration:imageFields}
				<p>
					{$imageFields.fileImage} {$imageFields.fileImageError}
				</p>
				{/iteration:imageFields}
				
			</div>
		</div>
	</div>
	<div class="fullwidthOptions">
			<a href="{$var|geturl:'edit'}&amp;id={$record.id}" class="button linkButton">
				<span>{$lblCancel|ucfirst}</span>
			</a>
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblUploadImages|ucfirst}" />
		</div>
	</div>
 
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}