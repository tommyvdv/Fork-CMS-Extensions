<?php

/**
 * BackendModulemanagerActions
 * This is the actions-action (default), it will display the actions of a module
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerDetailModule extends BackendBaseActionIndex
{

	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();
		
		// load record
		$this->loadData();
		
		// load datagrid
		$this->loadDatagrid();
		
		// load datagrid
		$this->installerPhp();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the record
	 *
	 * @return	void
	 */
	private function loadData()
	{
		// get record
		$this->module = $this->getParameter('module', 'string');

		// validate id
		if($this->module === null || !BackendModulemanagerModel::exists($this->module)) $this->redirect(BackendModel::createURLForAction('modules') . '&error=non-existing');

		// get the record
		$this->record = BackendModulemanagerModel::get($this->module);
		$this->actionFiles = BackendModulemanagerModel::getModuleActionsFromFiles($this->module);
		$this->missing_actions = BackendModulemanagerModel::getMissingActions($this->module);
	}


	/**
	 * Load the datagrids
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendModulemanagerModel::QRY_DATAGRID_BROWSE_ACTIONS, $this->module);
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_action') . '&amp;id=[id]', BL::getLabel('Edit'));
		$this->datagrid->setPaging(false);

	
		// create datagrid
		$this->datagridMissingActions = new BackendDataGridArray($this->missing_actions);
		$this->datagridMissingActions->addColumn('add', null, BL::getLabel('Add'), BackendModel::createURLForAction('add_action') . '&amp;action=[action]&amp;module=' . $this->module, BL::getLabel('Add'));
		$this->datagridMissingActions->setColumnHidden('file');
		$this->datagridMissingActions->setColumnsSequence(array('action','path','add'));
		$this->datagridMissingActions->setPaging(false);
	}
	
	private function installerPhp()
	{
		$installerTpl = new SpoonTemplate();
		
		$installerTpl->setCacheDirectory(BACKEND_CACHE_PATH . '/compiled_templates');
		$installerTpl->setCompileDirectory(BACKEND_CACHE_PATH . '/compiled_templates');
		$installerTpl->setForceCompile(true);
		$installerTpl->assign('item', $this->record);
		$installerTpl->assign('actions', $this->actionFiles);
		$this->installer = $installerTpl->getContent(BACKEND_MODULES_PATH . '/' . $this->URL->getModule() .'/layout/templates/installer.tpl');
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		
		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
		$this->tpl->assign('datagridMissingActions', ($this->datagridMissingActions->getNumResults() != 0) ? $this->datagridMissingActions->getContent() : false);
		$this->tpl->assign('installer', $this->installer);
		
	}
}

?>