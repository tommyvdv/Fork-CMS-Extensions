<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This categories overview action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryCategories extends BackendBaseActionIndex
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
	 * Loads the dataGrids
	 */
	private function loadDataGrid()
	{
		// create dataGrid
		$this->dataGrid = new BackendDataGridDB(BackendPhotogalleryModel::QRY_DATAGRID_BROWSE_CATEGORIES, BL::getWorkingLanguage());

		// sorting columns
		$this->dataGrid->setSortingColumns(array('title'), 'sequence');
		
		$this->dataGrid->enableSequenceByDragAndDrop();

		// add column
		$this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_category') . '&amp;id=[id]', BL::getLabel('Edit'));
		
		$this->dataGrid->setColumnsHidden(array('sequence'));

		// disable paging
		$this->dataGrid->setPaging(false);
		
		
		$this->dataGrid->setAttributes(array('data-action' => "category_sequence"));
	}

	/**
	 * Parse & display the page
	 */
	protected function parse()
	{
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}

}
