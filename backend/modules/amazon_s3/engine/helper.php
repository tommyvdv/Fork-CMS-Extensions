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
class BackendAmazonS3Helper
{

	/**
	 * Checks if a valid CM account is set up
	 *
	 * @return bool
	 */
	public static function checkAccount()
	{
		try
		{
			$s3 = self::get();
			$response = $s3->list_buckets();
			return $response->isOK();
		}

		catch(Exception $e)
		{
			return false;
		}
		
		return false;
	}

	/**
	 * Get a setting
	 *
	 * @param string $setting The setting
	 * @return mixed
	 */
	public static function getSetting($setting)
	{
		return BackendModel::getModuleSetting('amazon_s3', $setting);
	}

	/**
	 * Get all the settings
	 *
	 * @return array
	 */
	public static function getSettings()
	{
		$settings = BackendModel::getModuleSettings();
		return isset($settings['amazon_s3']) ? $settings['amazon_s3'] : array();
	}

	/**
	 * Returns the Amazon S3 object.
	 *
	 * @return AmazonS3
	 */
	public static function get()
	{
		// campaignmonitor reference exists
		if(!Spoon::exists('s3'))
		{
			// check if the CampaignMonitor class exists
			if(!SpoonFile::exists(PATH_LIBRARY . '/external/aws/sdk-1.4.7/sdk.class.php'))
			{
				// the class doesn't exist, so throw an exception
				throw new SpoonFileException(BL::err('ClassDoesNotExist', 'amazon_s3'));
			}

			// require CampaignMonitor class
			require_once 'external/aws/sdk-1.4.7/sdk.class.php';

			// set login data
			$awsAccessKey = BackendModel::getModuleSetting('amazon_s3', 'awsAccessKey');
			$awsSecretKey = BackendModel::getModuleSetting('amazon_s3', 'awsSecretKey');

			// init CampaignMonitor object
			$s3 = new AmazonS3($awsAccessKey, $awsSecretKey);
			$s3->enable_debug_mode(false);
			$s3->disable_ssl();
			$s3->disable_ssl_verification();

			// set CampaignMonitor object reference
			Spoon::set('s3', $s3);
		}

		return Spoon::get('s3');
	}

	/**
	 * Returns the clients for use in a dropdown
	 *
	 * @return array
	 */
	public static function getBucketsAsPairs()
	{
		// get the base stack of clients
		$buckets = self::get()->get_bucket_list();

		// stop here if no clients were found
		if(empty($buckets)) return array();

		// reserve results stack
		$results = array();
		$results[0] = ucfirst(BL::lbl('CreateNewBucket', 'amazon_s3'));
		// loop the clients
		foreach($buckets as $bucket)
		{
			$results[$bucket] = $bucket;
		}

		return $results;
	}
	
	public static function getMimeTypes()
	{
		$mimes = array(	'hqx'	=>	'application/mac-binhex40',
						'cpt'	=>	'application/mac-compactpro',
						'csv'	=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
						'bin'	=>	'application/macbinary',
						'dms'	=>	'application/octet-stream',
						'lha'	=>	'application/octet-stream',
						'lzh'	=>	'application/octet-stream',
						'exe'	=>	array('application/octet-stream', 'application/x-msdownload'),
						'class'	=>	'application/octet-stream',
						'psd'	=>	'application/x-photoshop',
						'so'	=>	'application/octet-stream',
						'sea'	=>	'application/octet-stream',
						'dll'	=>	'application/octet-stream',
						'oda'	=>	'application/oda',
						'pdf'	=>	array('application/pdf', 'application/x-download'),
						'ai'	=>	'application/postscript',
						'eps'	=>	'application/postscript',
						'ps'	=>	'application/postscript',
						'smi'	=>	'application/smil',
						'smil'	=>	'application/smil',
						'mif'	=>	'application/vnd.mif',
						'xls'	=>	array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
						'ppt'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint'),
						'wbxml'	=>	'application/wbxml',
						'wmlc'	=>	'application/wmlc',
						'dcr'	=>	'application/x-director',
						'dir'	=>	'application/x-director',
						'dxr'	=>	'application/x-director',
						'dvi'	=>	'application/x-dvi',
						'gtar'	=>	'application/x-gtar',
						'gz'	=>	'application/x-gzip',
						'php'	=>	'application/x-httpd-php',
						'php4'	=>	'application/x-httpd-php',
						'php3'	=>	'application/x-httpd-php',
						'phtml'	=>	'application/x-httpd-php',
						'phps'	=>	'application/x-httpd-php-source',
						'js'	=>	'application/x-javascript',
						'swf'	=>	'application/x-shockwave-flash',
						'sit'	=>	'application/x-stuffit',
						'tar'	=>	'application/x-tar',
						'tgz'	=>	array('application/x-tar', 'application/x-gzip-compressed'),
						'xhtml'	=>	'application/xhtml+xml',
						'xht'	=>	'application/xhtml+xml',
						'zip'	=>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
						'mid'	=>	'audio/midi',
						'midi'	=>	'audio/midi',
						'mpga'	=>	'audio/mpeg',
						'mp2'	=>	'audio/mpeg',
						'mp3'	=>	array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
						'aif'	=>	'audio/x-aiff',
						'aiff'	=>	'audio/x-aiff',
						'aifc'	=>	'audio/x-aiff',
						'ram'	=>	'audio/x-pn-realaudio',
						'rm'	=>	'audio/x-pn-realaudio',
						'rpm'	=>	'audio/x-pn-realaudio-plugin',
						'ra'	=>	'audio/x-realaudio',
						'rv'	=>	'video/vnd.rn-realvideo',
						'wav'	=>	array('audio/x-wav', 'audio/wave', 'audio/wav'),
						'bmp'	=>	array('image/bmp', 'image/x-windows-bmp'),
						'gif'	=>	'image/gif',
						'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
						'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
						'jpe'	=>	array('image/jpeg', 'image/pjpeg'),
						'png'	=>	array('image/png',  'image/x-png'),
						'tiff'	=>	'image/tiff',
						'tif'	=>	'image/tiff',
						'css'	=>	'text/css',
						'html'	=>	'text/html',
						'htm'	=>	'text/html',
						'shtml'	=>	'text/html',
						'txt'	=>	'text/plain',
						'text'	=>	'text/plain',
						'log'	=>	array('text/plain', 'text/x-log'),
						'rtx'	=>	'text/richtext',
						'rtf'	=>	'text/rtf',
						'xml'	=>	'text/xml',
						'xsl'	=>	'text/xml',
						'mpeg'	=>	'video/mpeg',
						'mpg'	=>	'video/mpeg',
						'mpe'	=>	'video/mpeg',
						'qt'	=>	'video/quicktime',
						'mov'	=>	'video/quicktime',
						'avi'	=>	'video/x-msvideo',
						'movie'	=>	'video/x-sgi-movie',
						'doc'	=>	'application/msword',
						'docx'	=>	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
						'xlsx'	=>	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						'word'	=>	array('application/msword', 'application/octet-stream'),
						'xl'	=>	'application/excel',
						'eml'	=>	'message/rfc822',
						'json' => array('application/json', 'text/json')
					);
					
		return $mimes;
	}
	
	public static function getMimeByExtension($file)
	{
		$extension = strtolower(substr(strrchr($file, '.'), 1));
		
		$mimes = self::getMimeTypes();

		if (array_key_exists($extension, $mimes))
		{
			if (is_array($mimes[$extension]))
			{
				// Multiple mime types, just give the first one
				return current($mimes[$extension]);
			}
			else
			{
				return $mimes[$extension];
			}
		}
		else
		{
			return false;
		}
	}
}
