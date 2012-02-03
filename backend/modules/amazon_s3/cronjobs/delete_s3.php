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
class BackendAmazonS3CronjobDeleteS3 extends BackendBaseCronjob
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		
		// Include helper
		include BACKEND_MODULES_PATH . '/' . $this->getModule() . '/engine/helper.php';
	
		// set busy file
		$this->setBusyFile();
		
		$this->accountLinked = BackendAmazonS3Helper::checkAccount();
		
		if($this->accountLinked)
		{
			$this->settings = BackendAmazonS3Helper::getSettings();
			
			// S3 object
			$s3 = BackendAmazonS3Helper::get();
			
			// Delete
			$deleteRecords = BackendAmazonS3Model::getAllCronjobsByActionAndLocation('delete', 's3', BackendAmazonS3Model::CRONJOB_S3_DELETE_LIMIT);
			
			foreach($deleteRecords as $delete)
			{
				try
				{
					$response = $s3->get_object_list($this->settings['bucket'], array(
					   'prefix' => $delete['path'] . '/' . $delete['filename']
					));

					foreach ($response as $v)
					{
					    $s3->batch()->delete_object($this->settings['bucket'], $v);
					}

					$responses = $s3->batch()->send();

					if($responses->areOK())
					{
						BackendAmazonS3Model::deleteCronjobById($delete['id']);
					}
					else
					{
						BackendAmazonS3Model::updateCronjob(array('id' => $delete['id'], 'num_tries' => (int) $delete['num_tries'] + 1, 'last_tried_on' => BackendModel::getUTCDate()));
					}
				}
				catch(Exception $e)
				{
					BackendAmazonS3Model::updateCronjob(array('id' => $delete['id'], 'num_tries' => (int) $delete['num_tries'] + 1, 'last_tried_on' => BackendModel::getUTCDate()));
					
					throw new Exception('Something went wrong during the deletion in the Amazon S3 Bucket path: ' . $this->settings['s3_bucket']);
				}
			}
		}
		
		// remove busy file
		$this->clearBusyFile();
	}

}
