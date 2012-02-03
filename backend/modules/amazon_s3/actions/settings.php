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
class BackendAmazonS3Settings extends BackendBaseActionEdit
{
	/**
	 * Holds true if the CM account exists
	 *
	 * @var	bool
	 */
	private $accountLinked = false;

	/**
	 * The client ID
	 *
	 * @var	string
	 */
	private $bucket;

	/**
	 * The forms used on this page
	 *
	 * @var	BackendForm
	 */
	private $frmAccount, $frmBucket;

	/**
	 * Mailmotor settings
	 *
	 * @var	array
	 */
	private $settings = array();



	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();
		$this->getData();
		$this->loadAccountForm();
		$this->loadClientForm();
		$this->validateAccountForm();
		$this->validateClientForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Get all necessary data
	 */
	private function getData()
	{
		// store amazon_s3 settings
		$this->settings = BackendAmazonS3Helper::getSettings();

		// check if an account was linked already and/or client ID was set
		$this->accountLinked = BackendAmazonS3Helper::checkAccount();
		
		$this->bucket = BackendModel::getModuleSetting($this->getModule(), 'bucket');
	}

	/**
	 * Loads the account settings form
	 */
	private function loadAccountForm()
	{
		// init account settings form
		$this->frmAccount = new BackendForm('settingsAccount');

		// add fields for campaignmonitor API
		$this->frmAccount->addPassword('awsSecretKey', $this->settings['awsSecretKey']);
		$this->frmAccount->addText('awsAccessKey', $this->settings['awsAccessKey']);

		if($this->accountLinked)
		{
			$this->frmAccount->getField('awsSecretKey')->setAttributes(array('disabled' => 'disabled'));
			$this->frmAccount->getField('awsAccessKey')->setAttributes(array('disabled' => 'disabled'));
		}
	}

	/**
	 * Loads the client settings form
	 */
	private function loadClientForm()
	{
		// init account settings form
		$this->frmBucket = new BackendForm('settingsBucket', BackendModel::createURLForAction('settings') . '#tabSettingsBucket');

		// an account was succesfully made
		if($this->accountLinked)
		{
			// get all clients linked to the active account
			$buckets = BackendAmazonS3Helper::getBucketsAsPairs();

			// add field for client ID
			$this->frmBucket->addDropdown('buckets', $buckets, $this->bucket);
			$this->frmBucket->addDropdown('regions', 
						array(
							'' => 'US E1', 'us-west-1' => 'US W1', 'us-west-2' => 'US W2', 'EU' => 'EU W1', 'ap-southeast-1' => 'APAC SE1', 'ap-northeast-1' => 'APAC NE1'
							), BackendModel::getModuleSetting($this->getModule(), 'region'));
							
			$this->frmBucket->addText('bucket', $this->bucket);

		}
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		// parse settings in template
		$this->tpl->assign('account', $this->accountLinked);
		
		// parse client ID
		if($this->accountLinked && !empty($this->settings['bucket'])) $this->tpl->assign('bucket', $this->settings['bucket']);

		// add all forms to template
		$this->frmAccount->parse($this->tpl);
		$this->frmBucket->parse($this->tpl);
	}

	/**
	 * Attempts to create a client
	 *
	 * @param array $record The client record to create.
	 * @return mixed
	 */
	private function createBucket($record)
	{
		// create a client
		try
		{
			$s3 = BackendAmazonS3Helper::get();
			$response = $s3->create_bucket($record['bucket'], $record['region'], AmazonS3::ACL_PUBLIC);
			return $response->isOk();
		}
		catch(Exception $e)
		{
			// add an error to the email field
			$this->redirect(BackendModel::createURLForAction('settings') . '&error=s3-error&var=' . $e->getMessage() . '#tabSettingsBucket');
		}
		return false;
	}

	/**
	 * Validates the client tab
	 */
	private function validateClientForm()
	{
		// form is submitted
		if($this->frmBucket->isSubmitted())
		{
			// Only validte for new buckets
			if($this->frmBucket->getField('buckets')->getValue() == '0')
			{
				$this->frmBucket->getField('regions')->isFilled(BL::err('FieldIsRequired'));
				$this->frmBucket->getField('bucket')->isFilled(BL::err('FieldIsRequired'));
				
				$validBucketName = BackendAmazonS3Helper::get()->validate_bucketname_create($this->frmBucket->getField('bucket')->getValue());
				if(!$validBucketName) $this->frmBucket->getField('bucket')->addError(BL::err('InvalidBucketName'));
			}

			// form is validated
			if($this->frmBucket->isCorrect())
			{
				// get the client settings from the install
				$bucket = array();
				$bucket['region'] = $this->frmBucket->getField('regions')->getValue();
				$bucket['bucket'] = $this->frmBucket->getField('bucket')->getValue();

				// client ID was not yet set OR the user wants a new client created
				if($this->frmBucket->getField('buckets')->getValue() == '0')
				{
					// attempt to create the client
					$response = $this->createBucket($bucket);
					
					if($response)
					{
						$url = BackendAmazonS3Helper::get()->get_object_url($bucket['bucket'], '');
						BackendModel::setModuleSetting($this->getModule(), 'url',$url);
					
						// store the client info in our database
						BackendModel::setModuleSetting($this->getModule(), 'region', $bucket['region']);
						BackendModel::setModuleSetting($this->getModule(), 'bucket', $bucket['bucket']);

						// trigger event
						BackendModel::triggerEvent($this->getModule(), 'after_saved_bucket_settings');

						// redirect to a custom success message
						$this->redirect(BackendModel::createURLForAction('settings') . '&report=bucket-created&var=' . $bucket['bucket']);
					}
					else
					{
						$this->frmBucket->getField('bucket')->addError(BL::err('BucketNameIsNotUnique'));
					}
				}

				// client ID was already set
				else
				{
					// overwrite the client ID
					$this->bucket = $this->frmBucket->getField('buckets')->getValue();
					
					// get basic details for this client
					$url = BackendAmazonS3Helper::get()->get_object_url($this->bucket, '');
					$region = BackendAmazonS3Helper::get()->get_bucket_region($this->bucket);

					// store the client info in our database
					BackendModel::setModuleSetting($this->getModule(), 'bucket', $this->bucket);
					BackendModel::setModuleSetting($this->getModule(), 'url', $url);
					BackendModel::setModuleSetting($this->getModule(), 'region', $region->body);

					// trigger event
					BackendModel::triggerEvent($this->getModule(), 'after_saved_bucket_settings');

					// redirect to the settings page
					$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved#tabSettingsBucket');
				}
			}
		}
	}

	/**
	 * Validates the account tab. On successful validation it will unlink an existing campaignmonitor account.
	 */
	private function validateAccountForm()
	{
		// form is submitted
		if($this->frmAccount->isSubmitted())
		{
			// form is validated
			if($this->frmAccount->isCorrect())
			{
				// unlink the account and client ID
				BackendModel::setModuleSetting($this->getModule(), 'awsAccessKey', null);
				BackendModel::setModuleSetting($this->getModule(), 'awsSecretKey', null);
				BackendModel::setModuleSetting($this->getModule(), 'account', false);
				BackendModel::setModuleSetting($this->getModule(), 'url', null);
				BackendModel::setModuleSetting($this->getModule(), 'bucket', null);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_account_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=unlinked#tabSettingsAccount');
			}
		}
	}
	
	
	

}
