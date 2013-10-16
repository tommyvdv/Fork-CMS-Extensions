<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This edit category action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryEditCategory extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// get id
		$this->category_id = $this->getParameter('category_id', 'int', 0);

		// does the item exists
		if($this->id !== null && BackendPhotogalleryModel::existsCategory($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the dataGrid
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		$this->record = BackendPhotogalleryModel::getCategory($this->id);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('editCategory');

		// get categories
		/*
		$this->categories = BackendPhotogalleryModel::getCategoriesForDropdown(
			BackendModel::getModuleSetting('photogallery', 'categories_depth'),
			false
		);
		*/
		$allowedDepth = BackendModel::getModuleSetting('photogallery', 'categories_depth', 0);
		$allowedDepthStart = BackendModel::getModuleSetting('photogallery', 'categories_depth_start', 0);
		$this->categoriesCount = BackendPhotogalleryModel::getCategoriesCount();
		$this->categories = BackendPhotogalleryModel::getCategoriesForDropdown(
			array(
				$allowedDepthStart,
				$allowedDepth == 0 ? 0 : $allowedDepth
			)
		);

		// create elements
		$this->frm->addText('title', $this->record['title']);
		$this->frm->addDropdown('parent_id', $this->categories, $this->record['parent_id'])->setDefaultElement('');
		$this->tpl->assign('deleteAllowed', BackendPhotogalleryModel::deleteCategoryAllowed($this->id));

		// meta object
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);

		// set callback for generating an unique URL
		$this->meta->setUrlCallback('BackendPhotogalleryModel', 'getURLForCategory', array($this->record['id']));
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign
		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('category', $this->record);
		$this->tpl->assign('categories', $this->categories);
		$this->tpl->assign('categories_depth', is_null(BackendModel::getModuleSetting('photogallery', 'categories_depth')) ? false : true);
		$this->tpl->assign('categoriesCount', $this->categoriesCount);

		// delete allowed?
		$this->tpl->assign('deleteAllowed', BackendPhotogalleryModel::deleteCategoryAllowed($this->id));
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));

			// parented to self?
			if($this->frm->getField('parent_id')->getValue() == $this->record['id'])
			{
				$this->frm->getField('parent_id')->addError(BL::getError('CanNotParentToSelf'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['meta_id'] = $this->meta->save(true);
				$item['parent_id'] = $this->frm->getField('parent_id')->getValue();

				// upate the item
				BackendPhotogalleryModel::updateCategory($item);

				// everything is saved, so redirect to the overview
				//$this->redirect(BackendModel::createURLForAction('categories') . '&report=edited-category&var=' . urlencode($item['title']));
				$this->redirect(BackendModel::createURLForAction('categories') . ($this->category_id ? '&category_id=' . $this->category_id : '') . '&highlight=row-' . $this->id . '&report=edited-category&var=' . urlencode($item['title']));
			}
		}
	}

}