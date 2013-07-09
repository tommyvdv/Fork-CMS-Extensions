<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This extras overiew action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryExtras extends BackendBaseActionIndex
{

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load dataGrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Load the dataGrid
	 */
	private function loadDataGrid()
	{
		// create dataGrid
		$this->dataGrid = new BackendDataGridDB(BackendPhotogalleryModel::QRY_DATAGRID_BROWSE_EXTRAS);
		
		// sorting columns
		$this->dataGrid->setSortingColumns(array('action', 'kind'), 'kind');
	
		// add columns
		$this->dataGrid->addColumn('resolutions', SpoonFilter::ucfirst(BL::getLabel('Resolutions')));

		// functions
		$this->dataGrid->setColumnFunction(array('BackendPhotogalleryHelper', 'getResolutionsForDataGrid'), array('[id]'), 'resolutions', true);
		
		$this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::getLabel('Edit'));

		$this->dataGrid->setColumnFunction(array('BackendPhotogalleryHelper', 'getExtraEditURLForKind'), array('[id]','[kind]', '[action]'), 'edit', true);

		// set colum URLs
		$this->dataGrid->setColumnFunction(create_function('$action','return $action == null ? "" : BackendTemplateModifiers::toLabel($action);'), array('[action]'), 'action', true);
		
		$this->dataGrid->setColumnFunction(array('BackendTemplateModifiers', 'toLabel'), array('[kind]'), 'kind', true);
		$this->dataGrid->setColumnURL('action', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
		
	
		
	
	}

	/**
	 * Parse & display the page
	 */
	protected function parse()
	{
		// parse dataGrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// parse paging & sorting
		$this->tpl->assign('offset', (int) $this->dataGrid->getOffset());
		$this->tpl->assign('order', (string) $this->dataGrid->getOrder());
		$this->tpl->assign('sort', (string) $this->dataGrid->getSort());
	}
}