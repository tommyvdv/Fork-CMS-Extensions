<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This delete album action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryDeleteAlbum extends BackendBaseActionDelete
{
	/**
	 * Execute this action.
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendPhotogalleryModel::existsAlbum($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->record = (array) BackendPhotogalleryModel::getAlbum($this->id);

			// reset some data
			if(empty($this->record)) $this->record['title'] = '';

			// delete record
			$deleted = BackendPhotogalleryModel::deleteAlbum($this->id);
			$emptySetsAfterDelete =  $deleted['empty_set_ids'];
			$setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';
			
			foreach($emptySetsAfterDelete as $id)
			{

				SpoonDirectory::delete($setsFilesPath . '/frontend/' . $id);
				SpoonDirectory::delete($setsFilesPath . '/backend/' . $id);
				SpoonDirectory::delete($setsFilesPath . '/original/' . $id);
				
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

			// delete search indexes
			if(is_callable(array('BackendSearchModel', 'removeIndex'))) BackendSearchModel::removeIndex($this->getModule(), (int) $this->id);
			
			// deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('albums') . '&report=deleted-album&var=' . urlencode($this->record['title']));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('albums') . '&error=non-existing');
	}
}