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
class BackendAmazonS3AjaxLinkAccount extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$awsSecretKey = SpoonFilter::getPostValue('awsSecretKey', null, '');
		$awsAccessKey = SpoonFilter::getPostValue('awsAccessKey', null, '');

		// check input
		if(empty($awsSecretKey)) $this->output(self::BAD_REQUEST, array('field' => 'awsSecretKey'), BL::err('NoCMAccountCredentials'));
		if(empty($awsAccessKey)) $this->output(self::BAD_REQUEST, array('field' => 'awsAccessKey'), BL::err('NoCMAccountCredentials'));

		try
		{
			// check if the CampaignMonitor class exists
			if(!SpoonFile::exists(PATH_LIBRARY . '/external/aws/sdk-1.4.7/sdk.class.php'))
			{
				// the class doesn't exist, so throw an exception
				$this->output(self::BAD_REQUEST, null, BL::err('ClassDoesNotExist', $this->getModule()));
			}

			// require CampaignMonitor class
			require_once 'external/aws/sdk-1.4.7/sdk.class.php';

			$s3 = new AmazonS3($awsAccessKey, $awsSecretKey);
			$s3->enable_debug_mode(false);
			$s3->disable_ssl();
			$s3->disable_ssl_verification();
			
			$response = $s3->list_buckets();
			
			if($response->isOK())
			{
				// save the new data
				BackendModel::setModuleSetting($this->getModule(), 'awsSecretKey', $awsSecretKey);
				BackendModel::setModuleSetting($this->getModule(), 'awsAccessKey', $awsAccessKey);

				// account was linked
				BackendModel::setModuleSetting($this->getModule(), 'account', true);
				
				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_account_linked');
				
				// CM was successfully initialized
				$this->output(self::OK, array('message' => 'account-linked'), BL::msg('AccountLinked', $this->getModule()));
			}
			else
			{
				$this->output(self::BAD_REQUEST, null, BL::err('AmazonS3Error', $this->getModule()));
			}
			
		}

		catch(Exception $e)
		{
			$this->output(self::BAD_REQUEST, null, BL::err('AmazonS3Error', $this->getModule()));
		}
		
	}
}
