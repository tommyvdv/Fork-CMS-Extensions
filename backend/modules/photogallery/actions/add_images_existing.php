<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This add images existing action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryAddImagesExisting extends BackendBaseActionAdd
{
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

			// validate the form
			$this->validateForm();

			// parse
			$this->parse();

			// display the page
			$this->display();
		}
		// no item found, throw an exception, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		$this->frm->addDropdown('sets', $this->sets)->setDefaultElement(SpoonFilter::ucfirst(BL::getLabel('ChooseAnExistingAlbum')));
	}

	/**
	 * Get the data for a question
	 */
	private function getData()
	{
		// get the record
		$this->record = BackendPhotogalleryModel::getAlbum($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing-album');

		$this->sets = BackendPhotogalleryModel::getSetsForDropdown();

		if(empty($this->sets)) $this->redirect(BackendModel::createURLForAction('add_images_upload') . '&album_id=' . $this->id);

		// If set_id is not null and set doesn't exists
		if($this->record['set_id'] !== null && !BackendPhotogalleryModel::existsSet($this->record['set_id']))
		{
			// Reset set_id of it the set doesn't exists anymore
			BackendPhotogalleryModel::updateAlbum(array('id' => $this->id, 'set_id' => null));

			$this->redirect(BackendModel::createURLForAction('add_images_choose') . '&album_id=' . $this->id);
		}
		// If set_id is not null and set exists
		elseif($this->record['set_id'] !== null && BackendPhotogalleryModel::existsSet($this->record['set_id']))
		{
			$this->redirect(BackendModel::createURLForAction('add_images_upload') . '&album_id=' . $this->id);
		}
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

			// Validate
			$this->frm->getField('sets')->isFilled(BL::getError('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				$set_id = $this->frm->getField('sets')->getValue();

				// Does the set still exists?
				if(BackendPhotogalleryModel::existsSet($set_id))
				{
					// Link
					BackendPhotogalleryModel::updateAlbum(array('id' => $this->id, 'set_id' => $set_id));

					$images = BackendPhotogalleryModel::getSetImages($set_id);
					$content = array();
					$metaData = array();

					foreach($images as $image)
					{
						// Meta
						$meta['keywords'] = $image['original_filename'];
						$meta['keywords_overwrite'] = 'N';
						$meta['description'] = $image['original_filename'];
						$meta['description_overwrite'] = 'N';
						$meta['title'] = $image['original_filename'];
						$meta['title_overwrite'] = 'N';
						$meta['url'] = BackendPhotogalleryModel::getURLForImage($image['original_filename'], BL::getWorkingLanguage());

						// add
						$metaData[] = $meta;

						// build record
						$temp = array();
						$temp['title'] = $image['original_filename'];
						$temp['album_id'] = $this->id;
						$temp['set_image_id'] = $image['id'];
						$temp['set_id'] = $set_id;
						$temp['language'] = BL::getWorkingLanguage();
						$temp['created_on'] = BackendModel::getUTCDate();
						$temp['edited_on'] = BackendModel::getUTCDate();

						/*
						$imageContent = BackendPhotogalleryModel::getImageWithContent($image['id'], $this->id);

						$temp['title_hidden'] = $imageContent['title_hidden'] ? 'Y' : 'N';
						$temp['text'] = $imageContent['text']; 

						$temp['data'] = serialize($imageContent['data']);
						$temp['title'] = $imageContent['title'];
						*/

						
						// add
						$content[] = $temp;
					}

					BackendPhotogalleryModel::insertImagesContentForExisting($content, $metaData);

					// Update some statistics
					BackendPhotogalleryModel::updateSetStatistics($set_id);

					$this->redirect(BackendModel::createURLForAction('edit') . '&report=added-images&id=' . $this->id);
				}
				else
				{
					$this->redirect(BackendModel::createURLForAction('edit') . '&error=non-existing&id=' . $this->id);
				}
			}
		}
	}

}