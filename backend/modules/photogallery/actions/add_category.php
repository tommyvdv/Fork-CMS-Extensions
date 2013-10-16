<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This add category action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryAddCategory extends BackendBaseActionAdd
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
		$this->category = BackendPhotogalleryModel::getCategory($this->category_id);

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the dataGrid
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign category (if there is one)
		$this->tpl->assign('category', $this->category);
		$this->tpl->assign('categories', $this->categories);
		$this->tpl->assign('categories_depth', is_null(BackendModel::getModuleSetting('photogallery', 'categories_depth')) ? false : true);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('addCategory');

		// get categories
		$this->categories = BackendPhotogalleryModel::getCategoriesForDropdown(
			BackendModel::getModuleSetting('photogallery', 'categories_depth')
		);

		// create elements
		$this->frm->addText('title', null, 255);
		$this->frm->addDropdown('parent_id', $this->categories, SpoonFilter::getGetValue('category_id', null, null, 'int'))->setDefaultElement('');

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);
		
		$this->meta->setURLCallback('BackendPhotogalleryModel', 'getURLForCategory');
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

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['meta_id'] = $this->meta->save();
				$item['sequence'] = (int) BackendPhotogalleryModel::getSequenceCategory() + 1;
				$item['parent_id'] = (int) $this->frm->getField('parent_id')->getValue();

				// insert the item
				$item['id'] = BackendPhotogalleryModel::insertCategory($item);

				// everything is saved, so redirect to the overview
				$this->redirect(
					BackendModel::createURLForAction(
						'categories',
						'photogallery',
						BL::getWorkingLanguage()
					) . '&report=added-category' .
					'&var=' . urlencode($item['title']) .
					'&highlight=row-' . $item['id'] .
					'&category_id=' . $item['parent_id']
				);
			}
		}
	}
}