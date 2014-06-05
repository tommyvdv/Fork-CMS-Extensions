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
                <label for="thumbnailResolution">{$lblResolution|ucfirst}</label>
                {$ddmThumbnailResolution} {$ddmThumbnailResolutionError}
            </p>
        </div>
	
		<div class="options labelWidthLong hidden">
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
                <label for="largeResolution">{$lblResolution|ucfirst}</label>
                {$ddmLargeResolution} {$ddmLargeResolutionError}
            </p>
        </div>

		<div class="options labelWidthLong hidden">
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

	{include:{$BACKEND_MODULES_PATH}/photogallery/layout/templates/Partials/FormContentLightboxSettings.tpl}