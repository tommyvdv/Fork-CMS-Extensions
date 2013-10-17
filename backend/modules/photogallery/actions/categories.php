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

		// get id
		$this->category_id = $this->getParameter('category_id', 'int', 0);
		$this->category = $this->category_id ? BackendPhotogalleryModel::getCategory($this->category_id) : array();

		// get data
		$this->getData();

		// load dataGrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		// get parent, parents parent, etc…
		$this->breadcrumbs = array_reverse(BackendPhotogalleryModel::getBreadcrumbsForCategory($this->category_id));
		$this->depth = count($this->breadcrumbs)-1 > 0 ? count($this->breadcrumbs)-1 : 0;
		
		$this->allowedForStart =
			$this->depth >= (int) BackendModel::getModuleSetting('photogallery', 'categories_depth_start');
		$this->allowedForLimit =
			$this->depth <= (int) BackendModel::getModuleSetting('photogallery', 'categories_depth') ||
			!is_null(BackendModel::getModuleSetting('photogallery', 'categories_depth'));
		$this->allowChildSubCategoryCreation = (bool) $this->allowedForStart && (bool) $this->allowedForLimit;

		$this->allowedForStart =
			$this->depth > (int) BackendModel::getModuleSetting('photogallery', 'categories_depth_start');
		$this->allowedForLimit =
			$this->depth < (int) BackendModel::getModuleSetting('photogallery', 'categories_depth') ||
			!is_null(BackendModel::getModuleSetting('photogallery', 'categories_depth'));
		$this->allowChildCategoryCreation = (bool) $this->allowedForStart && (bool) $this->allowedForLimit;;
	}

	/**
	 * Loads the dataGrids
	 */
	private function loadDataGrid()
	{
		// create dataGrid
		//$this->dataGrid = new BackendDataGridDB(BackendPhotogalleryModel::QRY_DATAGRID_BROWSE_CATEGORIES, BL::getWorkingLanguage());

		$column_sequence = array(
			'dragAndDropHandle',
			'title',
			'num_children',
			'num_albums',
			'edit'
		);

		// create dataGrid
		$this->dataGrid = new BackendDataGridDB(
			BackendPhotogalleryModel::QRY_DATAGRID_BROWSE_CATEGORIES,
			array(
				BL::getWorkingLanguage(),
				$this->category_id
			)
		);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('title'), 'sequence');
		
		$this->dataGrid->enableSequenceByDragAndDrop();

		// add column
		//$this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_category') . '&amp;id=[id]', BL::getLabel('Edit'));
		$this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_category') . '&amp;id=[id]' . ($this->category_id ? '&amp;category_id=' . $this->category_id : ''), BL::getLabel('Edit'));

		$this->dataGrid->setColumnsHidden(array('sequence'));

		// add subcategories button (if depth is not greater than…)
		if(
			!is_null(BackendModel::getModuleSetting('photogallery', 'categories_depth')) &&
			(
				count($this->breadcrumbs) <= BackendModel::getModuleSetting('photogallery', 'categories_depth', 0) ||
				(int) BackendModel::getModuleSetting('photogallery', 'categories_depth') === 0
			)
		)
		{
			// build sequence
			$column_sequence = array(
				'dragAndDropHandle',
				'title',
				'num_children',
				'num_albums',
				'children'
			);

			// add children column
			$this->dataGrid->addColumn('children', null);
			$this->dataGrid->setColumnFunction(create_function('$num_children,$id','return $num_children = $num_children ? "<a href=\"" . BackendModel::createURLForAction("categories") . "&amp;category_id=" . $id . "\">" . BL::lbl("ViewSubcategories") . "</a>" : BL::lbl("NoSubcategories");'),array('[num_children]', '[id]'),'children',true);
			
			// add add button
			if($this->allowChildSubCategoryCreation)
			{
				$this->dataGrid->addColumn('add_subcategory', null, sprintf(BL::lbl('AddSubCategory')), BackendModel::createURLForAction('add_category') . '&amp;category_id=[id]', BL::getLabel('msgCategoriesForParent'));
				$column_sequence[] = 'add_subcategory';
			}
			
			// add edit to sequence
			$column_sequence[] = 'edit';
		}

		// disable paging
		$this->dataGrid->setPaging(false);      
		
		$this->dataGrid->setAttributes(array('data-action' => "category_sequence"));

		// column sequence
		$this->dataGrid->setColumnsSequence($column_sequence);
	}

	/**
	 * Parse & display the page
	 */
	protected function parse()
	{
		// assign
		$this->tpl->assign('addToParentURL', BackendModel::createURLForAction('add_category', 'photogallery', BL::getWorkingLanguage(), isset($this->category['id']) ? array('category_id' => $this->category['id']) : array()));
		$this->tpl->assign('category', $this->category);
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
		$this->tpl->assign('breadcrumbs', $this->breadcrumbs);
		
		$this->tpl->assign('depth', $this->depth);
		$this->tpl->assign('allowed_depth_start', BackendModel::getModuleSetting('photogallery', 'categories_depth_start'));
		$this->tpl->assign('allowed_depth', BackendModel::getModuleSetting('photogallery', 'categories_depth', 'null'));
		$this->tpl->assign('allowedForStart', $this->allowedForStart);
		$this->tpl->assign('allowedForLimit', $this->allowedForLimit);
		
		$this->tpl->assign('allowChildSubCategoryCreation', $this->allowChildSubCategoryCreation);
		$this->tpl->assign('allowChildCategoryCreation', $this->allowChildCategoryCreation);
	}

}