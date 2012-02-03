<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class FrontendPhotogalleryHelper
{
	/**
	 * Check if Amazon S3 exists
	 *
	 * @return bool
	 */
	public static function existsAmazonS3()
	{
		if(is_callable(array('FrontendAmazonS3Helper', 'getSetting'))) return FrontendAmazonS3Helper::getSetting('account');
		return false;
	}

	/**
	 * Get the url for an image
	 *
	 * @param string $path The path.
	 * @return string
	 */
	public static function getImageURL($path)
	{
		// Redefine
		$path = (string) $path;
		$url = false;
		
		if(SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $path) && !FrontendAmazonS3Model::existsCronjobPutByFullPath('photogallery', $path))
		{
			$url = FRONTEND_FILES_URL . '/' . $path;
		}
		else
		{
			if(FrontendAmazonS3Model::existsCronjobPutByFullPath('photogallery', $path))
			{
				$url = FRONTEND_FILES_URL . '/' . $path;
			}
			else
			{
				$url = FrontendAmazonS3Helper::getSetting('url') . $path;
			}
		}
		
		return $url;
	}
}