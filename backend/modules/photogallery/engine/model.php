<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the photogallery module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryModel
{
	const QRY_DATAGRID_BROWSE_CATEGORIES =
		'SELECT i.id, i.title, COUNT(a.album_id) AS num_albums, i.sequence,
			(SELECT COUNT(*) FROM photogallery_categories WHERE parent_id = i.id) AS num_children
		FROM photogallery_categories AS i
		LEFT OUTER JOIN photogallery_categories_albums AS a ON i.id = a.category_id
		WHERE i.language = ? AND i.parent_id = ?
		GROUP BY i.id';
	/*	
	const QRY_DATAGRID_BROWSE_CATEGORIES =
		'SELECT i.id, i.title, COUNT(a.album_id) AS num_albums, i.sequence
		FROM photogallery_categories AS i
		LEFT OUTER JOIN photogallery_categories_albums AS a ON i.id = a.category_id
		WHERE i.language = ?
		GROUP BY i.id';
		*/

	const QRY_DATAGRID_BROWSE_EXTRAS_BY_KIND =
		'SELECT i.id, i.action
		FROM photogallery_extras AS i
		INNER JOIN  photogallery_extras_resolutions AS r on r.extra_id = i.id
		WHERE i.kind = ?
		GROUP BY i.id';


	const QRY_DATAGRID_BROWSE_EXTRAS =
		'SELECT i.id, i.kind, i.action
		FROM photogallery_extras AS i
		INNER JOIN  photogallery_extras_resolutions AS r on r.extra_id = i.id
		GROUP BY i.id';

	const QRY_DATAGRID_BROWSE_IMAGES_FOR_SET =
		'SELECT i.id, i.filename, c.title, c.text, c.title_hidden, i.hidden as is_hidden, i.sequence
		FROM photogallery_sets_images AS i
		INNER JOIN photogallery_sets_images_content AS c on c.set_image_id = i.id
		WHERE i.set_id = ? AND c.album_id = ? AND c.language = ?
		GROUP BY i.id ORDER BY i.sequence DESC';

	const RESIZE_ORIGINAL_IMAGE = true; // resize the original image?
	const KEEP_ORIGINAL_IMAGE = true; // resize the original image?
	const MAX_ORIGINAL_IMAGE_WIDTH = 2000; // if RESIZE_ORIGINAL_IMAGE resize to # in px
	const MAX_ORIGINAL_IMAGE_HEIGHT = 2000; // if RESIZE_ORIGINAL_IMAGE resize to # in px
	const MAX_ORIGINAL_FILE_SIZE = 10; // in mb
	const IMAGE_QUALITY = 100; // 0 to 100
	const MAX_IMAGES_UPLOAD = 5;
	const DELETE_LOCAL_IN_TIME = '2 weeks';
	const MAX_ZIP_FILE_SIZE = 10;
	

	public static $backendResolutions = array(
		array('width' => 50, 'height' => 50, 'method' => 'crop'), // do not delete this one.
		array('width' => 128, 'height' => 128, 'method' => 'crop') // do not delete this one.
	);

	/**
	 * Returns breadcrumbs for certain category
	 *
	 * @param int $id The id of the category.
	 * @param int $depth Depth.
	 * @return array
	 */
	public static function getBreadcrumbsForCategory($id = 0, $depth = 0)
	{
		if(!$id) return array();

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get category
		$category = self::getCategory((int) $id);
		if($depth == 0) $category['selected'] = true;
		else if($depth == 1) $category['beforeSelected'] = true;
		else $category['selected'] = $category['selected'] = false;
		if ((bool) $category['parent_id'] == "0") $category['firstChild'] = true;
		$output[] = $category;
		
		if($category['parent_id']) $output = array_merge($output, self::getBreadcrumbsForCategory($category['parent_id'], $depth + 1));
		else $output[] = array("root" => true, "title" => Spoonfilter::ucfirst(BL::lbl('Root')), "beforeSelected" => isset($category['selected']) && $category['selected'] ? true : false);
		
		return $output;
	}
	
	
	/**
	 * Checks the settings and optionally returns an array with warnings
	 *
	 * @return array
	 */
	public static function checkSettings()
	{
		$warnings = array();

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('settings', 'photogallery'))
		{
			// rss title
			if(BackendModel::getModuleSetting('photogallery', 'rss_title_' . BL::getWorkingLanguage(), null) == '')
			{
				$warnings[] = array('message' => sprintf(BL::err('RSSTitle', 'photogallery'), BackendModel::createURLForAction('settings', 'photogallery')));
			}

			// rss description
			if(BackendModel::getModuleSetting('photogallery', 'rss_description_' . BL::getWorkingLanguage(), null) == '')
			{
				$warnings[] = array('message' => sprintf(BL::err('RSSDescription', 'photogallery'), BackendModel::createURLForAction('settings', 'photogallery')));
			}
		}

		return $warnings;
	}
	


	/**
	 * Checks if it is allowed to delete the a category
	 *
	 * @param int $id The id of the category.
	 * @return bool
	 */
	public static function deleteCategoryAllowed($id)
	{
		// check if has been assigned to album(s)
		$hasAlbums = (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(category_id)
			 FROM photogallery_categories_albums AS i
			 WHERE i.category_id = ?',
			array((int) $id)
		);

		// check if category is parent of children
		$hasChildren = (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(parent_id)
			FROM photogallery_categories AS i
			WHERE i.parent_id = ?',
			array((int) $id)
		);

		// if none of these apply return true
		return !($hasAlbums || $hasChildren);
	}
	/*
	public static function deleteCategoryAllowed($id)
	{
		return !(bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(category_id)
			 FROM photogallery_categories_albums AS i
			 WHERE i.category_id = ?',
			array((int) $id)
		);
	}
	*/

	/**
	 * Deletes one or more items
	 *
	 * @param  mixed $ids The ids to delete.
	 */
	public static function deleteAlbum($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get used meta ids
		$metaIds = (array) $db->getColumn(
			'SELECT meta_id
			 FROM photogallery_sets_images_content AS p
			 WHERE album_id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?',
			array_merge($ids, array(BL::getWorkingLanguage()))
		);

		// delete meta
		if(!empty($metaIds)) $db->delete('meta', 'id IN (' . implode(',', $metaIds) . ')');
		
		// Delete linked categories ids
		$db->delete('photogallery_categories_albums', 'album_id IN (' . implode(',', $ids) . ')');

		// Get sets linked to an album
		$setIds = self::getSetIdsForAlbum($ids);
		$emptySetIds  = array();

		// delete records
		$db->delete('photogallery_albums', 'id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?', array_merge($ids, array(BL::getWorkingLanguage())));
		$db->delete('photogallery_sets_images_content', 'album_id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?', array_merge($ids, array(BL::getWorkingLanguage())));

		// Update stats based on the setIds
		if(!empty($setIds)) self::updateSetStatistics($setIds);

		if(!empty($setIds)) $emptySetIds = self::getSetIdsToDelete($setIds);

		if(!empty($emptySetIds)) $db->delete('photogallery_sets_images', 'set_id IN (' . implode(', ', $emptySetIds) . ')');
		if(!empty($emptySetIds)) $db->delete('photogallery_sets_images_content', 'set_id IN (' . implode(', ', $emptySetIds) . ')');
		if(!empty($emptySetIds)) $db->delete('photogallery_sets', 'id IN (' . implode(', ', $emptySetIds) . ')');

		// Widgets
		$extraIds = self::getModuleExtraIdsForAlbum($ids);
		$db->delete('photogallery_extras_ids', 'album_id IN (' . implode(', ', $ids) . ')');
		if(!empty($extraIds)) $db->delete('modules_extras', 'id IN (' . implode(', ', $extraIds) . ')');

		// update blocks with this item linked
		$db->update('pages_blocks', array('extra_id' => null, 'html' => ''), 'extra_id IN (' . implode(', ', $ids) . ')');

		// update blocks with this item linked
		if(!empty($extraIds)) $db->update('pages_blocks', array('extra_id' => null, 'html' => ''), 'extra_id IN (' . implode(', ', $extraIds) . ')');

		// delete tags
		foreach($ids as $id) BackendTagsModel::saveTags($id, '', 'photogallery');

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('photogallery', BL::getWorkingLanguage());

		return array('ids' => $ids, 'empty_set_ids' => $emptySetIds);
	}

	/**
	 * Deletes one or more items
	 *
	 * @param  mixed $ids The ids to delete.
	 */
	public static function deleteExtra($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// delete records
		$db->delete('photogallery_extras', 'id IN (' . implode(', ', $idPlaceHolders) . ') ', $ids);
		$db->delete('photogallery_extras_resolutions', 'extra_id IN (' . implode(', ', $idPlaceHolders) . ') ', $ids);

		// Widgets
		$extraIds = self::getModuleExtraIdsForExtra($ids);
		$db->delete('photogallery_extras_ids', 'extra_id IN (' . implode(', ', $ids) . ')');
		if(!empty($extraIds)) $db->delete('modules_extras', 'id IN (' . implode(', ', $extraIds) . ')');

		// update blocks with this item linked
		if(!empty($extraIds)) $db->update('pages_blocks', array('extra_id' => null, 'html' => ''), 'extra_id IN (' . implode(', ', $extraIds) . ')');
	}

	/**
	 * Get the ids to delete
	 *
	 * @param mixed $ids The ids.
	 * @return array
	 */
	public static function getSetIdsToDelete($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// get db
		$db = BackendModel::getContainer()->get('database');

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		return (array) $db->getColumn(
			'SELECT id
			 FROM photogallery_sets AS p
			 WHERE id IN (' . implode(', ', $idPlaceHolders) . ') AND num_albums = ?',
			array_merge($ids, array(0))
		);
	}

	/**
	 * Deletes one or more items
	 *
	 * @param  mixed $ids The ids to delete.
	 */
	public static function deleteImage($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get used meta ids
		$metaIds = (array) $db->getColumn(
			'SELECT meta_id
			 FROM photogallery_sets_images_content AS p
			 WHERE set_image_id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?',
			array_merge($ids, array(BL::getWorkingLanguage()))
		);

		$setIds = self::getSetIdsForImage($ids);
		$emptySetIds  = array();

		// delete records
		$db->delete('photogallery_sets_images', 'id IN (' . implode(', ', $idPlaceHolders) . ')', array_merge($ids));
		$db->delete('photogallery_sets_images_content', 'set_image_id IN (' . implode(', ', $idPlaceHolders) . ') AND language = ?', array_merge($ids, array(BL::getWorkingLanguage())));

		// delete meta
		if(!empty($metaIds)) $db->delete('meta', 'id IN (' . implode(',', $metaIds) . ')');

		// Update stats based on the setIds
		if(!empty($setIds)) self::updateSetStatistics($setIds);

		if(!empty($setIds)) $emptySetIds = self::getSetIdsNoImages($setIds);

		if(!empty($emptySetIds)) $db->delete('photogallery_sets_images_content', 'set_id IN (' . implode(', ', $emptySetIds) . ')');
		if(!empty($emptySetIds)) $db->delete('photogallery_sets', 'id IN (' . implode(', ', $emptySetIds) . ')');
		if(!empty($emptySetIds)) BackendModel::getContainer()->get('database')->update('photogallery_albums', array('set_id' => null), 'set_id IN(' . implode(',', $emptySetIds) . ')');

		// invalidate the cache for blog
		BackendModel::invalidateFrontendCache('photogallery', BL::getWorkingLanguage());

		return array('ids' => $ids, 'empty_set_ids' => $emptySetIds);
	}

	/**
	 * Get the ids to for an image
	 *
	 * @param mixed $ids The ids.
	 * @return array
	 */
	public static function getSetIdsForImage($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get used set ids
		return (array) $db->getColumn(
			'SELECT set_id
			 FROM photogallery_sets_images AS p
			 WHERE id IN (' . implode(', ', $idPlaceHolders) . ')',
			$ids
		);
	}

	/**
	 * Get the ids to for an image
	 *
	 * @param mixed $ids The ids.
	 * @return array
	 */
	public static function getAlbumIdsForSet($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get used set ids
		return (array) $db->getColumn(
			'SELECT id
			 FROM photogallery_albums AS p
			 WHERE id IN (' . implode(', ', $idPlaceHolders) . ')',
			$ids
		);
	}

	/**
	 * Get the ids to for a set that has no images
	 *
	 * @param mixed $ids The ids.
	 * @return array
	 */
	public static function getSetIdsNoImages($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get used meta ids
		return (array) $db->getColumn(
			'SELECT id
			 FROM photogallery_sets
			 WHERE id IN (' . implode(', ', $idPlaceHolders) . ') AND num_images = ? ',
			array_merge($ids, array(0))
		);
	}

	/**
	 * Deletes a category
	 *
	 * @param int $id The id of the category to delete.
	 */
	public static function deleteCategory($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get item
		$item = self::getCategory($id);

		// any items?
		if(!empty($item))
		{
			// delete meta
			$db->delete('meta', 'id = ?', array($item['meta_id']));

			// delete category
			$db->delete('photogallery_categories', 'id = ?', array($id));

			// update category for the posts that might be in this category
			$db->delete('photogallery_categories_albums', 'category_id = ?', array($id));

			// invalidate the cache for blog
			BackendModel::invalidateFrontendCache('photogallery', BL::getWorkingLanguage());
		}
	}

	/**
	 * Checks if an album exists
	 *
	 * @param int $id The id of the album to check for existence.
	 * @return bool
	 */
	public static function existsAlbum($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
					'SELECT i.id
					 FROM photogallery_albums AS i
					 WHERE i.id = ? AND i.language = ?',
					array((int) $id, BL::getWorkingLanguage())
				);
	}

	/**
	 * Checks if a images exists
	 *
	 * @param int $id The id of the image to check for existence.
	 * @return bool
	 */
	public static function existsImage($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
					'SELECT i.id
					 FROM photogallery_sets_images AS i
					 WHERE i.id = ?',
					array((int) $id)
				);
	}

	/**
	 * Checks if a resolution exists
	 *
	 * @param int $width The width
	 * @param int $height The height
	 * @param string $method The method of the resolution
	 * @return bool
	 */
	public static function existsResolution($width, $height, $method)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
					'SELECT i.id
					 FROM photogallery_extras_resolutions AS i
					 WHERE i.width = ? AND i.height = ? AND i.method = ?',
					array((int) $width, (int) $height, (string) $method)
				);
	}

	/**
	 * Checks if an extra exists
	 *
	 * @param int $id The id of the extra to check for existence.
	 * @return bool
	 */
	public static function existsExtra($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
					'SELECT i.id
					 FROM photogallery_extras AS i
					 WHERE i.id = ?',
					array((int) $id)
				);
	}

	/*public static function existsCronjobByImageId($module, $id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
					'SELECT i.id
					 FROM amazon_s3_cronjobs AS i
					 WHERE i.module = ? AND i.data LIKE ?',
					array((string) $module, '%s:8:"image_id";i:' . (int) $id . ';%')
				);
	}*/

	/**
	 * Checks if an set exists
	 *
	 * @param int $id The id of the set to check for existence.
	 * @return bool
	 */
	public static function existsSet($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
					'SELECT i.id
					 FROM photogallery_sets AS i
					 WHERE i.id = ?',
					array((int) $id)
				);
	}

	/**
	 * Checks if a category exists
	 *
	 * @param int $id The id of the category to check for existence.
	 * @return int
	 */
	public static function existsCategory($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(id)
			 FROM photogallery_categories AS i
			 WHERE i.id = ? AND i.language = ?',
			array((int) $id, BL::getWorkingLanguage())
		);
	}

	/**
	 * Get all data for a given id and album
	 *
	 * @param int $id The id of the image to fetch
	 * @param int $album_id The album_id of the content
	 * @return array
	 */
	public static function getImageWithContent($id, $album_id)
	{
		$return =  (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.id, i.set_id, i.hidden, c.title, c.title_hidden, c.text, c.meta_id, i.filename, c.id as image_content_id, c.data, c.album_id
			FROM photogallery_sets_images AS i
			INNER JOIN photogallery_sets_images_content AS c ON c.set_image_id = i.id
			WHERE i.id = ? AND c.language = ? AND c.album_id = ?
			LIMIT 1',
			array((int) $id, BL::getWorkingLanguage(), $album_id));


		// unserialize data
		if($return['data'] !== null) $return['data'] = unserialize($return['data']);
		$return['title_hidden'] = ($return['title_hidden'] == 'Y');

		return $return;
	}

	/**
	 * Get all data for a given id
	 *
	 * @param int $id The id of the image to fetch
	 * @return array
	 */
	public static function getImage($id)
	{
		$return =  (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			FROM photogallery_sets_images AS i
			WHERE i.id = ?
			LIMIT 1',
			array((int) $id));

		return $return;
	}

	/**
	 * Get all data for a given id and kind
	 *
	 * @param int $extra_id The id of the extra to fetch
	 * @param string $kind The kind of the extra to fetch
	 * @return array
	 */
	public static function getExtraResolutionForKind($extra_id, $kind)
	{
		$return =  (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			FROM photogallery_extras_resolutions AS i
			WHERE i.extra_id = ? AND i.kind = ?
			LIMIT 1',
			array((int) $extra_id, (string) $kind));

		return $return;
	}

	/**
	 * Get all data for a given id
	 *
	 * @param int $id The Id of the resolution
	 * @return array
	 */
	public static function getExtraResolutions($id)
	{
		$return =  (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*
			FROM photogallery_extras_resolutions AS i
			WHERE i.extra_id = ?',
			array((int) $id));

		return $return;
	}

	/**
	 * Get all data for a given id
	 *
	 * @param int $id The id of an extra id
	 * @return array
	 */
	public static function getAllModuleExtraIds($id)
	{
		$return =  (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*
			FROM photogallery_extras_ids AS i
			WHERE i.extra_id = ?',
			array((int) $id));

		return $return;
	}

	/**
	 * Get all data for a given id
	 *
	 * @param int $id The id of the album to fetch
	 * @return array
	 */
	public static function getAlbum($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on,  UNIX_TIMESTAMP(i.new_from) AS new_from,  UNIX_TIMESTAMP(i.new_until) AS new_until , m.url,
			GROUP_CONCAT(c.category_id) AS category_ids
			FROM photogallery_albums AS i
			INNER JOIN meta AS m ON m.id = i.meta_id
			LEFT OUTER JOIN photogallery_categories_albums AS c ON i.id = c.album_id
			WHERE i.id = ?
			LIMIT 1',
			array((int) $id));
	}

	/**
	 * Get all the images
	 *
	 * @return array
	 */
	public static function getAllImages()
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT i.set_id, i.filename, i.id
			FROM photogallery_sets_images AS i');
	}

	/**
	 * Get all the albums
	 *
	 * @return array
	 */
	public static function getAllAlbums()
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*
			FROM photogallery_albums AS i');
	}

	/**
	 * Get all the sets
	 *
	 * @return array
	 */
	public static function getAllSets()
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*
			FROM photogallery_sets AS i');
	}

	/**
	 * Get all the extra widgets
	 *
	 * @return array
	 */
	public static function getAllExtrasWidgets()
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*
			FROM photogallery_extras AS i
			WHERE i.kind = ?', array('widget'));
	}

	/**
	 * Get all the resolutions for an extra
	 *
	 * @param int $id The id of an extra id
	 * @return array
	 */
	public static function getResolutionsForExtra($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords('SELECT width, height, method, kind FROM photogallery_extras_resolutions WHERE extra_id = ?',array((int) $id));
	}

	/**
	 * Get all the albums linked to a set
	 *
	 * @param int $id The id of the set
	 * @return array
	 */
	public static function getAlbumsLinkedToSet($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT a.language, a.id
			FROM  photogallery_albums AS a
			WHERE a.set_id = ?',
			array((int) $id));
	}

	/**
	 * Get all data for a given id
	 *
	 * @param int $id The id of the category to fetch.
	 * @return array
	 */
	public static function getCategory($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*, m.url
			FROM photogallery_categories AS i
				JOIN meta AS m ON m.id = i.meta_id
			WHERE i.id = ? AND i.language = ?',
			array(
				(int) $id,
				BL::getWorkingLanguage()
			)
		);
	}

	/**
	 * Get all data for a given id
	 *
	 * @param int $id The id of the extra to fetch.
	 * @return array
	 */
	public static function getExtra($id)
	{
		$return =  (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM photogallery_extras AS i
			 WHERE i.id = ? LIMIT 1',
			array((int) $id)
		);

		if($return['data'] !== null) $return['data'] = unserialize($return['data']);

		return $return;
	}

	/**
	 * Get all categories
	 *
	 * @param bool[optional] $includeCount Include the count?
	 * @return array
	 */
	/*
	public static function getCategoriesForDropdown($includeCount = false)
	{
		$db = BackendModel::getContainer()->get('database');

		if($includeCount)
		{
			return (array) $db->getPairs(
				'SELECT i.id, CONCAT(i.title, " (", COUNT(p.album_id) ,")") AS title
				 FROM photogallery_categories AS i
				 LEFT OUTER JOIN photogallery_categories_albums AS p ON i.id = p.category_id 
				 WHERE i.language = ? 
				 GROUP BY i.id ORDER BY i.sequence ASC',
				array(BL::getWorkingLanguage())
			);
		}

		return (array) $db->getPairs(
			'SELECT i.id, i.title
			 FROM photogallery_categories AS i
			 WHERE i.language = ? ORDER BY i.sequence ASC',
			array(BL::getWorkingLanguage())
		);
	}
	*/
	
	/**
	 * Get all categories
	 *
	 * @param bool[optional] $includeCount Include the count?
	 * @return array
	 */
	public static function getCategoriesCount()
	{
		return (int) BackendModel::getContainer()->get('database')->getVar(
			'SELECT count(i.id)
			FROM photogallery_categories AS i
			WHERE i.language = ?',
			array(BL::getWorkingLanguage())
		);
	}
	public static function getCategoriesForDropdown($allowedDepth = null, $includeCount = false, $parent_id = 0, $depth = 0, $parent = null, $seperator = '&rsaquo;', $space = ' ')
	{
		if(is_array($allowedDepth))
		{
			$startAllowedDepth = (int) $allowedDepth[0];
			$allowedDepth = (int) $allowedDepth[1];
		}

		$db = BackendModel::getContainer()->get('database');

		$categories = (array) $db->getPairs(
			'SELECT i.id, i.title
			FROM photogallery_categories AS i
			WHERE i.language = ? AND i.parent_id = ?
			ORDER BY i.sequence ASC',
			array(
				BL::getWorkingLanguage(),
				$parent_id
			)
		);

		if(
			(
				$depth < $allowedDepth ||
				$allowedDepth === 0
			) && !is_null($allowedDepth)
		)
		{
			foreach($categories as $key => $value)
			{
				if(
					!isset($startAllowedDepth) ||
					$depth >= $startAllowedDepth
				) $output[$key] =  $value;
				
				$children = self::getCategoriesForDropdown(isset($startAllowedDepth) && $startAllowedDepth ? array($startAllowedDepth, $allowedDepth) : $allowedDepth, $includeCount, $key, $depth + 1, $value);
				foreach($children as $c_key => $c_value)
				{
					$output[$c_key] = $value . $space . $seperator . $space . $c_value;
				}
			}
		}

		return empty($output) ? array() : $output;
	}

	/**
	 * Get the maximum id
	 *
	 * @return int
	 */
	public static function getSequenceAlbum()
	{
		// return
		return (int) BackendModel::getContainer()->get('database')->getVar('SELECT MAX(sequence) FROM photogallery_albums');
	}
	
	
	/**
	 * Get the maximum id
	 *
	 * @return int
	 */
	public static function getSequenceCategory()
	{
		// return
		return (int) BackendModel::getContainer()->get('database')->getVar('SELECT MAX(sequence) FROM photogallery_categories');
	}
	

	/**
	 * Get the ids for an album
	 *
	 * @param  mixed $ids The ids.
	 * @return array
	 */
	public static function getSetIdsForAlbum($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get used set ids
		return (array) $db->getColumn(
			'SELECT set_id
			 FROM photogallery_albums AS p
			 WHERE id IN (' . implode(', ', $idPlaceHolders) . ')',
			$ids
		);
	}

	/**
	 * Get the ids for an album
	 *
	 * @param  mixed $ids The ids.
	 * @return array
	 */
	public static function getModuleExtraIdsForAlbum($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get used set ids
		return (array) $db->getColumn(
			'SELECT modules_extra_id
			 FROM  photogallery_extras_ids AS p
			 WHERE album_id IN (' . implode(', ', $idPlaceHolders) . ')',
			$ids
		);
	}

	/**
	 * Get the ids for an an album
	 *
	 * @param  mixed $ids The ids.
	 * @return array
	 */
	public static function getExtraIdsForAlbum($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get used set ids
		return (array) $db->getRecords(
			'SELECT p.*, a.action
			 FROM  photogallery_extras_ids AS p
			INNER JOIN photogallery_extras AS a ON a.id = p.extra_id
			 WHERE album_id IN (' . implode(', ', $idPlaceHolders) . ')',
			$ids
		);
	}

	/**
	 * Get the ids for an extra
	 *
	 * @param  mixed $ids The ids.
	 * @return array
	 */
	public static function getModuleExtraIdsForExtra($ids)
	{
		// make sure $ids is an array
		$ids = (array) $ids;

		// loop and cast to integers
		foreach($ids as &$id) $id = (int) $id;

		// create an array with an equal amount of questionmarks as ids provided
		$idPlaceHolders = array_fill(0, count($ids), '?');

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get used set ids
		return (array) $db->getColumn(
			'SELECT modules_extra_id
			 FROM  photogallery_extras_ids AS p
			 WHERE extra_id IN (' . implode(', ', $idPlaceHolders) . ')',
			$ids
		);
	}

	/**
	 * Get all the images of a set
	 *
	 * @param int $id The id
	 * @return array
	 */
	public static function getSetImages($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT a.filename, a.original_filename, a.id
			FROM  photogallery_sets_images  as a
			WHERE a.set_id  = ?',
			array((int) $id));
	}

	/**
	 * Get the sets for a dropdown
	 *
	 * @return array
	 */
	public static function getSetsForDropdown()
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// get records and return them
		return $results =  (array) $db->getPairs(
									'SELECT s.id, CONCAT(s.language, ": ", a.title, " (", s.num_images ,")") AS title
									FROM photogallery_sets AS s
									LEFT JOIN photogallery_albums AS a ON (a.set_id = s.id)
									WHERE (a.set_id IS NULL) OR (a.set_id IS NOT NULL AND s.language != ?)
									GROUP BY s.id
									ORDER BY s.title ASC',
									array(BL::getWorkingLanguage()));

	}

	/**
	 * Get unique extra resolutions
	 *
	 * @return array
	 */
	public static function getUniqueExtrasResolutions()
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// get records and return them
		return (array) $db->getRecords(
									'SELECT DISTINCT r.width, r.height, r.method
									FROM photogallery_extras_resolutions AS r');

	}

	/**
	 * Get the maximum id
	 *
	 * @param int The of of the set
	 * @return int
	 */
	public static function getSetImageSequence($set_id)
	{
		// return
		return (int) BackendModel::getContainer()->get('database')->getVar('SELECT MAX(sequence) FROM photogallery_sets_images WHERE set_id = ? LIMIT 1',array($set_id));
	}

	/**
	 * Retrieve the unique URL for an item
	 *
	 * @param string $URL The string wheron the URL will be based.
	 * @param int[optional] $itemId The id of the photogallerypost to ignore.
	 * @return string The URL to base on.
	 */
	public static function getURLForAlbum($URL, $itemId = null)
	{
		// redefine URL
		$URL = SpoonFilter::urlise((string) $URL);

		// get db
		$db = BackendModel::getContainer()->get('database');

		// new item
		if($itemId === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
										FROM photogallery_albums AS i
										INNER JOIN meta AS m ON i.meta_id = m.id
										WHERE i.language = ? AND m.url = ?',
										array(BL::getWorkingLanguage(), $URL));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForAlbum($URL);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
										FROM photogallery_albums AS i
										INNER JOIN meta AS m ON i.meta_id = m.id
										WHERE i.language = ? AND m.url = ? AND i.id != ?',
										array(BL::getWorkingLanguage(), $URL, $itemId));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForAlbum($URL, $itemId);
			}
		}
		return $URL;
	}

	/**
	 * Retrieve the unique URL for a category
	 *
	 * @param string $URL The string wheron the URL will be based.
	 * @param int[optional] $categoryId The id of the category to ignore.
	 * @return string
	 */
	public static function getURLForCategory($URL, $id = null)
	{
		// redefine URL
		$URL = SpoonFilter::urlise((string) $URL);

		// get db
		$db = BackendModel::getContainer()->get('database');

		// new category
		if($id === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM photogallery_categories AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ?',
											array(BL::getWorkingLanguage(), $URL));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForCategory($URL);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM photogallery_categories AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ? AND i.id != ?',
											array(BL::getWorkingLanguage(), $URL, $id));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForCategory($URL, $id);
			}
		}

		// return the unique URL!
		return $URL;
	}

	/**
	 * Retrieve the unique URL for an image
	 *
	 * @param string $URL The string wheron the URL will be based.
	 * @param string $language The language
	 * @param int[optional] $id The id of the image to ignore.
	 * @return string
	 */
	public static function getURLForImage($URL, $language, $id = null)
	{
		// redefine URL
		$URL = SpoonFilter::urlise((string) $URL);

		// get db
		$db = BackendModel::getContainer()->get('database');

		// new category
		if($id === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM photogallery_sets_images_content AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ?',
											array($language, $URL));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForImage($URL, $language);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM photogallery_sets_images_content AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.language = ? AND m.url = ? AND i.id != ?',
											array($language, $URL, $id));

			// already exists
			if($number != 0)
			{
				// add number
				$URL = BackendModel::addNumber($URL);

				// try again
				return self::getURLForImage($URL, $language, $id);
			}
		}

		// return the unique URL!
		return $URL;
	}

	/**
	 * Retrieve the unique filename for a file
	 *
	 * @param string $filename The string wheron the filename will be based.
	 * @param string $extension The extension of the file.
	 * @param int[optional] $id The id of the category to ignore.
	 * @return string
	 */
	public static function getFilenameForImage($filename, $extension, $id = null)
	{
		// redefine
		$filename = SpoonFilter::urlise((string) $filename);

		// get db
		$db = BackendModel::getContainer()->get('database');

		// new category
		if($id === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM  photogallery_sets_images AS i
											WHERE i.filename = ?',
											array($filename . '.' . $extension));

			// already exists
			if($number != 0)
			{
				// add number
				$filename = BackendModel::addNumber($filename);

				// try again
				return self::getFilenameForImage($filename, $extension);
			}
		}

		// current category should be excluded
		else
		{
			// get number of items with this URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM photogallery_sets_images AS i
											WHERE i.filename = ? AND i.id != ?',
											array($filename . '.' . $extension, $id));

			// already exists
			if($number != 0)
			{
				// add number
				$filename = BackendModel::addNumber($filename);

				// try again
				return self::getFilenameForImage($filename, $extension, $id);
			}
		}

		// return the unique URL!
		return $filename;
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $item The data to insert.
	 * @return array
	 */
	public static function insertAlbum(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// insert and return the new id
		$item['id'] = $db->insert('photogallery_albums', $item);

		return $item['id'] ;
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $item The data to insert.
	 * @return array
	 */
	public static function insertExtraId(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// insert and return the new id
		$item['id'] = $db->insert('photogallery_extras_ids', $item);

		return $item['id'] ;
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $extra The extra data.
	 * @return int The id
	 */
	public static function insertModulesExtraWidget($extra)
	{
		$db = BackendModel::getContainer()->get('database');

		$extra['hidden'] = 'N';
		$extra['type'] = 'widget';
		$extra['sequence'] =  $db->getVar('SELECT MAX(i.sequence) + 1 FROM modules_extras AS i WHERE i.module = ?', array($extra['module']));
		if(is_null($extra['sequence'])) $extra['sequence'] = $db->getVar('SELECT CEILING(MAX(i.sequence) / 1000) * 1000 FROM modules_extras AS i');

		// Save widget
		return $db->insert('modules_extras', $extra);
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $item The data to insert.
	 * @return array
	 */
	public static function insertExtra(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// insert and return the new id
		$item['id'] = $db->insert('photogallery_extras', $item);

		return $item['id'] ;
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $item The data to insert.
	 * @return array
	 */
	public static function insertExtraResolution(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// insert and return the new id
		$item['id'] = $db->insert('photogallery_extras_resolutions', $item);

		return $item['id'] ;
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $item The data to insert.
	 * @return array
	 */
	public static function insertSet(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// insert and return the new id
		$item['id'] = $db->insert('photogallery_sets', $item);

		return $item['id'] ;
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $item The data to insert.
	 * @return array
	 */
	public static function insertSetImage(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// insert and return the new id
		$item['id'] = $db->insert('photogallery_sets_images', $item);

		return $item['id'] ;
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $item The data to insert.
	 * @param int[optional] $meta The meta data?
	 * @return array
	 */
	public static function insertCategory(array $item, $meta = null)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// meta given?
		if($meta !== null) $item['meta_id'] = $db->insert('meta', $meta);

		// create category
		$item['id'] = $db->insert('photogallery_categories', $item);

		// invalidate the cache for photogallery
		BackendModel::invalidateFrontendCache('photogallery', BL::getWorkingLanguage());

		// return the id
		return $item['id'];
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $item The data to insert.
	 * @param array $content The data to insert.
	 * @param array $meta The data to insert.
	 * @return array
	 */
	public static function insertImage(array $item, array $content, array $meta)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// insert the category
		$id = $db->insert('photogallery_sets_images', $item);

		// loop
		foreach($meta as $key => $row)
		{
			// set id
			$content[$key]['set_image_id'] = $id;

			// insert meta
			$content[$key]['meta_id'] = $db->insert('meta', $row);

			// insert content
			$db->insert('photogallery_sets_images_content', $content[$key]);
		}

		// return id
		return $id;
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $content The data to insert.
	 * @param array $meta The data to insert.
	 * @return array
	 */
	public static function insertImagesContentForExisting(array $content, array $meta)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// loop
		foreach($meta as $key => $row)
		{
			// insert meta
			$content[$key]['meta_id'] = $db->insert('meta', $row);

			// insert content
			$db->insert('photogallery_sets_images_content', $content[$key]);
		}

		return true;
	}

	/**
	 * Update an existing record
	 *
	 * @param array $item The new data.
	 * @return int
	 */
	public static function updateAlbum(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// update category
		$db->update('photogallery_albums', $item, 'id = ?', array($item['id']));
		
		return $item['id'];
	}

	/**
	 * Update an existing record
	 *
	 * @param array $item The new data.
	 * @return int
	 */
	public static function updateExtra(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// update category
		$db->update('photogallery_extras', $item, 'id = ?', array($item['id']));
		
		return $item['id'];
	}

	/**
	 * Update an existing record
	 *
	 * @param array $item The new data.
	 * @return int
	 */
	public static function updateExtraResolution(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// update category
		$db->update('photogallery_extras_resolutions', $item, 'id = ?', array($item['id']));
		
		return $item['id'];
	}

	/**
	 * Update an existing record
	 *
	 * @param array $item The new data.
	 * @return int
	 */
	public static function updateModulesExtraWidget(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// update category
		$db->update('modules_extras', $item, 'id = ?', array($item['id']));
		
		return $item['id'];
	}

	/**
	 * Update an existing record
	 *
	 * @param array $item The new data.
	 * @return int
	 */
	public static function updateModulesExtraBlockByModule(array $item)
	{
		$db = BackendModel::getContainer()->get('database');

		// update category
		$db->update('modules_extras', $item, 'module = ? AND type = ?', array($item['module'], $item['type']));
		
		return $item;
	}

	/**
	 * Update an existing record
	 *
	 * @param array $item The new data.
	 * @param array $content The new data.
	 * @return int
	 */
	public static function updateImage(array $item, array $content = null)
	{
		$db = BackendModel::getContainer()->get('database');

		// update
		if($content !== null) $db->update('photogallery_sets_images_content', $content, 'set_image_id = ? AND album_id = ? AND language = ?', array($content['set_image_id'], $content['album_id'], BL::getWorkingLanguage()));
		return $db->update('photogallery_sets_images', $item, 'id = ?', array($item['id']));
	}

	/**
	 * Update the set statistics
	 *
	 * @param  mixed $ids The ids to update
	 */
	public static function updateSetStatistics($ids)
	{
		$db = BackendModel::getContainer()->get('database');

		// make sure $ids is an array
		$ids = (array) $ids;

		foreach($ids as $id)
		{
			$item['num_images'] = 				(int) $db->getVar('SELECT COUNT(id) FROM  photogallery_sets_images WHERE set_id = ?', array((int) $id));
			$item['num_images_hidden'] =		(int) $db->getVar('SELECT COUNT(id) FROM  photogallery_sets_images WHERE set_id = ? AND hidden = ?', array((int) $id, 'Y'));
			$item['num_images_not_hidden'] = 	(int) $db->getVar('SELECT COUNT(id) FROM  photogallery_sets_images WHERE set_id = ? AND hidden = ?', array((int) $id, 'N'));
			$item['num_albums'] = 				(int) $db->getVar('SELECT COUNT(set_id) FROM  photogallery_albums WHERE set_id = ?', array((int) $id));

			// update
			$db->update('photogallery_sets', $item, 'id = ?', array((int) $id));

			// Update albums based on set_id
			$album_ids = $db->getColumn('SELECT id FROM photogallery_albums WHERE set_id  = ?', array((int) $id));

			self::updateAlbumStatistics($album_ids);
		}
	}

	/**
	 * Update the album statistics
	 *
	 * @param  mixed $ids The ids to update
	 */
	public static function updateAlbumStatistics($ids)
	{
		$db = BackendModel::getContainer()->get('database');

		// make sure $ids is an array
		$ids = (array) $ids;

		foreach($ids as $id)
		{
			// Get all the sets linked to the album and get count.
			$set_ids = $db->getColumn('SELECT set_id FROM photogallery_albums WHERE id  = ?', array((int) $id));

			$item['num_images'] = 				(int) $db->getVar('SELECT SUM(num_images) FROM  photogallery_sets WHERE id IN(' . implode(', ', $set_ids) . ')');
			$item['num_images_hidden'] =		(int) $db->getVar('SELECT SUM(num_images_hidden) FROM  photogallery_sets WHERE id IN(' . implode(', ', $set_ids) . ')');
			$item['num_images_not_hidden'] = 	(int) $db->getVar('SELECT SUM(num_images_not_hidden) FROM  photogallery_sets WHERE id IN(' . implode(', ', $set_ids) . ')');

			// update
			$db->update('photogallery_albums', $item, 'id = ?', array((int) $id));
		}
	}

	/**
	 * Update an existing category
	 *
	 * @param array $item The new data.
	 * @param array $meta The new meta data.
	 * @return int
	 */
	public static function updateCategory(array $item, $meta = null)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// update category
		$updated = $db->update('photogallery_categories', $item, 'id = ?', array((int) $item['id']));

		// meta passed?
		if($meta !== null)
		{
			// get current category
			$category = self::getCategory($item['id']);

			// update the meta
			$db->update('meta', $meta, 'id = ?', array((int) $category['meta_id']));
		}

		// invalidate the cache for photogallery
		BackendModel::invalidateFrontendCache('photogallery', BL::getWorkingLanguage());

		// return
		return $item['id'];
	}

	/**
	 * Update an existing record
	 *
	 * @param  mixed $ids The ids to update
	 */
	public static function updateAlbumsHidden($ids)
	{
		BackendModel::getContainer()->get('database')->update('photogallery_albums', array('hidden' => 'Y'), 'id IN(' . implode(',', $ids) . ')');
		
		return $ids;
	}

	/**
	 * Update an existing record
	 *
	 * @param  mixed $ids The ids to update
	 */
	public static function updateAlbumsPublished($ids)
	{
		BackendModel::getContainer()->get('database')->update('photogallery_albums', array('hidden' => 'N'), 'id IN(' . implode(',', $ids) . ')');
		return $ids;
	}

	/**
	 * Update an existing record
	 *
	 * @param  mixed $ids The ids to update
	 */
	public static function updateImagesHidden($ids)
	{
		BackendModel::getContainer()->get('database')->update('photogallery_sets_images', array('hidden' => 'Y'), 'id IN(' . implode(',', $ids) . ')');
		return $ids;
	}

	/**
	 * Update an existing record
	 *
	 * @param  mixed $ids The ids to update
	 */
	public static function updateImagesPublished($ids)
	{
		BackendModel::getContainer()->get('database')->update('photogallery_sets_images', array('hidden' => 'N'), 'id IN(' . implode(',', $ids) . ')');
		return $ids;
	}
	
	/**
	 * Update the linked categories for an item
	 *
	 * @return	void
	 * @param	int $id							The id of the item.
	 * @param	array[optional] $categories		The new categories.
	 */
	public static function updateAlbumCategories($id, array $categories = null)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getContainer()->get('database');

		// delete old categories
		$db->delete('photogallery_categories_albums', 'album_id = ?', $id);

		// insert the new one(s)
		if(!empty($categories)) $db->insert('photogallery_categories_albums', $categories);
	}


	public static $copyCategories = array(); 

	public static function copyCategories($from, $to, $parent = 0, $new_parent = 0)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		$categories = (array) $db->getRecords('SELECT * FROM photogallery_categories WHERE language = ? AND parent_id = ?', array($from, $parent));

		foreach($categories as $category)
		{
			// get and build meta
			$meta = $db->getRecord(
				'SELECT *
				 FROM meta
				 WHERE id = ?',
				array($category['meta_id'])
			);

			unset($meta['id']);

			$category['language'] = $to;
			$category['meta_id'] = (int) $db->insert('meta', $meta);
			$category['parent_id'] = $new_parent;

			$parent = $category['id'];

			unset($category['id']);

			$id = $db->insert('photogallery_categories', $category);

			BackendPhotogalleryModel::$copyCategories[$parent] = $id;

			BackendPhotogalleryModel::copyCategories($from, $to, $parent, $id);
		}
	}




	public static function copy($from, $to)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		$albums_ids = (array) $db->getColumn('SELECT id FROM photogallery_albums WHERE language = ?', array($from));

		$extras = (array) $db->getRecords('SELECT * FROM photogallery_extras WHERE kind = ?', array('widget'));

		BackendPhotogalleryModel::copyCategories($from, $to);

		foreach($albums_ids as $album_id)
		{
			
			$sourceData = BackendPhotogalleryModel::getAlbum($album_id);

			// get and build meta
			$meta = $db->getRecord(
				'SELECT *
				 FROM meta
				 WHERE id = ?',
				array($sourceData['meta_id'])
			);

			// remove id
			unset($meta['id']);
			unset($sourceData['id']);
			unset($sourceData['category_ids']);
			unset($sourceData['url']);

			$sourceData['meta_id'] = (int) $db->insert('meta', $meta);
			$sourceData['publish_on'] = BackendModel::getUTCDate();
			$sourceData['created_on'] = BackendModel::getUTCDate();
			$sourceData['edited_on'] = BackendModel::getUTCDate();
			$sourceData['language'] = $to;
			$sourceData['new_from'] = $sourceData['new_from'] == null ? null : BackendModel::getUTCDate(null, $sourceData['new_from']);
			$sourceData['new_until'] = $sourceData['new_until'] == null ? null : BackendModel::getUTCDate(null, $sourceData['new_until']);

			$new_album_id = BackendPhotogalleryModel::insertAlbum($sourceData);


			$categories = (array) $db->getRecords('SELECT * FROM photogallery_categories_albums WHERE album_id = ?', array($album_id));

			
			foreach($categories as $category)
			{
				$category['album_id'] = $new_album_id;
				$category['category_id'] = BackendPhotogalleryModel::$copyCategories[$category['category_id']];
				$db->insert('photogallery_categories_albums', $category);
			}


			// images
			$images_ids = (array)  $db->getColumn('SELECT id FROM photogallery_sets_images_content WHERE language = ? AND album_id = ?', array($from, $album_id));


			foreach($images_ids as $image_id){

				$image = $db->getRecord('SELECT * FROM photogallery_sets_images_content WHERE id = ?', array($image_id));

				// get and build meta
				$meta = $db->getRecord(
					'SELECT *
					 FROM meta
					 WHERE id = ?',
					array($image['meta_id'])
				);
				
				unset($meta['id']);
				unset($image['id']);

				$image['language'] = $to;
				$image['album_id'] = $new_album_id;
				$image['meta_id'] = (int) $db->insert('meta', $meta);

				// insert
				$db->insert('photogallery_sets_images_content', $image);
			}

			// extras
			foreach($extras as $extra)
			{
				$resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($extra['id']);

				$label = $sourceData['title'] . ' | ' . BackendTemplateModifiers::toLabel($extra['action']) . ' | ' . $resolutionsLabel;
				
				$new_extra['module'] = 'photogallery';
				$new_extra['label'] = $extra['action'];
				$new_extra['action'] = $extra['action'];
				$new_extra['data'] = serialize(
									array(
										'id' => $new_album_id,
										'extra_label' => $label,
										'extra_id' => $extra['id'],
										'language' => $to,
										'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $new_album_id
									)
								);
				
				$extraId = BackendPhotogalleryModel::insertModulesExtraWidget($new_extra);


				BackendPhotogalleryModel::insertExtraId(array(
					'album_id' => $new_album_id,
					'extra_id' => $extra['id'],
					'modules_extra_id' => $extraId
				));
			}
		}
	}

}