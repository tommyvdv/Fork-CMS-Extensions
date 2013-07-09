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
	 * Generate a correct path
	 *
	 * @return string
	 */
	public static function getPathJS($file, $module)
	{
		$file = (string) $file;
		$module = (string) $module;

		$theme = FrontendTheme::getTheme();
		$themePath = '/frontend/themes/' . $theme . '/core/js';

		$filePath = $themePath . $file;

		if(SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $filePath))) return $filePath;

		return '/frontend/modules/' . $module . '/js' . $file;
	}

	/**
	 * Generate a correct path
	 *
	 * @return string
	 */
	public static function getPathCSS($file, $module)
	{
		$file = (string) $file;
		$module = (string) $module;

		$theme = FrontendTheme::getTheme();
		$themePath = '/frontend/themes/' . $theme . '/core/layout/css';

		$filePath = $themePath . $file;

		if(SpoonFile::exists(PATH_WWW . str_replace(PATH_WWW, '', $filePath))) return $filePath;

		return '/frontend/modules/' . $module . '/layout/css' . $file;
	}

	public static function getImagePath($set_id, $filename, $resolution)
	{
		return 'photogallery/sets/frontend/' . $set_id . '/' . $resolution['width'] . 'x' . $resolution['height']  . $resolution['method'] . '/'  . $filename;
	}

	public static function getOriginalPath($set_id, $filename)
	{
		return 'photogallery/sets/original/' . $set_id . '/'  . $filename;
	}
	
	public static function createImage($var, $set_id, $filename, $width, $height, $method = 'crop')
	{
		$original 	= self::getOriginalPath($set_id, $filename);
		$image 		= self::getImagePath($set_id, $filename, array('width' => $width, 'height' => $height, 'method' => $method));
		
		if( ! SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $image) && SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $original)   )
		{
			$forceOriginalAspectRatio = $method == 'crop' ? false : true;
			$allowEnlargement = true;
			
			$thumb = new SpoonThumbnail(FRONTEND_FILES_PATH . '/' . $original, $width, $height);
			$thumb->setAllowEnlargement($allowEnlargement);
			$thumb->setForceOriginalAspectRatio($forceOriginalAspectRatio);
			$thumb->parseToFile(FRONTEND_FILES_PATH . '/' . $image,	100);
		}

		return FRONTEND_FILES_URL . '/' . $image;
	}
}