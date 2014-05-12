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
class BackendPhotogalleryAddImagesUploadZip extends BackendBaseActionAdd
{

	private $validImagedCount = 0;
	
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
		$this->frm->addFile('zip');
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		$this->tpl->assign('record', $this->record);
		$this->tpl->assign('zip_upload', extension_loaded('zip'));
	}

	/**
	 * Validate the image
	 *
	 * @param string $field The name of the field
	 * @param int $set_idThe id of the set
	 */
	private function processImage($path, $file, $set_id)
	{
		$info = SpoonFile::getInfo($path . '/' . $file);
		
		// Get languages where set is linked to
		$linkedAlbums = BackendPhotogalleryModel::getAlbumsLinkedToSet($this->set_id);

		$extension = $info['extension'];
		$original_filename = $info['name'];
		
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
			
			$thumb = new SpoonThumbnail($path . '/' . $file, $resolution['width'], $resolution['height']);
			$thumb->setAllowEnlargement($allowEnlargement);
			$thumb->setForceOriginalAspectRatio($forceOriginalAspectRatio);
			$thumb->parseToFile(
				$setsFilesPath . '/backend/' . $set_id . '/' . $resolution['width'] . 'x' . $resolution['height'] . $resolution['method'] . '/' . $filename,
				BackendPhotogalleryModel::IMAGE_QUALITY
			);
		}

		$image['id'] = BackendPhotogalleryModel::insertImage($image, $content, $metaData);

		// Do we need to resize the original image or not?
		if(BackendPhotogalleryModel::RESIZE_ORIGINAL_IMAGE)
		{
			$thumb = new SpoonThumbnail($path . '/' . $file, BackendPhotogalleryModel::MAX_ORIGINAL_IMAGE_WIDTH, BackendPhotogalleryModel::MAX_ORIGINAL_IMAGE_HEIGHT);
			$thumb->setAllowEnlargement(false);
			$thumb->parseToFile($setsFilesPath . '/original/' . $set_id . '/' . $filename);
		}
		else
		{
			// Move the original image
			SpoonFile::move($path . '/' . $file, $setsFilesPath . '/original/' . $set_id . '/' . $filename);
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
			
			if($this->frm->getField('zip')->isFilled())
			{
				$this->frm->getField('zip')->isAllowedExtension(array('zip'), BL::getError('ZipOnly'));
				//$this->frm->getField('zip')->isAllowedMimeType(array('application/x-zip', 'application/zip', 'application/x-zip-compressed'), BL::getError('ZipOnly'));
				$this->frm->getField('zip')->isFilesize(BackendPhotogalleryModel::MAX_ZIP_FILE_SIZE, 'mb', 'smaller', BL::getError('FileSizeIsToLarge'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				if($this->frm->getField("zip")->isFilled())
				{
					$fileName = SpoonFilter::urlise($this->frm->getField('zip')->getFileName(false));
					$fileName = $fileName . '_' . uniqid();
					$extension = $this->frm->getField('zip')->getExtension();
					$fileNameWithExtension = $fileName . '.' . $extension;
					
					// Move
					$this->frm->getField("zip")->moveFile(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/' . $fileNameWithExtension);
					
					// Create unzip folder
					SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/unzipped');
					
					// Unzip
					$zip = new ZipArchive();
					if($zip->open(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/' . $fileNameWithExtension))
					{
						// Unzip
						$zip->extractTo(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/unzipped');
						$zip->close();
						
						// Delete zip file
						SpoonFile::delete(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/' . $fileNameWithExtension);
						
						// Loop thru folder
						$files = SpoonDirectory::getList(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/unzipped', true, array('__MACOSX','.DS_Store','.svn','.git'));
						
						foreach($files as $file)
						{
							// Is valid extension
							if(SpoonThumbnail::isSupportedFileType(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/unzipped/' . $file))
							{
								$this->validImagedCount++;
							}
						}
					}
					
					// Are there images selected?
					if($this->validImagedCount > 0)
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
						
						// Process images
						$files = SpoonDirectory::getList(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/unzipped', true);
						
						foreach($files as $file)
						{
							$info = SpoonFile::getInfo(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/unzipped/' . $file);
							
							// Is valid extension
							if(SpoonThumbnail::isSupportedFileType(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/unzipped/' . $file))
							{
								self::processImage(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName . '/unzipped', $file, $this->set_id);
							}
						}
						
						// Delete folder
						SpoonDirectory::delete(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/zip/' . $fileName);
						
						// Update some statistics
						BackendPhotogalleryModel::updateSetStatistics($this->set_id);
						
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
	}
}