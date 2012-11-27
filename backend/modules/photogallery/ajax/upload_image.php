<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* Re-order the albums
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */

class BackendPhotogalleryAjaxUploadImage extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		$verifyToken =  md5(SpoonFilter::getPostValue('timestamp', null, '', 'string'));
		$token = SpoonFilter::getPostValue('token', null, '', 'string');
		$this->id = SpoonFilter::getPostValue('album_id', null, '', 'int');


		if(!empty($_FILES))
		{
			if ($token == $verifyToken)
			{	
				ini_set('memory_limit', -1);

				$this->record = (array) BackendPhotogalleryModel::getAlbum($this->id);

				if(empty($this->record)) $this->output(self::ERROR, null, 'album not found');

				$this->set_id = $this->record['set_id'];

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

				// Upload
				self::uploadImage($_FILES, $this->set_id);

				// Update some statistics
				BackendPhotogalleryModel::updateSetStatistics($this->set_id);

				ini_restore('memory_limit');

				$this->output(self::OK, null, '1');
			}
			else
			{
				$this->output(self::ERROR, null, 'invalid token');
			}
		}
		else
		{
			$this->output(self::ERROR, null, 'no files selected');
		}
	}


	private function isImage($tempFile) {

		// Get the size of the image
	    $size = getimagesize($tempFile);

		if (isset($size) && $size[0] && $size[1] && $size[0] *  $size[1] > 0) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Validate the image
	 *
	 * @param string $field The name of the field
	 * @param int $set_idThe id of the set
	 */
	private function uploadImage($file, $set_id)
	{
		// image provided
		$fileData = $file['Filedata'];

		if($fileData)
		{
			$fileParts = pathinfo($fileData['name']);
			$tempFile   = $fileData['tmp_name'];

			$extension = $fileParts['extension'];
			$original_filename = $fileParts['filename'];

			$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions


			if (in_array(strtolower($fileParts['extension']), $fileTypes) && filesize($tempFile) > 0 && self::isImage($tempFile))
			{

				// Get languages where set is linked to
				$linkedAlbums = BackendPhotogalleryModel::getAlbumsLinkedToSet($this->set_id);

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
				$setsFilesPath = FRONTEND_FILES_PATH . '/photogallery/sets';

				// Backend resolutions
				foreach(BackendPhotogalleryModel::$backendResolutions as $resolution)
				{
					$forceOriginalAspectRatio = $resolution['method'] == 'crop' ? false : true;
					$allowEnlargement = true;


					$thumbnail = new SpoonThumbnail($tempFile , $resolution['width'], $resolution['height'], true);
					$thumbnail->setAllowEnlargement($allowEnlargement);
					$thumbnail->setForceOriginalAspectRatio($forceOriginalAspectRatio);
					$thumbnail->parseToFile($setsFilesPath . '/backend/' . $set_id . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'] . '/' . $filename, BackendPhotogalleryModel::IMAGE_QUALITY);
				}

				$image['id'] = BackendPhotogalleryModel::insertImage($image, $content, $metaData);


				if(BackendPhotogalleryModel::KEEP_ORIGINAL_IMAGE)
				{
					// Do we need to resize the original image or not?
					if(BackendPhotogalleryModel::RESIZE_ORIGINAL_IMAGE)
					{

						// Original, but resize if larger then MAX_ORIGINAL_IMAGE_WIDTH OR MAX_ORIGINAL_IMAGE_HEIGHT
						
						$thumbnail = new SpoonThumbnail($tempFile , BackendPhotogalleryModel::MAX_ORIGINAL_IMAGE_WIDTH, BackendPhotogalleryModel::MAX_ORIGINAL_IMAGE_HEIGHT, true);
						$thumbnail->setAllowEnlargement(false);
						$thumbnail->setForceOriginalAspectRatio(true);
						$thumbnail->parseToFile($setsFilesPath . '/original/' . $set_id . '/' . $filename, 100);
					}
					else
					{
						// Move the original image
						move_uploaded_file($tempFile, $setsFilesPath . '/original/' . $set_id . '/' . $filename);
					}


					// Put original
					$cronjob = array();
					$cronjob['module'] = 'photogallery';
					$cronjob['path'] = 'photogallery/sets/original/' . $set_id;
					$cronjob['filename'] = $filename;
					$cronjob['full_path'] = $cronjob['path'] . '/' . $cronjob['filename'];
					$cronjob['data'] = serialize(array('set_id' => $set_id, 'image_id' => $image['id'], 'delete_local' => true, 'delete_local_in_time' => BackendPhotogalleryModel::DELETE_LOCAL_IN_TIME));
					$cronjob['action'] = 'put';
					$cronjob['location'] = 's3';
					$cronjob['created_on'] =  BackendModel::getUTCDate();
					$cronjob['execute_on'] = BackendModel::getUTCDate();
					if(BackendPhotogalleryHelper::existsAmazonS3()) BackendAmazonS3Model::insertCronjob($cronjob);
				}
				
				
				
				$resolutions = BackendPhotogalleryModel::getUniqueExtrasResolutions();
				
				if(BackendPhotogalleryHelper::existsAmazonS3())
				{
					foreach($resolutions as $resolution)
					{
						// Delete cronjobs with same path and filename
						BackendAmazonS3Model::deleteCronjobByFullPath('photogallery', 'photogallery/sets/frontend/' . $set_id . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'] . '/' . $filename);
					}	
				}
				
				
				foreach($resolutions as $resolution)
				{
					$forceOriginalAspectRatio = $resolution['method'] == 'crop' ? false : true;
					$allowEnlargement = true;

					$thumbnail = new SpoonThumbnail($tempFile , $resolution['width'], $resolution['height'], true);
					$thumbnail->setAllowEnlargement($allowEnlargement);
					$thumbnail->setForceOriginalAspectRatio($forceOriginalAspectRatio);
					$thumbnail->parseToFile($setsFilesPath . '/frontend/' . $set_id  . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'] . '/' . $filename, BackendPhotogalleryModel::IMAGE_QUALITY);
					
					// Put
					$cronjob = array();
					$cronjob['module'] = 'photogallery';
					$cronjob['path'] = 'photogallery/sets/frontend/' . $set_id . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'];
					$cronjob['filename'] = $filename;
					$cronjob['full_path'] = $cronjob['path'] . '/' . $cronjob['filename'];
					$cronjob['data'] = serialize(array('set_id' => $set_id, 'image_id' => $image['id'], 'delete_local' => true, 'delete_local_in_time' => BackendPhotogalleryModel::DELETE_LOCAL_IN_TIME));
					$cronjob['action'] = 'put';
					$cronjob['location'] = 's3';
					$cronjob['created_on'] =  BackendModel::getUTCDate();
					$cronjob['execute_on'] = BackendModel::getUTCDate();
					if(BackendPhotogalleryHelper::existsAmazonS3()) BackendAmazonS3Model::insertCronjob($cronjob);
				}
			}
			else
			{
				$this->output(self::OK, null, 'Invalid file type.');
			}
		}
	}
}
