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
class AmazonS3Installer extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');
		
		// add 'blog' as a module
		$this->addModule('amazon_s3', 'The AWS S3 module that stores the global S3 settings.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'amazon_s3');

		// action rights
		$this->setActionRights(1, 'amazon_s3', 'settings');
		$this->setActionRights(1, 'amazon_s3', 'link_account');
		$this->setActionRights(1, 'amazon_s3', 'load_bucket_info');

		
		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'AmazonS3', 'amazon_s3/settings');
		
		// Settings
		$this->setSetting('amazon_s3', 'awsAccessKey', '');
		$this->setSetting('amazon_s3', 'awsSecretKey', '');
		$this->setSetting('amazon_s3', 'url', '');
		$this->setSetting('amazon_s3', 'account', false);
		$this->setSetting('amazon_s3', 'region', '');
		
		self::doApiCall();
	}
	
	private function doApiCall()
	{
		if(!is_callable(array('ApiCall', 'doCall'))) include dirname(__FILE__) . '/../engine/api_call.php';
		
		try
		{
			// build parameters
			$parameters = array(
				'site_domain' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'fork.local',
				'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
				'type' => 'module',
				'name' => 'amazon_s3',
				'version' => '1.0',
				'email' => SpoonSession::get('email')
			);
		
			// call
			$api = new ApiCall();
			$api->setApiURL('http://www.fork-cms-extensions.com/api/1.0');
			$api->doCall('products.insertProductInstallation', $parameters, false);
		} 
		catch(Exception $e) 
		{}
	}
	
	
	
}