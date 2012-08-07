<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This delete category action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryDeleteCategory extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendPhotogalleryModel::existsCategory($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->record = (array) BackendPhotogalleryModel::getCategory($this->id);

			// delete item
			BackendPhotogalleryModel::deleteCategory($this->id);

			// deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('categories') . '&report=deleted-category&var=' . urlencode($this->record['title']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('categories') . '&error=non-existing');
	}
}
