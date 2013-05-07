{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblPhotogallery|ucfirst}: {$lblEditWidget|sprintf:{$lblSlideshow}}</h2>
</div>

{form:editWidget}

	<p>
		<label for="title">{$lblTitle|ucfirst}</label>
		{$txtTitle} {$txtTitleError}
	</p>
	
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
				<label for="showCaption">{$lblShowCaption|ucfirst}</label>
				{$ddmShowCaption} {$ddmShowCaptionError}
			</p>
			<p>
				<label for="slideshowItemWidth">{$lblSlideshowItemWidth|ucfirst}</label>
				{$txtSlideshowItemWidth} {$txtSlideshowItemWidthError}
				<span class="helpTxt">{$msgSlideshowItemWidthHelpText}</span>
			</p>

		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblNavigation|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="showPagination">{$lblShowPagination|ucfirst}</label>
				{$ddmShowPagination} {$ddmShowPaginationError}
			</p>

			<p>
				<label for="paginationType">{$lblPaginationType|ucfirst}</label>
				{$ddmPaginationType} {$ddmPaginationTypeError}
			</p>

			<p>
				<label for="showArrows">{$lblShowArrows|ucfirst}</label>
				{$ddmShowArrows} {$ddmShowArrowsError}
			</p>

			<p>
				<label for="pauseOnHover">{$lblPauseOnHover|ucfirst}</label>
				{$ddmPauseOnHover} {$ddmPauseOnHoverError}
			</p>
		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblAnimation|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="random">{$lblRandom|ucfirst}</label>
				{$ddmRandom} {$ddmRandomError}
			</p>

			<p>
				<label for="slideshowSpeed">{$lblSlideshowSpeed|ucfirst}</label>
				{$txtSlideshowSpeed} {$txtSlideshowSpeedError}
			</p>

			<p>
				<label for="anitmationSpeed">{$lblAnimationSpeed|ucfirst}</label>
				{$txtAnimationSpeed} {$txtAnimationSpeedError}
			</p>

			<p>
				<label for="animation">{$lblAnimation|ucfirst}</label>
				{$ddmAnimation} {$ddmAnimationError}
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