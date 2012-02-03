<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 * In this file we store all generic functions that we will be using to help with the photogallery module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryHelper
{
	/**
	 * Create cronjob for each image
	 *
	 * @return bool
	 */
	 public static function createAmazonS3Cronjobs($module)
	{
		if(self::existsAmazonS3())
		{
			// Get all resolutions
			$resolutions = BackendPhotogalleryModel::getUniqueExtrasResolutions();

			// Get all images
			$images = BackendPhotogalleryModel::getAllImages();

			// Loop
			foreach($resolutions as $resolution)
			{
				foreach($images as $image)
				{
					// Resized image
					$cronjob = array();
					$cronjob['module'] = $module;
					$cronjob['path'] = $module . '/sets/frontend/' . $image['set_id'] . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'];
					$cronjob['filename'] =  $image['filename'];
					$cronjob['full_path'] = $cronjob['path'] . '/' . $cronjob['filename'];
					$cronjob['data'] = serialize(array('set_id' => $image['set_id'], 'image_id' => $image['id'], 'delete_local' => true, 'delete_local_in_time' => BackendPhotogalleryModel::DELETE_LOCAL_IN_TIME));
					$cronjob['action'] = 'put';
					$cronjob['location'] = 's3';
					$cronjob['created_on'] =  BackendModel::getUTCDate();
					$cronjob['execute_on'] = BackendModel::getUTCDate();

					BackendAmazonS3Model::insertCronjob($cronjob);

					// Original
					$cronjob = array();
					$cronjob['module'] = $module;
					$cronjob['path'] = $module . '/sets/original/' . $image['set_id'];
					$cronjob['filename'] =  $image['filename'];
					$cronjob['full_path'] = $cronjob['path'] . '/' . $cronjob['filename'];
					$cronjob['data'] = serialize(array('set_id' => $image['set_id'], 'image_id' => $image['id'], 'delete_local' => true, 'delete_local_in_time' => BackendPhotogalleryModel::DELETE_LOCAL_IN_TIME));
					$cronjob['action'] = 'put';
					$cronjob['location'] = 's3';
					$cronjob['created_on'] =  BackendModel::getUTCDate();
					$cronjob['execute_on'] = BackendModel::getUTCDate();

					BackendAmazonS3Model::insertCronjob($cronjob);
				}
			}
			return true;
		}
		return false;
	}
		
	/**
	 * Get the HTML for an image
	 *
	 * @param int $set_id The id of the set
	 * @param string $module The module where where all the files are stored
	 * @param string $filename The filename of the image
	 * @return string
	 */
	public static function getPreviewHTML50x50_crop($set_id, $module, $filename)
	{
		$image = FRONTEND_FILES_URL . '/' . $module . '/sets/backend/' . $set_id . '/50x50_crop/' . $filename;
		return '<img src="' . $image . '" width="50" height="50" />';
	}

	/**
	 * Get the HTML for an image
	 *
	 * @param int $set_id The id of the set
	 * @param string $module The module where where all the files are stored
	 * @param string $filename The filename of the image
	 * @return string
	 */
	public static function getPreviewHTML128x128_crop($set_id, $module, $filename)
	{
		$image = FRONTEND_FILES_URL . '/' . $module . '/sets/backend/' . $set_id . '/128x128_crop/' . $filename;
		return '<img src="' . $image . '" width="128" height="128" />';
	}

	/**
	 * Get the HTML for an image
	 *
	 * @param int $album_id The id of the album
	 * @param string $module The module where where all the files are stored
	 * @return string
	 */
	public static function getPreviewHTMLForAlbums50x50_crop($album_id, $module)
	{
		$result = (array) BackendModel::getDB()->getRecord(
				'SELECT i.filename, a.set_id
				FROM photogallery_albums AS a
				LEFT JOIN photogallery_sets_images as i ON i.set_id = a.set_id
				WHERE a.id = ? AND i.hidden = ?	
				ORDER BY i.sequence ASC
				LIMIT 1',
				array((int) $album_id, 'N')
		);
		
		if(empty($result)) return '';
					
		$image = FRONTEND_FILES_URL . '/' . $module . '/sets/backend/' . $result['set_id'] . '/50x50_crop/' . $result['filename'];
		return '<img src="' . $image . '" width="50" height="50" />';
	}

	/**
	 * Format the image count of an album
	 *
	 * @param int $num_images_not_hidden The count of image not hidden
	 * @param int $num_images The count of the total images
	 * @return string
	 */
	public static function getNumImagesForAlbums($num_images_not_hidden, $num_images)
	{
		return $num_images_not_hidden . '/' . $num_images;
	}

	/**
	 * Check if the amazon_s3 module exists
	 *
	 * @return void
	 */
	public static function existsAmazonS3()
	{
		if(BackendExtensionsModel::existsModule('amazon_s3') && BackendExtensionsModel::isModuleInstalled('amazon_s3')) BackendModel::getModuleSetting('amazon_s3', 'account');
		return false;
	}

	/**
	 * Get the original image
	 *
	 * @param string $path The path to the image
	 * @return bool
	 */
	public static function processOriginalImage($path)
	{
		$fromAmazonS3 = false;
		
		if(!SpoonFile::exists(FRONTEND_FILES_PATH . '/' . $path))
		{
			// Linked?
			if(self::existsAmazonS3())
			{
				
				if(BackendAmazonS3Helper::checkAccount())
				{
					// Not on amazon
					if(!BackendAmazonS3Model::existsCronjobByFullPath('photogallery', $path))
					{
						$from = BackendModel::getModuleSetting('amazon_s3', 'url') . $path;
						SpoonFile::download($from, FRONTEND_FILES_PATH . '/' . $path);
					}
		
					$fromAmazonS3 = true;
				}
			}
		}
		
		return $fromAmazonS3;
	}

	/**
	 * Get the resolution for the datagrid
	 *
	 * @param int $id The id of the extra
	 * @param string $kind The kind of resolution
	 * @return string
	 */
	public static function getWidgetResolutionForDatagridByKind($id, $kind)
	{
		$record = BackendModel::getDB()->getRecord('SELECT width, height, method FROM photogallery_extras_resolutions WHERE extra_id = ? AND kind = ? LIMIT 1',array((int) $id, (string) $kind));
		
		return !empty($record) ?  $record['width'] . 'x' . $record['height'] . ' (' . BackendTemplateModifiers::toLabel($record['method']) . ')' : '';
	}

	/**
	 * Format the edit url for a widget/module
	 *
	 * @param int $id The id of the extra
	 * @param string $kind The kind of widget
	 * @param string $action The action 
	 * @return string
	 */
	public static function getExtraEditURLForKind($id, $kind, $action)
	{
		$action = $action != null ? 'edit_' . strtolower($kind) . '_' . strtolower($action) : 'edit_' . strtolower($kind);
		$url = BackendModel::createURLForAction($action) . '&amp;id=' . $id;
		return '<a href="' . $url . '" class="button icon iconEdit linkButton"><span>' . BL::getLabel('Edit') . '</span></a>';
	}

	/**
	 * Format a string of all resolutions for an extra
	 *
	 * @param int $id The id of the extra
	 * @return string
	 */
	public static function getResolutionsForDataGrid($id)
	{
		$resolutions = BackendModel::getDB()->getRecords('SELECT width, height, method, kind FROM photogallery_extras_resolutions WHERE extra_id = ? ORDER BY method ASC',array((int) $id));
		
		if(empty($resolutions)) return '';
		
		$return = '';
		
		foreach($resolutions as $resolution)
		{
			$return .=  '<small>' . BackendTemplateModifiers::toLabel($resolution['kind']) . ':</small> ' . $resolution['width'] . 'x' . $resolution['height'] . ' <small>(' . strtolower(BackendTemplateModifiers::toLabel($resolution['method'])) . ')</small>';
			$return .= '<br />';
		}
		
		// Remove last <br />
		$return = rtrim($return,'<br />');
		
		return $return;
	}

	/**
	 * Format a string of all resolutions for an extra
	 *
	 * @param int $id The id of the extra
	 * @return string
	 */
	public static function getResolutionsForExtraLabel($id)
	{
		$resolutions = BackendModel::getDB()->getRecords('SELECT width, height, method, kind FROM photogallery_extras_resolutions WHERE extra_id = ? ORDER BY method ASC',array((int) $id));
		
		if(empty($resolutions)) return '';
		
		$return = '';
		
		foreach($resolutions as $resolution)
		{
			$return .=   BackendTemplateModifiers::toLabel($resolution['kind']) . ': ' . $resolution['width'] . 'x' . $resolution['height'] . ' (' . strtolower(BackendTemplateModifiers::toLabel($resolution['method'])) . ')';
			$return .= ' / ';
		}
		
		// Remove last /
		$return = rtrim($return,' / ');
		
		return $return;
	}
}
