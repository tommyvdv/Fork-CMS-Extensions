{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblAmazonS3|ucfirst}</h2>
</div>

{option:!bucket}
	<div class="generalMessage infoMessage content">
		<p><strong>{$msgConfigurationError}</strong></p>
		<ul class="pb0">
			{option:!account}<li>{$errNoAmazonS3Account}</li>{/option:!account}
			{option:account}<li>{$errNoBucket}</li>{/option:account}
		</ul>
	</div>
{/option:!bucket}

<div class="tabs">
	<ul>
		<li><a href="#tabSettingsAccount">{$lblAmazonS3|ucfirst} - {$lblAccount|ucfirst}</a></li>
		{option:account}<li><a href="#tabSettingsBucket">{$lblAmazonS3|ucfirst} - {$lblBucket|ucfirst}</a></li>{/option:account}
	</ul>

	

	<div id="tabSettingsAccount">
		{form:settingsAccount}
		<div class="box horizontal" id="accountBox">
			<div class="heading">
				<h3>{$lblAmazonS3|ucfirst} - {$lblAccount|ucfirst}</h3>
			</div>
			<div class="options">
			
				<p>
					<label for="awsAccessKey">{$lblAwsAccesKey|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtAwsAccessKey} {$txtAwsAccessKeyError}
				</p>
				<p>
					<label for="awsSecretKey">{$lblAwsSecretKey|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtAwsSecretKey} {$txtAwsSecretKeyError}
				</p>
				<div class="buttonHolder">
					{option:!account}<a id="linkAccount" href="#" class="askConfirmation button inputButton"><span>{$msgLinkAmazonS3Account}</span></a>{/option:!account}
					{option:account}
					<a id="unlinkAccount" href="#" class="askConfirmation submitButton button inputButton"><span>{$msgUnlinkAmazonS3Account}</span></a>
					{/option:account}
				</div>
			</div>
		</div>
		{/form:settingsAccount}
	</div>

	{option:account}
	<div id="tabSettingsBucket">
		{form:settingsBucket}
		<div class="box horizontal">
			<div class="heading">
				<h3>{$lblAmazonS3|ucfirst} - {$lblBucket|ucfirst}</h3>
			</div>
			<div class="options id">	
				<p>
					<label for="buckets">{$lblBuckets|ucfirst}</label>
					{$ddmBuckets}
				</p>
				{option:!bucket}<p class="formError"><strong>{$msgNoBucket}</strong></p>{/option:!bucket}
			</div>
			
			<div class="options generate">
				
				<p>
					<label for="regions">{$lblRegion|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$ddmRegions} {$ddmRegionsError}
				</p>
				
				<p>
					<label for="buckets">{$lblBucket|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtBucket} {$txtBucketError}
				</p>
			
			</div>

			<div class="fullwidthOptions">
				<div class="buttonHolderRight">
					<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
				</div>
			</div>
		</div>
		{/form:settingsBucket}
	</div>
	{/option:account}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}