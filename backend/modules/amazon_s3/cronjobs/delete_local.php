<?php

/*
 * This file is part of the amazon_s3 module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendAmazonS3CronjobDeleteLocal extends BackendBaseCronjob
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
	
		// set busy file
		$this->setBusyFile();
		
		// Delete
		$deleteRecords = BackendAmazonS3Model::getAllCronjobsByActionAndLocation('delete', 'local', BackendAmazonS3Model::CRONJOB_LOCAL_DELETE_LIMIT);
		
		foreach($deleteRecords as $delete)
		{
			SpoonFile::delete(FRONTEND_FILES_PATH . '/' . $delete['full_path']);
			
			// Delete empty folders
			$path = SpoonDirectory::getList(FRONTEND_FILES_PATH . '/' . $delete['path'], false, null,  '/.*/');
			if(empty($path)) SpoonDirectory::delete(FRONTEND_FILES_PATH . '/' . $delete['path']);	
			
			BackendAmazonS3Model::deleteCronjobById($delete['id']);
		}
		
		// remove busy file
		$this->clearBusyFile();
	}
}
