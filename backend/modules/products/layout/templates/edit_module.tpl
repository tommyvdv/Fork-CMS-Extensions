{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblProducts|ucfirst}: {$lblEditModule|sprintf:{$lblPhotogallery}}</h2>
</div>

{form:editWidget}

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblProductOverviewThumbnail|ucfirst}</h3>
		</div>

		<div class="options labelWidthLong">
			<p>
				<label for="productOverviewThumbnail">{$lblWidth|ucfirst}</label>
				{$txtProductOverviewThumbnailWidth} {$txtAlbumOverviewThumbnailWidthError}
			</p>
			<p>
				<label for="productDetailOverviewThumbnailHeight">{$lblHeight|ucfirst}</label>
				{$txtProductOverviewThumbnailHeight} {$txtAlbumOverviewThumbnailHeightError}
			</p>

			<p>
				<label for="productDetailOverviewThumbnailMehod">{$lblResizeMethod|ucfirst}</label>
				{$ddmProductOverviewThumbnailMethod} {$ddmAlbumOverviewThumbnailMethodError}
			</p>
		</div>
	</div>
	
	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblProductDetailOverviewThumbnail|ucfirst}</h3>
		</div>
	
		<div class="options labelWidthLong">
			<p>
				<label for="productDetailOverviewThumbnailWidth">{$lblWidth|ucfirst}</label>
				{$txtProductDetailOverviewThumbnailWidth} {$txtAlbumDetailOverviewThumbnailWidthError}
			</p>
			<p>
				<label for="productDetailOverviewThumbnailHeight">{$lblHeight|ucfirst}</label>
				{$txtProductDetailOverviewThumbnailHeight} {$txtAlbumDetailOverviewThumbnailHeightError}
			</p>
			
			<p>
				<label for="productDetailOverviewThumbnailMehod">{$lblResizeMethod|ucfirst}</label>
				{$ddmProductDetailOverviewThumbnailMethod} {$ddmAlbumDetailOverviewThumbnailMethodError}
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
