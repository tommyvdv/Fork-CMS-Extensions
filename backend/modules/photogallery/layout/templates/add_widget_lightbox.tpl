{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPhotogallery|ucfirst}: {$lblAddWidgetType|sprintf:{$lblLightbox}}</h2>
</div>

{form:addWidget}
	
	<p>
		<label for="title">{$lblTitle|ucfirst}</label>
		{$txtTitle} {$txtTitleError}
	</p>

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
			<h3>{$lblAppearance|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="showCloseButton">{$lblShowCloseButton|ucfirst}</label>
				{$ddmShowCloseButton} {$ddmShowCloseButtonError}
			</p>

			<p>
				<label for="showArrows">{$lblShowArrows|ucfirst}</label>
				{$ddmShowArrows} {$ddmShowArrowsError}
			</p>

			<p>
				<label for="showCaption">{$lblShowCaption|ucfirst}</label>
				{$ddmShowCaption} {$ddmShowCaptionError}
			</p>

			<p>
				<label for="captionType">{$lblCaptionType|ucfirst}</label>
				{$ddmCaptionType} {$ddmCaptionTypeError}
			</p>

			<p>
				<label for="padding">{$lblPadding|ucfirst}</label>
				{$txtPadding} {$txtPaddingError}
			</p>

			<p>
				<label for="margin">{$lblMargin|ucfirst}</label>
				{$txtMargin} {$txtMarginError}
			</p>

			<p>
				<label for="modal">{$lblModal|ucfirst}</label>
				{$ddmModal} {$ddmModalError}
			</p>
		</div>
	</div>


	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblMiscellaneous|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="closeClick">{$lblCloseClick|ucfirst}</label>
				{$ddmCloseClick} {$ddmCloseClickError}
			</p>

			<p>
				<label for="mediaHelper">{$lblMediaHelper|ucfirst}</label>
				{$ddmMediaHelper} {$ddmMediaHelperError}
			</p>
		
		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblAnimation|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="navigationEffect">{$lblNavigationEffect|ucfirst}</label>
				{$ddmNavigationEffect} {$ddmNavigationEffectError}
			</p>

			<p>
				<label for="openEffect">{$lblOpenEffect|ucfirst}</label>
				{$ddmOpenEffect} {$ddmOpenEffectError}
			</p>

			<p>
				<label for="closeEffect">{$lblCloseEffect|ucfirst}</label>
				{$ddmCloseEffect} {$ddmCloseEffectError}
			</p>
			
			<p>
				<label for="playSpeed">{$lblPlaySpeed|ucfirst}</label>
				{$txtPlaySpeed} {$txtPlaySpeedError}
			</p>

			<p>
				<label for="loop">{$lblLoop|ucfirst}</label>
				{$ddmLoop} {$ddmLoopError}
			</p>
		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblThumbnailNavigation|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="showThumbnails">{$lblShowThumbnails|ucfirst}</label>
				{$ddmShowThumbnails} {$ddmShowThumbnailsError}
			</p>

		
			<p>
				<label for="thumbnailsPosition">{$lblThumbnailsPosition|ucfirst}</label>
				{$ddmThumbnailsPosition} {$ddmThumbnailsPositionError}
			</p>
			
			<p>
				<label for="thumbnailWidth">{$lblThumbnailWidth|ucfirst}</label>
				{$txtThumbnailNavigationWidth} {$txtThumbnailNavigationWidthError}
			</p>

			<p>
				<label for="thumbnailHeight">{$lblThumbnailHeight|ucfirst}</label>
				{$txtThumbnailNavigationHeight} {$txtThumbnailNavigationHeightError}
			</p>
		</div>
	</div>


	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblOverlay|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="showOverlay">{$lblShowOverlay|ucfirst}</label>
				{$ddmShowOverlay} {$ddmShowOverlayError}
			</p>

			
			
			<p>
				<label for="overlayOpacity">{$lblOverlayOpacity|ucfirst}</label>
				{$txtOverlayOpacity} {$txtOverlayOpacityError}
			</p>

			<p>
				<label for="overlayColor">{$lblOverlayColor|ucfirst}</label>
				{$txtOverlayColor} {$txtOverlayColorError}
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