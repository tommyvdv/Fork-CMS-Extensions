<?php

namespace Backend\Modules\Photogallery\Engine;

use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\TemplateModifiers as BackendTemplateModifiers;
use Backend\Core\Engine\Language as BL;

/**
 * In this file we store all generic functions that we will be using to help with the photogallery module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Helper
{
	public static function toLabel($input)
	{
		if(!$input) return '';
		return BackendTemplateModifiers::toLabel($input);
	}

	//$this->dataGrid->setColumnFunction(create_function('$is_hidden','return $is_hidden = $is_hidden == "Y" ? \SpoonFilter::ucfirst(Bl::getLabel("Yes")) : \SpoonFilter::ucfirst(Bl::getLabel("No"));'),array('[is_hidden]'),'is_hidden',true);
	public static function translateBoolean($is_hidden)
	{
		return $is_hidden = $is_hidden == "Y" ? \SpoonFilter::ucfirst(Bl::getLabel("Yes")) : \SpoonFilter::ucfirst(Bl::getLabel("No"));
	}

	/*
		- Remove createAmazonS3Cronjobs
		- Remove existsAmazonS3
		- Remove processOriginalImage
		- existsCronjobByFullPath
	*/
		
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
		$result = (array) BackendModel::getContainer()->get('database')->getRecord(
				'SELECT i.filename, a.set_id
				FROM photogallery_albums AS a
				LEFT JOIN photogallery_sets_images as i ON i.set_id = a.set_id
				WHERE a.id = ? AND i.hidden = ?	
				ORDER BY i.sequence DESC
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
	 * Get the resolution for the datagrid
	 *
	 * @param int $id The id of the extra
	 * @param string $kind The kind of resolution
	 * @return string
	 */
	public static function getWidgetResolutionForDatagridByKind($id, $kind)
	{
		$record = BackendModel::getContainer()->get('database')->getRecord('SELECT width, height, method FROM photogallery_extras_resolutions WHERE extra_id = ? AND kind = ? LIMIT 1',array((int) $id, (string) $kind));
		
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
		$resolutions = BackendModel::getContainer()->get('database')->getRecords('SELECT width, height, method, kind FROM photogallery_extras_resolutions WHERE extra_id = ? ORDER BY method ASC',array((int) $id));
		
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
		$resolutions = BackendModel::getContainer()->get('database')->getRecords('SELECT width, height, method, kind FROM photogallery_extras_resolutions WHERE extra_id = ? ORDER BY method ASC',array((int) $id));
		
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
