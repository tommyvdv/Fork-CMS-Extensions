{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}
<div class="pageTitle">
	<h2>{$lblDetailsForModule|sprintf:{$item.name}|ucfirst}</h2>
	<div class="buttonHolderRight">
		<a class="button icon iconBack" href="{$var|geturl:'modules'}"><span>{$lblBack}</span></a>
	</div>
</div>


<div class="tabs">
	<ul>
		<li><a href="#tabActions">{$lblActions|ucfirst}</a></li>
		<li><a href="#tabMissingActions">{$lblMissingActions|ucfirst}</a></li>
		<li><a href="#tabInstaller">{$lblInstaller|ucfirst}</a></li>
	</ul>
	
	<div id="tabActions">
		{option:datagrid}
			<div class="dataGridHolder">
				{$datagrid}
			</div>
		{/option:datagrid}

		{option:!datagrid}
			<p class="p0">{$msgNoActions|sprintf:{$var|geturl:'add_action'}}&amp;module={$item.name}</p>
		{/option:!datagrid}
	</div>
	
	<div id="tabMissingActions">
		{option:datagridMissingActions}
			<div class="dataGridHolder">
				{$datagridMissingActions}
			</div>	
		{/option:datagridMissingActions}
		
		{option:!datagridMissingActions}
			<p class="p0">{$msgNoMissingActions}</p>
		{/option:!datagridMissingActions}
	</div>
	
	<div id="tabInstaller">
		<textarea style="width:100%; height:1200px">{$installer}</textarea>
	</div>
	
</div>
	

<div class="fullwidthOptions">
	<a href="{$var|geturl:'delete'}&amp;module={$item.name}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
		<span>{$lblDeleteModule|ucfirst}</span>
	</a>
</div>

<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
	<p>
		{$msgConfirmDelete|sprintf:{$item.name}}
	</p>
</div>



{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
