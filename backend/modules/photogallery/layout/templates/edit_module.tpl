{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPhotogallery|ucfirst}: {$lblEditModule|sprintf:{$lblPhotogallery}}</h2>
</div>

{form:editWidget}

<div class="tabs">
		
	<ul>
		<li><a href="#tabGeneral">{$lblGeneral|ucfirst}</a></li>
		<li><a href="#tabLightboxSettings">{$lblLightboxSettings|ucfirst}</a></li>
	</ul>

	<div id="tabGeneral">

		<div class="box horizontal">
			<div class="heading">
				<h3>{$lblAlbumOverviewThumbnail|ucfirst}</h3>
			</div>

			<div class="options labelWidthLong">
				<p>
					<label for="albumOverviewThumbnail">{$lblWidth|ucfirst}</label>
					{$txtAlbumOverviewThumbnailWidth} {$txtAlbumOverviewThumbnailWidthError}
				</p>
				<p>
					<label for="albumDetailOverviewThumbnailHeight">{$lblHeight|ucfirst}</label>
					{$txtAlbumOverviewThumbnailHeight} {$txtAlbumOverviewThumbnailHeightError}
				</p>

				<p>
					<label for="albumDetailOverviewThumbnailMehod">{$lblResizeMethod|ucfirst}</label>
					{$ddmAlbumOverviewThumbnailMethod} {$ddmAlbumOverviewThumbnailMethodError}
				</p>
			</div>
		</div>
		
		<div class="box horizontal">
			<div class="heading">
				<h3>{$lblAlbumDetailOverviewThumbnail|ucfirst}</h3>
			</div>
		
			<div class="options labelWidthLong">
				<p>
					<label for="albumDetailOverviewThumbnailWidth">{$lblWidth|ucfirst}</label>
					{$txtAlbumDetailOverviewThumbnailWidth} {$txtAlbumDetailOverviewThumbnailWidthError}
				</p>
				<p>
					<label for="albumDetailOverviewThumbnailHeight">{$lblHeight|ucfirst}</label>
					{$txtAlbumDetailOverviewThumbnailHeight} {$txtAlbumDetailOverviewThumbnailHeightError}
				</p>
				
				<p>
					<label for="albumDetailOverviewThumbnailMehod">{$lblResizeMethod|ucfirst}</label>
					{$ddmAlbumDetailOverviewThumbnailMethod} {$ddmAlbumDetailOverviewThumbnailMethodError}
				</p>
			</div>
		</div>

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
		
		<div class="box horizontal">
			<div class="heading">
				<h3>{$lblAction|ucfirst}</h3>
			</div>
			<div class="options labelWidthLong">
				{$rbtActionError}
				<ul class="inputList pb0">
					{iteration:action}
						<li>
							<label for="{$action.id}">{$action.rbtAction} {$action.label}</label>
						</li>
					{/iteration:action}
				</ul>
			</div>
		</div>
		
		
		<div class="box horizontal">
			<div class="heading">
				<h3>{$lblDisplay|ucfirst}</h3>
			</div>
			<div class="options labelWidthLong">
				{$rbtDisplayError}
				<ul class="inputList pb0">
					{iteration:display}
						<li>
							<label for="{$display.id}">{$display.rbtDisplay} {$display.label}</label>
						</li>
					{/iteration:display}
				</ul>
			</div>
		</div>
	</div>


	<div id="tabLightboxSettings">

		{include:{$BACKEND_MODULES_PATH}/photogallery/layout/templates/form_content_lightbox_settings.tpl}
	</div>
</div>
	
	<div class="fullwidthOptions">
			<a href="{$var|geturl:'extras'}" class="button linkButton">
				<span>{$lblCancel|ucfirst}</span>
			</a>
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSaveModule|ucfirst}" />
		</div>
	</div>
{/form:editWidget}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}