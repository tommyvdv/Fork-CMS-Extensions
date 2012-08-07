<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This delete image action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryDeleteImage extends BackendBaseActionDelete
{
	/**
	 * Execute this action.
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');
		$this->album_id = $this->getParameter('album_id', 'int');

		// does the item exist
		if($this->id !== null && BackendPhotogalleryModel::existsImage($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->record = (array) BackendPhotogalleryModel::getImageWithContent($this->id, $this->album_id);

			// reset some data
			if(empty($this->record)) $this->record['title'] = '';

			$deleted = BackendPhotogalleryModel::deleteImage((array) $this->id);
			$emptySetsAfterDelete =  $deleted['empty_set_ids'];
			
			// Delete cronjob
			if(BackendPhotogalleryHelper::existsAmazonS3()) BackendAmazonS3Model::deleteCronjobByData($this->URL->getModule(), 's:8:"image_id";i:' . (int) $this->id . ';');

			// Delete files
			$setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';

			// Backend resolutions
			foreach(BackendPhotogalleryModel::$backendResolutions as $resolution)
			{
				SpoonFile::delete($setsFilesPath . '/backend/' . $this->record['set_id'] . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'] . '/' . $this->record['filename']);
			}

			// Delete original
			SpoonFile::delete($setsFilesPath . '/original/' . $this->record['set_id'] . '/' . $this->record['filename']);

			// Frontend image
			$resolutions = BackendPhotogalleryModel::getUniqueExtrasResolutions();
			foreach($resolutions as $resolution)
			{
				SpoonFile::delete($setsFilesPath . '/frontend/' . $this->record['set_id'] . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'] . '/' . $this->record['filename']);
				
				$cronjob = array();
				$cronjob['module'] = $this->URL->getModule();
				$cronjob['path'] = $this->URL->getModule() . '/sets/frontend/' . $this->record['set_id'] . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'];
				$cronjob['filename'] = $this->record['filename'];
				$cronjob['full_path'] = $cronjob['path'] . '/' . $cronjob['filename'];
				$cronjob['data'] = serialize(array('set_id' => $this->record['set_id'], 'image_id' => $this->id));
				$cronjob['action'] = 'delete';
				$cronjob['location'] = 's3';
				$cronjob['created_on'] =  BackendModel::getUTCDate();
				$cronjob['execute_on'] = BackendModel::getUTCDate();
				if(BackendPhotogalleryHelper::existsAmazonS3()) BackendAmazonS3Model::insertCronjob($cronjob);
			}

			// Delete empty sets
			foreach($emptySetsAfterDelete as $id)
			{
				SpoonDirectory::delete($setsFilesPath . '/' . $id);
			
				// Are there any cronjobs with the same prefix? Delete them
				if(BackendPhotogalleryHelper::existsAmazonS3()) BackendAmazonS3Model::deleteCronjobByFullPathLike($this->URL->getModule(), $this->URL->getModule() . '/sets/' . $id);
				
				$cronjob = array();
				$cronjob['module'] = $this->URL->getModule();
				$cronjob['path'] = $this->URL->getModule() . '/sets/' . $id;
				$cronjob['full_path'] = $cronjob['path'] ;
				$cronjob['data'] = serialize(array('set_id' => $id, 'image_id' => null));
				$cronjob['action'] = 'delete';
				$cronjob['location'] = 's3';
				$cronjob['created_on'] =  BackendModel::getUTCDate();
				$cronjob['execute_on'] = BackendModel::getUTCDate();
				if(BackendPhotogalleryHelper::existsAmazonS3()) BackendAmazonS3Model::insertCronjob($cronjob);
			}

			// deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('edit') . '&report=image-deleted&var=' . urlencode($this->record['title']) . '&id=' . $this->record['album_id'] . '#tabImages');
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('edit') . '&error=non-existing&id=' . $this->record['album_id'] . '#tabImages' );
	}

}
