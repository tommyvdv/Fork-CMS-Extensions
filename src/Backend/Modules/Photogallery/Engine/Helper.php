<?php

namespace Backend\Modules\Photogallery\Engine;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

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
	public static function getExtraTitleForDataGrid($data)
	{
		$data = unserialize($data);
		if(isset($data['settings']) && isset($data['settings']['title'])) return $data['settings']['title'];
	}

	public static function getTitleWithNumAlbums($num_albums, $title, $edit_link = null)
	{
		return ($edit_link ? '<a href="' . $edit_link . '">' : '') . ($num_albums ? $title . ' (' . $num_albums . ')' : $title) . ($edit_link ? '</a>' : '');
	}

	public static function getNumchildrenButton($num_children, $id)
	{
		return $num_children = $num_children ? "<a href=\"" . BackendModel::createURLForAction("categories") . "&amp;category_id=" . $id . "\">" . vsprintf(BL::lbl("ViewSubcategories"), $num_children) . "</a>" : '';
	}

	public static function refreshResolution($resolution, $backend = false)
	{
		$backend_path = false;
		$frontend_path = FRONTEND_FILES_PATH . '/' . 'photogallery/sets/';

		$targetDir = ($resolution['width_null'] == 'Y' ? '' : $resolution['width']) . 'x' . ($resolution['height_null'] == 'Y' ? '' : $resolution['height'])  . $resolution['method'];
		//\Spoon::dump($targetDir);
		//\Spoon::dump($frontend_path);
		
		$finder = new Finder();
        $fs = new Filesystem();
        foreach (
            $finder->directories()
            	->name($targetDir)
                ->in($frontend_path)
                ->depth(2)
            as $directory
        ) {
        	$fs->remove($directory);
        }
		/*
		$objects = scandir($frontend_path);
		$targetDir = ($resolution['width_null'] == 'Y' ? '' : $resolution['width']) . 'x' . ($resolution['height_null'] == 'Y' ? '' : $resolution['height'])  . $resolution['method'];
		foreach($objects as $object)
		{
			if($object != "." && $object != ".." && $object != '.DS_Store')
			{
				if(
					$backend_path &&
					\SpoonDirectory::exists($backend_path . '/' . $object . '/' . $targetDir) &&
					filetype($backend_path . '/' . $object . '/' . $targetDir) == 'dir'
				)
					\SpoonDirectory::delete($backend_path . '/' . $object . '/' . $targetDir);
				
				if(
					\SpoonDirectory::exists($frontend_path . '/' . $object . '/' . $targetDir) &&
					filetype($frontend_path . '/' . $object . '/' . $targetDir) == 'dir'
				)
					\SpoonDirectory::delete($frontend_path . '/' . $object . '/' . $targetDir);
					//rmdir($frontend_path . '/' . $object . '/' . $targetDir);
				//else
					//unlink($frontend_path . '/' . $object . '/' . $targetDir);
			} 
		}
		*/
	}

    public static function getResolutionEditButton($id, $allow_edit)
    {
        return $allow_edit == 'Y' ? '<a class="button icon iconEdit linkButton" href="'.BackendModel::createURLForAction('edit_resolution').'&amp;id='.$id.'"><span>'.ucfirst(BL::getLabel('Edit')).'</span></a>' : '<a class="button icon iconDetails linkButton" href="'.BackendModel::createURLForAction('edit_resolution').'&amp;id='.$id.'"><span>'.ucfirst(BL::getLabel('Detail')).'</span></a>';
    }

	public static function translateYes($enum = 'Y')
	{
		return $enum == 'Y' ? BL::lbl('Yes') : BL::lbl('No');
	}

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

	public static function getSeperateResolutionsForDataGrid($extra_id)
	{
		$extras = BackendModel::get('database')->getRecords(
			'SELECT e.id as extra_id, r.*
			FROM photogallery_extras_resolutions AS e
				JOIN photogallery_resolutions AS r ON r.kind = e.resolution
			WHERE e.extra_id = ?',
			array(
				(int) $extra_id
			)
		);

		if(empty($extras)) return '';

		$return = '';

		foreach($extras as &$resolution)
		{
			$resolution['width_null'] = $resolution['width_null'] == 'Y' ? true : false;
			$resolution['height_null'] = $resolution['height_null'] == 'Y' ? true : false;
			$resolution['allow_watermark'] = $resolution['allow_watermark'] == 'Y' ? true : false;
			$resolution['regenerate'] = $resolution['regenerate'] == 'Y' ? true : false;
			$resolution['allow_delete'] = $resolution['allow_delete'] == 'Y' ? true : false;
			$resolution['allow_edit'] = $resolution['allow_edit'] == 'Y' ? true : false;

			$return .=  '<small>' . ($resolution['allow_edit'] ? '<a href="' . BackendModel::createURLForAction('edit_resolution') . '&id=' . $resolution['id'] . '">' : '') . $resolution['kind'] . ($resolution['allow_edit'] ? '</a>' : '') . ':</small> ' . ($resolution['width_null'] ? '*' : $resolution['width']) . 'x' . ($resolution['height_null'] ? '*' : $resolution['height']) . ' <small>(' . strtolower(BackendTemplateModifiers::toLabel($resolution['method'])) . ')</small>';
			$return .= '<br />';
		}

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
