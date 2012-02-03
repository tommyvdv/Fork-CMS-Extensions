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
class BackendAmazonS3CronjobPutS3 extends BackendBaseCronjob
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
			// store photogallery settings
			$this->settings = BackendAmazonS3Helper::getSettings();
			
			// S3 object
			$s3 = BackendAmazonS3Helper::get();
			
			// Put
			$putRecords = BackendAmazonS3Model::getAllCronjobsByActionAndLocation('put', 's3', BackendAmazonS3Model::CRONJOB_S3_PUT_LIMIT);
			
			foreach($putRecords as $put)
			{
				$file = FRONTEND_FILES_PATH . '/' . $put['full_path'];
			
				if(SpoonFile::exists($file))
				{
					try
					{
						$contentType = BackendAmazonS3Helper::getMimeByExtension($file);
					}
					catch(Exception $e)
					{
						throw new Exception('MimeType could not be established for: ' . $put['path']);
					}
					
					try
					{
						$content = SpoonFile::getContent($file);
						$response = $s3->create_object($this->settings['bucket'], $put['full_path'], 
											array(
												'body' => $content,
												'acl' => AmazonS3::ACL_PUBLIC,
												'contentType' => $contentType
											));

						if($response->isOK())
						{
							// Delete record
							BackendAmazonS3Model::deleteCronjobById($put['id']);
							
							if(isset($put['data']['delete_local']) && $put['data']['delete_local'] == true)
							{
								// Add delete cronjob
								$cronjob = array();
								$cronjob['path'] = $put['path'];
								$cronjob['filename'] = $put['filename'];
								$cronjob['full_path'] = $put['full_path'];
								$cronjob['data'] = serialize($put['data']);
								$cronjob['action'] = 'delete';
								$cronjob['module'] = $put['module'];
								$cronjob['location'] = 'local';
								$cronjob['created_on'] =  BackendModel::getUTCDate();
								$cronjob['execute_on'] = isset($put['data']['delete_local_in_time']) ? BackendModel::getUTCDate(null, strtotime('now +' . $put['data']['delete_local_in_time'])) :  BackendModel::getUTCDate(null, strtotime('now +' . BackendAmazonS3Model::DELETE_LOCAL_IN_TIME));
								BackendAmazonS3Model::insertCronjob($cronjob);
							}
						}
					
					}
					catch(Exception $e)
					{
						throw new Exception('Something went wrong during the PUT "action" in the Amazon S3 Bucket: ' . $this->settings['s3_bucket']);
					}
				
				}
				else
				{
					// Delete record
					BackendAmazonS3Model::deleteCronjobById($put['id']);
				}
			}
		}
		
		// remove busy file
		$this->clearBusyFile();
	}

}
