<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This add images upload action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryAddImagesUploadMultiple extends BackendBaseActionAdd
{

	private $filledImagedCount = 0;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('album_id', 'int');

		// does the item exists
		if($this->id !== null && BackendPhotogalleryModel::existsAlbum($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the form
			$this->loadForm();

			// parse
			$this->parse();

			// display the page
			$this->display();
		}
		// no item found, throw an exception, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendPhotogalleryModel::getAlbum($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		$this->header->addJS('jquery.uploadifive.js');
		$this->header->addJS('uploadifive-init.js');
		$this->header->addCSS('uploadifive.css');
		
		$timestamp = time();
		$this->tpl->assign('timestamp', $timestamp);
		$this->tpl->assign('token', md5($timestamp));

		$this->set_id = $this->record['set_id'];

		if($this->set_id !== null && !BackendPhotogalleryModel::existsSet($this->set_id))
		{
			// Reset set_id of it the set doesn't exists anymore
			BackendPhotogalleryModel::updateAlbum(array('id' => $this->id, 'set_id' => null));

			$this->redirect(BackendModel::createURLForAction('add_images_choose') . '&album_id' . $this->id);
		}
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');
		
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		$this->tpl->assign('record', $this->record);
	}

}