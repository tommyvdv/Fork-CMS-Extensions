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
class BackendPhotogalleryAddImagesUpload extends BackendBaseActionAdd
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
	 * Get the data
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendPhotogalleryModel::getAlbum($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

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

		for($i = 0; $i< BackendPhotogalleryModel::MAX_IMAGES_UPLOAD; $i++)
		{
			$this->formImageFields[]['formElements']['fileImage'] = $this->frm->addImage('image' . $i);
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
		$this->tpl->assign('imageFields', $this->formImageFields);
		$this->tpl->assign('zip_upload', extension_loaded('zip'));
	}

	/**
	 * Validate the image
	 *
	 * @param string $field The name of the field
	 * @param int $set_idThe id of the set
	 */
	private function uploadImage($field, $set_id)
	{
		// image provided
		if($this->frm->getField($field)->isFilled())
		{
			// Get languages where set is linked to
			$linkedAlbums = BackendPhotogalleryModel::getAlbumsLinkedToSet($this->set_id);

			$extension = $this->frm->getField($field)->getExtension();
			$original_filename = $this->frm->getField($field)->getFileName(false);
			
			// Generate a unique filename
			$filename = BackendPhotogalleryModel::getFilenameForImage(time() . '_' . $original_filename, $extension) . '.' . $extension;

			$image['filename'] = $filename;
			$image['set_id'] = $set_id;
			$image['original_filename'] = $original_filename;
			$image['hidden'] = 'N';
			$image['created_on'] = BackendModel::getUTCDate();
			$image['edited_on'] = BackendModel::getUTCDate();
			$image['sequence'] = BackendPhotogalleryModel::getSetImageSequence($set_id) + 1;

			$content = array();
			$metaData = array();

			foreach($linkedAlbums as &$linkedAlbum)
			{
				// Meta
				$meta['keywords'] = $original_filename;
				$meta['keywords_overwrite'] = 'N';
				$meta['description'] = $original_filename;
				$meta['description_overwrite'] = 'N';
				$meta['title'] = $original_filename;
				$meta['title_overwrite'] = 'N';
				$meta['url'] = BackendPhotogalleryModel::getURLForImage($original_filename, $linkedAlbum['language']);

				// add
				$metaData[$linkedAlbum['language']] = $meta;

				// build record
				$temp = array();
				$temp['title'] = $original_filename;
				$temp['album_id'] = $linkedAlbum['id'];
				$temp['language'] = $linkedAlbum['language'];
				$temp['set_id'] = $set_id;
				$temp['created_on'] = BackendModel::getUTCDate();
				$temp['edited_on'] = BackendModel::getUTCDate();

				// add
				$content[$linkedAlbum['language']] = $temp;
			}

			// Path to the sets folder
			$setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';

			// Backend resolutions
			foreach(BackendPhotogalleryModel::$backendResolutions as $resolution)
			{
				$forceOriginalAspectRatio = $resolution['method'] == 'crop' ? false : true;
				$allowEnlargement = true;

				$this->frm->getField($field)->createThumbnail(
					$setsFilesPath . '/backend/' . $set_id . '/' . $resolution['width'] . 'x' . $resolution['height'] . $resolution['method'] . '/' . $filename,
					$resolution['width'], $resolution['height'],
					$allowEnlargement, $forceOriginalAspectRatio, BackendPhotogalleryModel::IMAGE_QUALITY
				);
			}

			$image['id'] = BackendPhotogalleryModel::insertImage($image, $content, $metaData);

			// Do we need to resize the original image or not?
			if(BackendPhotogalleryModel::RESIZE_ORIGINAL_IMAGE)
			{
				// Original, but resize if larger then MAX_ORIGINAL_IMAGE_WIDTH OR MAX_ORIGINAL_IMAGE_HEIGHT
				$this->frm->getField($field)->createThumbnail(
					$setsFilesPath . '/original/' . $set_id . '/' . $filename,
					BackendPhotogalleryModel::MAX_ORIGINAL_IMAGE_WIDTH, BackendPhotogalleryModel::MAX_ORIGINAL_IMAGE_HEIGHT,
					false
				);
			}
			else
			{
				// Move the original image
				$this->frm->getField($field)->moveFile($setsFilesPath . '/original/' . $set_id . '/' . $filename);
			}
			
		}
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

			$this->hasImages = false;

			// Validate images
			for($i = 0; $i < BackendPhotogalleryModel::MAX_IMAGES_UPLOAD; $i++) self::validateImage('image' . $i);

			// no errors?
			if($this->frm->isCorrect())
			{
				// Are there images selected?
				if($this->filledImagedCount > 0)
				{
					// There is no set linked
					if($this->set_id === null)
					{
						// Create a set based on the album name
						$item['title'] = $this->record['title'];
						$item['language'] = BL::getWorkingLanguage();
						$item['created_on'] = BackendModel::getUTCDate();
						$item['edited_on'] = BackendModel::getUTCDate();

						// Create set AND set the set_id
						$this->set_id = BackendPhotogalleryModel::insertSet($item);

						// Link set to album
						BackendPhotogalleryModel::updateAlbum(array('id' => $this->id, 'set_id' => $this->set_id));
					}
					
					ini_set('memory_limit', -1);
					
					// upload images
					for($i = 0; $i < BackendPhotogalleryModel::MAX_IMAGES_UPLOAD; $i++) self::uploadImage('image' . $i,  $this->set_id);
					
					// Update some statistics
					BackendPhotogalleryModel::updateSetStatistics($this->set_id);
					
					ini_restore('memory_limit');
					
					// everything is saved, so redirect to the overview
				  	$this->redirect(BackendModel::createURLForAction('edit') . '&report=added-images&id=' . $this->id . '#tabImages');
				}
				else
				{
					$this->redirect(BackendModel::createURLForAction('edit') . '&report=no-images-selected&id=' . $this->id . '#tabImages');
				}
			}
		}
	}

	/**
	 * Validate the image
	 *
	 * @param string $field The name of the field
	 */
	private function validateImage($field)
	{
		if($this->frm->getField($field)->isFilled())
		{
			// correct extension
			if($this->frm->getField($field)->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), BL::getError('JPGGIFAndPNGOnly')))
			{
				// correct mimetype?
				if($this->frm->getField($field)->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), BL::getError('JPGGIFAndPNGOnly')))
				{
					// image is not larger
					if($this->frm->getField($field)->isFilesize(BackendPhotogalleryModel::MAX_ORIGINAL_FILE_SIZE, 'mb', 'smaller', BL::getError('FileSizeIsTooLarge')))
					{
						$this->filledImagedCount++;
					}
				}
			}
		}
	}

}