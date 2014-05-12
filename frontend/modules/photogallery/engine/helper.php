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

	/**
	 * Get the url for an image
	 *
	 * @param string $path The path.
	 * @return string
	 */
	public static function getImageURL($module, $image, $resolution)
	{
		$original 	= $module . '/sets/original/' . $image['set_id'] . '/'  . $image['filename'];
		$image 		= $module . '/sets/frontend/' . $image['set_id'] . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_'  . $resolution['method'] . '/'  . $image['filename'];
		
		if( ! SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $image) && SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $original)   )
		{
			$forceOriginalAspectRatio = $resolution['method'] == 'crop' ? false : true;
			$allowEnlargement = true;
			
			$thumb = new SpoonThumbnail(FRONTEND_FILES_PATH . '/' . $original, $resolution['width'], $resolution['height']);
			$thumb->setAllowEnlargement($allowEnlargement);
			$thumb->setForceOriginalAspectRatio($forceOriginalAspectRatio);
			$thumb->parseToFile(FRONTEND_FILES_PATH . '/' . $image,	100);
		}

		return FRONTEND_FILES_URL . '/' . $image;
	}
}