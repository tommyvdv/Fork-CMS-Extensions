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
class FrontendPhotogalleryModel implements FrontendTagsInterface
{

	const FANCYBOX_VERSION = '2.1.4';
	const FLEXSLIDER_VERSION = '2.1';
	
	public static function buildCategoriesNavigation($parent_id = 0, $selectedUrl = null)
	{
		// Get DB
		$db = FrontendModel::getContainer()->get('database');

		// redefine
		$tpl = (string) FRONTEND_MODULES_PATH . '/photogallery/layout/widgets/category_navigation_children.tpl';
		$tpl = FrontendTheme::getPath($tpl);

		$selectedId = self::getCategoryIdByUrl($selectedUrl);

		// Get all categories
		$categories = (array) self::getAllCategoriesRecursive(0, $selectedId);

		$categories = self::htmlizeCategories($categories, $tpl);

		// create template
		$categoriesTpl = new FrontendTemplate(false);

		// assign data to template
		$categoriesTpl->assign('navigation', $categories);

		// return parsed content
		$categories =  $categoriesTpl->getContent($tpl, true, true);

		return $categories;
	}

	public static function htmlizeCategories($categories, $tpl)
	{
		$categories = $categories;

		foreach($categories as $key => $category)
		{
			if(isset($category['categories']) && !empty($category['categories'])) $categories[$key]['categories'] = self::htmlizeCategories($category['categories'], $tpl);
			
			$categories[$key]['navigation_title'] = $category['label'];
			$categories[$key]['link'] = $category['full_url'];
			$categories[$key]['nofollow'] = false;
			$categories[$key]['selected'] = isset($category['selected_by_proxy']) && $category['selected_by_proxy'] ? true : $category['selected'];

			if(isset($categories[$key]['categories']))
			{
				// create template
				$categoriesTpl = new FrontendTemplate(false);

				// assign data to template
				$categoriesTpl->assign('navigation', $categories[$key]['categories']);

				// return parsed content
				$categories[$key]['children'] =  $categoriesTpl->getContent($tpl, true, true);
			}
			else
			{
				$categories[$key]['children'] = '';
			}
		}

		return $categories;
	}

	public static function getCategoryIdByUrl($url)
	{
		return (int) FrontendModel::getContainer()->get('database')->getVar(
			'SELECT category.id
			FROM content_categories AS category
				INNER JOIN meta AS meta ON meta.id = category.meta_id
			WHERE meta.url = ? AND category.language = ?',
			array(
				$url,
				FRONTEND_LANGUAGE
			)
		);
	}

	public static function getAllCategoriesRecursive($parent_id = 0, $selectedId = null)
	{
		$categories = self::getAllCategories(null, $selectedId);

		$categories = self::selectByProxy($categories, $selectedId);

		$categories = self::hierarchise($categories);

		return $categories;
	}

	public static function selectByProxy($categories, $selectedId = null)
	{
		$categories = $categories;

		if(isset($categories[$selectedId]) && $categories[$selectedId]['parent_id'])
		{
			$selectId = $categories[$selectedId]['parent_id'];

			$categories[$selectId]['selected_by_proxy'] = true;

			$categories = self::selectByProxy($categories, $selectId);
		}

		return $categories;
	}

	public static function hierarchise($categories, $parents = null)
	{
		$categories = $categories;
		$parents = $parents ? $parents : $categories;

		foreach($categories as $id => $category)
		{
			$children = null;
			foreach($parents as $child_key => $child)
			{
				if(isset($child['parent_id']) && $child['parent_id'] == (int) $category['id'])
				{
					$children[$child_key] = $child;
					unset($categories[$child_key]);

				}
			}

			if($children && isset($categories[$id]['id'])) $categories[$id]['categories'] = self::hierarchise($children, $parents);
		}

		return $categories;
	}

	public static function getCategoryNavigationHTML($tpl = 'navigation.tpl')
	{

		// FRONTEND_PATH . '/themes/' . FrontendModel::getModuleSetting('core', 'theme', 'default') . '/core/layout/templates/' . (string) $tpl
		/* 
			HOW TO USE
			====================
			
			Add this piece of code in /frontend/core/navigaton.php on L318
		
			if(($navigation[$type][$parentId][$id]['link'] == FrontendNavigation::getUrlForBlock('photogallery')))
			{
				$navigation[$type][$parentId][$id]['children'] = FrontendPhotogalleryModel::getCategoryNavigationHTML();
				continue;
			}
		
		*/
		
		// Get DB
		$db = FrontendModel::getContainer()->get('database');

		// redefine
		$tpl = (string) $tpl;
		$tpl = FrontendTheme::getPath($tpl);
		
		// Get all categories
		$categories = (array) self::getAllCategories();
		
		// Get URL's
		$urlCategory = FrontendNavigation::getURLForBlock('photogallery','category');
		$urlDetail = FrontendNavigation::getURLForBlock('photogallery','detail');
		$categoryParam = Spoon::get('url')->getParameter(1);
		$childParam = Spoon::get('url')->getParameter(3);

		// Loop categories
		foreach($categories as $categoryKey => $category)
		{
			// Set variabels needed for the template
			$categories[$categoryKey]['link'] = $urlCategory . '/' . $category['url'];
			$categories[$categoryKey]['navigation_title'] = $category['label'];
			
			// Get children for categories
			$albums = self::getAllForCategory($category['url']);
			
			// Is the page selected?
			$selected = $category['url'] == $categoryParam;
			$categories[$categoryKey]['selected'] = $selected;
			
			// Has children?
			if(!empty($albums))
			{
				foreach($albums as $albumKey => $child)
				{
					// Set variabels needed for the template
					$albums[$albumKey]['link'] = $urlDetail . '/' . $child['url'];
					$albums[$albumKey]['navigation_title'] = $child['title'];
					$albums[$albumKey]['children'] = false;

					// $albums[$albumKey]['selected'] = $child['url'] == $childParam; // Not tested!
				}
				
				// create template
				$categoriesChildrenTpl = new FrontendTemplate(false);

				// assign navigation to template
				$categoriesChildrenTpl->assign('navigation', $albums);

				// return parsed content
				$categories[$categoryKey]['children'] = $categoriesChildrenTpl->getContent($tpl, true, true);
			}
			else
			{
				$categories[$categoryKey]['children'] = false;
			}
		}
		
		// create template
		$categoriesTpl = new FrontendTemplate(false);

		// assign navigation to template
		$categoriesTpl->assign('navigation', $categories);

		// return parsed content
		$return =  $categoriesTpl->getContent($tpl, true, true);
		
		return $return;
	}
	
	
	
	/**
	 * Get the album data
	 *
	 * @param array $data The data.
	 * @return array
	 */
	public static function getAlbum($data)
	{
		$db = FrontendModel::getContainer()->get('database');
		
		$return =  (array) $db->getRecord(
			'SELECT i.id, i.text, i.introduction, i.title, i.set_id, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.new_from) AS new_from,  UNIX_TIMESTAMP(i.new_until) AS new_until,
			m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
			m.url,
			m.data AS meta_data,
			GROUP_CONCAT(c.category_id) AS category_ids
			FROM photogallery_albums AS i
			INNER JOIN meta AS m ON m.id = i.meta_id
			LEFT OUTER JOIN photogallery_categories_albums AS c ON i.id = c.album_id
			WHERE i.id = ? AND i.language = ? AND hidden = ? AND i.num_images_not_hidden != ? AND i.publish_on <= ?
			GROUP BY c.category_id
			LIMIT 1',
			array((int) $data['id'], FRONTEND_LANGUAGE, 'N', 0, FrontendModel::getUTCDate('Y-m-d H:i') . ':00'));
		
		if(empty($return)) return array();
		
		$return['category_ids'] = ($return['category_ids'] != '') ? (array) explode(',', $return['category_ids']) : null;
		
		// unserialize
		if(isset($return['meta_data'])) $return['meta_data'] = @unserialize($return['meta_data']);
		
		$return['images'] =  (array) $db->getRecords('SELECT i.id, i.filename, m.url, c.title, c.title_hidden, c.text, i.set_id, c.data
													FROM  photogallery_sets_images AS i
													INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
													INNER JOIN meta AS m ON m.id = c.meta_id
													WHERE i.set_id = ? AND c.language = ? AND i.hidden = ?
													ORDER BY sequence DESC',
													array((int) $return['set_id'], FRONTEND_LANGUAGE, 'N'), 'id');
		
	
		$imageLink = FrontendNavigation::getURLForBlock('photogallery', 'image');
		$categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');
		$detailLink = FrontendNavigation::getURLForBlock('photogallery', 'detail');

		$return['full_url'] = $detailLink . '/' . $return['url'];
		$return['is_new'] = ($return['new_from'] <= time() && time() <= $return['new_until']);
		
		// loop
		$i = 1;
		foreach($return['images'] as &$image)
		{
			// URLs
			$image['full_url'] = $imageLink . '/' . $image['url'];
			$image['title_hidden'] = ($image['title_hidden'] == 'Y');
			$image['data'] = $image['data'] != null ? unserialize($image['data']) : null;
			$image['index'] = $i++;
		}
		
		if($return['category_ids'] !== null)
		{
			$return['categories'] = (array) $db->getRecords('SELECT i.title, i.id, m.url
															FROM photogallery_categories as i
															INNER JOIN meta as m ON m.id = i.meta_id
															WHERE i.id IN (' . implode(', ', $return['category_ids']) . ')
														');

			foreach($return['categories'] as &$category)
			{
				$category['full_url'] = $categoryLink . '/' . $category['url'];
			}
		}
		

		return $return;
	}

	/**
	 * Get the album based on the URL
	 *
	 * @param array $URL The URL.
	 * @return array
	 */
	public static function get($URL)
	{
		$db = FrontendModel::getContainer()->get('database');
		
		$return =  (array) $db->getRecord(
			'SELECT i.id, i.text, i.introduction, i.title, i.set_id, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.new_from) AS new_from,  UNIX_TIMESTAMP(i.new_until) AS new_until ,
			m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
			m.url,
			m.data AS meta_data,
			GROUP_CONCAT(c.category_id) AS category_ids
			FROM photogallery_albums AS i
			INNER JOIN meta AS m ON m.id = i.meta_id
			LEFT OUTER JOIN photogallery_categories_albums AS c ON i.id = c.album_id
			WHERE m.url = ? AND i.language = ? AND hidden = ?  AND show_in_albums = ? AND i.num_images_not_hidden != ? AND i.publish_on <= ? 
			GROUP BY i.id',
			array((string) $URL, FRONTEND_LANGUAGE, 'N', 'Y', 0, FrontendModel::getUTCDate('Y-m-d H:i') . ':00'));
		
		if(empty($return)) return array();
		
		// unserialize
		if(isset($return['meta_data'])) $return['meta_data'] = @unserialize($return['meta_data']);
		
		$return['category_ids'] = ($return['category_ids'] != '') ? (array) explode(',', $return['category_ids']) : null;
		
		$return['images'] =  (array) $db->getRecords('SELECT i.id, i.filename, m.url, c.title, c.title_hidden, c.text, i.set_id, c.data
													FROM  photogallery_sets_images AS i
													INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
													INNER JOIN meta AS m ON m.id = c.meta_id
													WHERE i.set_id = ? AND c.language = ? AND i.hidden = ?
													ORDER BY sequence DESC',
													array((int) $return['set_id'], FRONTEND_LANGUAGE, 'N'));
		
	
		$imageLink = FrontendNavigation::getURLForBlock('photogallery', 'image');
		$categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');
		$detailLink = FrontendNavigation::getURLForBlock('photogallery', 'detail');

		$return['full_url'] = $detailLink . '/' . $return['url'];
		$return['is_new'] = ($return['new_from'] <= time() && time() <= $return['new_until']);
		
		// loop
		foreach($return['images'] as &$image)
		{
			$image['full_url'] = $imageLink . '/' . $image['url'];
			$image['data'] = $image['data'] != null ? unserialize($image['data']) : null;
			$image['title_hidden'] = ($image['title_hidden'] == 'Y');
		}
		
		if($return['category_ids'] !== null)
		{
			$return['categories'] = (array) $db->getRecords('SELECT i.title, i.id, m.url
															FROM photogallery_categories as i
															INNER JOIN meta as m ON m.id = i.meta_id
															WHERE i.id IN (' . implode(', ', $return['category_ids']) . ')
														');

			foreach($return['categories'] as &$category)
			{
				$category['full_url'] = $categoryLink . '/' . $category['url'];
			}
		}
		
		return $return;
	}

	/**
	 * Getan image
	 *
	 * @param array $URL The URL.
	 * @return array
	 */
	public static function getImage($URL)
	{
		$db = FrontendModel::getContainer()->get('database');

		$return =  (array) $db->getRecord('SELECT images.id, images.sequence, images.filename, content.title_hidden,
											content.album_id, content.title, content.text,  content.set_id, content.data,
											album.title AS album_title, 
											album.introduction AS album_introduction, 
											album.text AS album_text,
											UNIX_TIMESTAMP(album.publish_on) AS publish_on,
											category.title AS category_title,
											categorymeta.url AS category_url,
											albummeta.url AS album_url,
											
											imagemeta.keywords AS meta_keywords, imagemeta.keywords_overwrite AS meta_keywords_overwrite,
											imagemeta.description AS meta_description, imagemeta.description_overwrite AS meta_description_overwrite,
											imagemeta.title AS meta_title, imagemeta.title_overwrite AS meta_title_overwrite,
											imagemeta.data AS meta_data,
											
											GROUP_CONCAT(categorya.category_id) AS category_ids
												
											FROM photogallery_sets_images AS images
											
											INNER JOIN photogallery_sets_images_content AS content ON content.set_image_id = images.id
											INNER JOIN photogallery_albums AS album ON content.album_id = album.id
											LEFT OUTER JOIN photogallery_categories_albums AS categorya ON categorya.album_id = album.id
											LEFT OUTER JOIN photogallery_categories AS category ON categorya.category_id = category.id
											
											INNER JOIN meta AS imagemeta ON content.meta_id = imagemeta.id
											LEFT OUTER JOIN meta AS categorymeta ON category.meta_id = categorymeta.id
											INNER JOIN meta AS albummeta ON album.meta_id = albummeta.id
											
											WHERE content.language = ? AND images.hidden = ? AND album.publish_on <= ? AND imagemeta.url = ?
											GROUP BY images.id
											LIMIT 1',
											array(FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i') . ':00', (string) $URL));

		if(empty($return)) return array();
		
		// unserialize
		if(isset($return['meta_data'])) $return['meta_data'] = @unserialize($return['meta_data']);
		
		$return['category_ids'] = ($return['category_ids'] != '') ? (array) explode(',', $return['category_ids']) : null;
		
		$return['album_full_url'] = FrontendNavigation::getURLForBlock('photogallery', 'detail') . '/' . $return['album_url'];
		$return['data'] = $return['data'] != null ? unserialize($return['data']) : null;

		$return['title_hidden'] = ($return['title_hidden'] == 'Y');


		if($return['category_ids'] !== null)
		{

			$return['categories'] = (array) $db->getRecords('SELECT i.title, i.id, m.url
															FROM photogallery_categories as i
															INNER JOIN meta as m ON m.id = i.meta_id
															WHERE i.id IN (' . implode(', ', $return['category_ids']) . ')
														');
			
			$categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');
			
			foreach($return['categories'] as &$category)
			{
				$category['full_url'] = $categoryLink . '/' . $category['url'];
			}
		}
		
		return $return;
	}

	/**
	 * Get an array with the previous and the next post
	 *
	 * @param int $id The id of the current item.
	 * @param int $album_id The album_id of the current item.
	 * @param int $sequence The sequence of the current item.
	 * @return array
	 */
	public static function getImageNavigation($id, $album_id, $sequence)
	{
		// get db
		$db = FrontendModel::getContainer()->get('database');
		
		$return = array();
		
		// get previous post
		$return['previous'] = $db->getRecord(
											'SELECT i.id, c.album_id, c.title, a.title AS album_title, i.filename, m.url
											FROM photogallery_sets_images AS i
											INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
											INNER JOIN photogallery_albums AS a ON c.album_id = a.id
											INNER JOIN meta AS m ON m.id = c.meta_id
											WHERE i.id != ? AND i.sequence = ? AND i.hidden = ?  AND a.show_in_albums = ? AND c.language = ? AND a.id = ?
											ORDER BY i.sequence ASC
											LIMIT 1',
											array((int) $id, $sequence+1, 'N', 'Y', FRONTEND_LANGUAGE, (int) $album_id));

		// get next post
		$return['next'] = $db->getRecord(
											'SELECT i.id, c.album_id, c.title, a.title AS album_title, i.filename, m.url
											FROM photogallery_sets_images AS i
											INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
											INNER JOIN photogallery_albums AS a ON c.album_id = a.id
											INNER JOIN meta AS m ON m.id = c.meta_id
											WHERE i.id != ? AND i.sequence = ? AND i.hidden = ?  AND a.show_in_albums = ? AND c.language = ? AND a.id = ?
											ORDER BY i.sequence DESC
											LIMIT 1',
											array((int) $id, $sequence-1, 'N', 'Y', FRONTEND_LANGUAGE, (int) $album_id));

		// return
		return $return;
	
	}

	/**
	 * Get an array with the previous and the next post
	 *
	 * @param int $id The id of the current item.
	 * @return array
	 */
	public static function getNavigation($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = FrontendModel::getContainer()->get('database');

		// get date for current item
		$sequence = (int) $db->getVar('SELECT i.sequence
									FROM photogallery_albums AS i
									WHERE i.id = ?',
									array($id));

		// validate
		if($sequence == '') return array();

		// init var
		$return = array();

		// get previous post
		$return['previous'] = $db->getRecord('SELECT i.id, i.title, m.url
											FROM photogallery_albums AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.id != ? AND i.hidden = ?  AND i.show_in_albums = ? AND i.language = ? AND i.sequence = ? AND i.num_images > 0 AND i.publish_on <= ?
											ORDER BY i.sequence DESC
											LIMIT 1',
											array($id, 'N', 'Y', FRONTEND_LANGUAGE, $sequence-1, FrontendModel::getUTCDate('Y-m-d H:i') . ':00'));

		// get next post
		$return['next'] = $db->getRecord('SELECT i.id, i.title, m.url
											FROM photogallery_albums AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											WHERE i.id != ? AND i.hidden = ?  AND i.show_in_albums = ? AND i.language = ? AND i.sequence = ? AND i.num_images > 0 AND i.publish_on <= ?
											ORDER BY i.sequence ASC
											LIMIT 1',
											array($id,'N', 'Y', FRONTEND_LANGUAGE, $sequence+1, FrontendModel::getUTCDate('Y-m-d H:i') . ':00'));

		// return
		return $return;
	}
	
	/**
	 * Get an array with the previous and the next post
	 *
	 * @param int $id The id of the current item.
	 * @return array
	 */
	public static function getNavigationInCategory($id, $category_ids)
	{
		
		// redefine
		$id = (int) $id;
		$category_ids = (array) $category_ids;
		
		if(empty($category_ids)) return array();

		// get db
		$db = FrontendModel::getContainer()->get('database');

		// get date for current item
		$sequence = (int) $db->getVar('SELECT i.sequence
									FROM photogallery_albums AS i
									WHERE i.id = ?',
									array($id));

		// validate
		if($sequence == '') return array();

		// init var
		$return = array();

		// get previous post
		$return['previous'] = $db->getRecord('SELECT i.id, i.title, m.url
											FROM photogallery_albums AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											INNER JOIN photogallery_categories_albums AS c ON c.album_id = i.id
											WHERE c.category_id IN ('. implode(', ', $category_ids) . ') AND i.id != ? AND i.hidden = ?  AND i.show_in_albums = ? AND i.language = ? AND i.sequence = ? AND i.num_images > 0 AND i.publish_on <= ?
											GROUP BY i.id
											ORDER BY i.publish_on DESC
											LIMIT 1',
											array($id, 'N', 'Y', FRONTEND_LANGUAGE, $sequence-1, FrontendModel::getUTCDate('Y-m-d H:i') . ':00'));

		// get next post
		$return['next'] = $db->getRecord('SELECT i.id, i.title, m.url
											FROM photogallery_albums AS i
											INNER JOIN meta AS m ON i.meta_id = m.id
											INNER JOIN photogallery_categories_albums AS c ON c.album_id = i.id
											WHERE c.category_id IN ('. implode(', ', $category_ids) . ') AND i.id != ? AND i.hidden = ?  AND i.show_in_albums = ? AND i.language = ? AND i.sequence = ? AND i.num_images > 0 AND i.publish_on <= ?
											GROUP BY i.id
											ORDER BY i.publish_on ASC
											LIMIT 1',
											array($id,'N', 'Y', FRONTEND_LANGUAGE, $sequence+1, FrontendModel::getUTCDate('Y-m-d H:i') . ':00'));
										
		
		// return
		return $return;
	}

	/**
	 * Get all albums
	 *
	 * @param int $limit The limit
	 * @param int $offset The offset
	 * @param int $extra_id The extra id from the module data
	 * @return array
	 */
	public static function getAll($limit = 10, $offset = 0)
	{
		$db = FrontendModel::getContainer()->get('database');
		
		$return =  (array) $db->getRecords(
			'SELECT i.id, i.text, i.introduction, i.title, i.set_id, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.new_from) AS new_from,  UNIX_TIMESTAMP(i.new_until) AS new_until, m.url,
			GROUP_CONCAT(c.category_id) AS category_ids
			FROM photogallery_albums AS i
			INNER JOIN meta AS m ON m.id = i.meta_id
			LEFT OUTER JOIN photogallery_categories_albums AS c ON i.id = c.album_id
			WHERE i.language = ? AND i.hidden = ? AND i.show_in_albums = ? AND i.num_images_not_hidden > ? AND i.publish_on <= ?
			GROUP BY i.id 
			ORDER BY i.sequence DESC
			LIMIT ?, ? ',
			array(FRONTEND_LANGUAGE, 'N', 'Y', 0, FrontendModel::getUTCDate('Y-m-d H:i') . ':00', (int) $offset, (int) $limit), 'id');
		
		if(empty($return)) return array();

		$categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');
		$detailLink = FrontendNavigation::getURLForBlock('photogallery', 'detail');
		
		// loop
		foreach($return as &$row)
		{
			$row['full_url'] = $detailLink . '/' . $row['url'];
			$row['is_new'] = ($row['new_from'] <= time() && time() <= $row['new_until']);
			$row['image'] =  (array) $db->getRecord('SELECT i.filename, m.url, c.title, c.text, i.set_id, c.data
														FROM photogallery_sets_images AS i
														INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
														INNER JOIN meta AS m ON m.id = c.meta_id
														WHERE i.set_id = ? AND c.language = ? AND i.hidden = ?
														ORDER BY sequence DESC LIMIT 1',
														array((int) $row['set_id'], FRONTEND_LANGUAGE, 'N'));

			$row['image']['data'] = $row['image']['data'] != null ? unserialize($row['image']['data']) : null;
			$row['category_ids'] = ($row['category_ids'] != '') ? (array) explode(',', $row['category_ids']) : null;

			if($row['category_ids'] !== null)
			{
				$row['categories'] = (array) $db->getRecords('SELECT i.title, i.id, m.url
																FROM photogallery_categories as i
																INNER JOIN meta as m ON m.id = i.meta_id
																WHERE i.id IN (' . implode(', ', $row['category_ids']) . ')
															');

				foreach($row['categories']as &$category)
				{
					$category['full_url'] = $categoryLink . '/' . $category['url'];
				}
			}
		}

		// get all tags
		$tags = FrontendTagsModel::getForMultipleItems('photogallery', array_keys($return));

		// loop tags and add to correct item
		foreach($tags as $postId => $tags)
		{
			if(isset($return[$postId])) $return[$postId]['tags'] = $tags;
		}

		return $return;
	}
	
	/**
	 * Get all albums
	 *
	 * @param int $limit The limit
	 * @param int $offset The offset
	 * @param int $extra_id The extra id from the module data
	 * @return array
	 */
	public static function getAllWithImages($limit = 10, $offset = 0)
	{
		$db = FrontendModel::getContainer()->get('database');

		$return =  (array) $db->getRecords(
			'SELECT i.id, i.text, i.introduction, i.title, i.set_id, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.new_from) AS new_from,  UNIX_TIMESTAMP(i.new_until) AS new_until, m.url,
			GROUP_CONCAT(c.category_id) AS category_ids
			FROM photogallery_albums AS i
			INNER JOIN meta AS m ON m.id = i.meta_id
			LEFT OUTER JOIN photogallery_categories_albums AS c ON i.id = c.album_id
			WHERE i.language = ? AND i.hidden = ?  AND i.show_in_albums = ? AND i.num_images_not_hidden > ? AND i.publish_on <= ?
			GROUP BY i.id 
			ORDER BY i.sequence ASC
			LIMIT ?, ? ',
			array(FRONTEND_LANGUAGE, 'N', 'Y', 0, FrontendModel::getUTCDate('Y-m-d H:i') . ':00', (int) $offset, (int) $limit), 'id');
		
		if(empty($return)) return array();

		$categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');
		$detailLink = FrontendNavigation::getURLForBlock('photogallery', 'detail');
		
		// loop
		foreach($return as &$row)
		{
			$row['full_url'] = $detailLink . '/' . $row['url'];
			$row['is_new'] = ($row['new_from'] <= time() && time() <= $row['new_until']);
			$row['image'] =  (array) $db->getRecord('SELECT i.filename, m.url, c.title, c.text, i.set_id, c.data
														FROM photogallery_sets_images AS i
														INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
														INNER JOIN meta AS m ON m.id = c.meta_id
														WHERE i.set_id = ? AND c.language = ? AND i.hidden = ?
														ORDER BY sequence ASC LIMIT 1',
														array((int) $row['set_id'], FRONTEND_LANGUAGE, 'N'));

			$row['image']['data'] = $row['image']['data'] != null ? unserialize($row['image']['data']) : null;
			$row['category_ids'] = ($row['category_ids'] != '') ? (array) explode(',', $row['category_ids']) : null;

			if($row['category_ids'] !== null)
			{
				$row['categories'] = (array) $db->getRecords('SELECT i.title, i.id, m.url
																FROM photogallery_categories as i
																INNER JOIN meta as m ON m.id = i.meta_id
																WHERE i.id IN (' . implode(', ', $row['category_ids']) . ')
															');

				foreach($row['categories']as &$category)
				{
					$category['full_url'] = $categoryLink . '/' . $category['url'];
				}
			}

			// get images
			$row['images'] =  (array) $db->getRecords('SELECT i.id, i.filename, m.url, c.title, c.text, i.set_id, c.data
																FROM  photogallery_sets_images AS i
																INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
																INNER JOIN meta AS m ON m.id = c.meta_id
																WHERE i.set_id = ? AND c.language = ? AND i.hidden = ?
																ORDER BY sequence ASC',
																array((int) $row['set_id'], FRONTEND_LANGUAGE, 'N'));

			$imageLink = FrontendNavigation::getURLForBlock('photogallery', 'image');

			// loop
			foreach($row['images'] as &$image)
			{
				$image['full_url'] = $imageLink . '/' . $image['url'];
				$image['data'] = $image['data'] != null ? unserialize($image['data']) : null;
			}
		}

		// get all tags
		$tags = FrontendTagsModel::getForMultipleItems('photogallery', array_keys($return));

		// loop tags and add to correct item
		foreach($tags as $postId => $tags)
		{
			if(isset($return[$postId])) $return[$postId]['tags'] = $tags;
		}

		return $return;
	}

	/**
	 * Fetch the list of tags for a list of items
	 *
	 * @param array $ids The ids of the items to grab.
	 * @return array
	 */
	public static function getForTags(array $ids)
	{
		// fetch items
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.title, m.url
			 FROM photogallery_albums AS i
			 INNER JOIN meta AS m ON m.id = i.meta_id
			 WHERE i.hidden = ? AND i.id IN (' . implode(',', $ids) . ')
			 ORDER BY i.publish_on DESC',
			array('N')
		);

		// has items
		if(!empty($items))
		{
			// init var
			$link = FrontendNavigation::getURLForBlock('photogallery', 'detail');

			// reset url
			foreach($items as &$row) $row['full_url'] = $link . '/' . $row['url'];
		}

		// return
		return $items;
	}

	/**
	 * Get the id of an item by the full URL of the current page.
	 * Selects the proper part of the full URL to get the item's id from the database.
	 *
	 * @param FrontendURL $URL The current URL.
	 * @return int
	 */
	public static function getIdForTags(FrontendURL $URL)
	{
		// select the proper part of the full URL
		$itemURL = (string) $URL->getParameter(1);

		// return the item
		return self::get($itemURL);
	}

	/**
	 * Get the resolution for the extra
	 *
	 * @param int $extra_id The extra_id
	 * @param string $kind The kind, small or large
	 * @return array
	 */
	public static function getExtraResolutionForKind($extra_id, $kind)
	{
		$return =  (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			FROM photogallery_extras_resolutions AS i
			WHERE i.extra_id = ? AND i.kind = ?
			LIMIT 1',
			array((int) $extra_id, (string) $kind));
			
		return $return;
	}

	public static function getExtra($extra_id)
	{
		$return =  (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			FROM photogallery_extras AS i
			WHERE i.id = ? 
			LIMIT 1',
			array((int) $extra_id));

		if(isset($return['data'])) $return['data'] = @unserialize($return['data']);
			
		return $return;
	}

	/**
	 * Get the number of items
	 *
	 * @return int
	 */
	public static function getAlbumsCount()
	{
		return (int) FrontendModel::getContainer()->get('database')->getVar('SELECT COUNT(i.id) AS count
														FROM photogallery_albums AS i
														WHERE i.language = ? AND i.hidden = ?  AND i.show_in_albums = ? AND i.publish_on <= ? AND i.num_images_not_hidden > ?',
														array(FRONTEND_LANGUAGE, 'N', 'Y', FrontendModel::getUTCDate('Y-m-d H:i') . ':00', 0));
	}

	/**
	 * Get all categories used
	 *
	 * @return array
	 */
	public static function getAllCategories($parent_id = null, $selectedId = null)
	{
		$parameters[] = FRONTEND_LANGUAGE;
		$parameters[] = 'N';
		$parameters[] = FrontendModel::getUTCDate('Y-m-d H:i') . ':00';
		if(!is_null($parent_id)) $parameters[] = $parent_id;
		
		$return =  (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT c.id, c.title AS label, m.url, COUNT(c.id) AS total, c.parent_id,
				m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
				m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
				m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.data AS meta_data
			FROM photogallery_categories AS c
				/*INNER JOIN photogallery_categories_albums AS a ON c.id = a.category_id*/
				/*INNER JOIN photogallery_albums AS i ON a.album_id = i.id AND c.language = i.language*/
				INNER JOIN meta AS m ON m.id = c.meta_id
			WHERE c.language = ?
				/*AND i.hidden = ?*/
				/*AND i.publish_on <= ?*/
				/*AND i.num_images > 0*/' .
				(is_null($parent_id) ? '' : ' AND c.parent_id = ?') . '
			GROUP BY c.id
			ORDER BY c.sequence ASC',
			$parameters,
			'id'
		);
		
		$categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');

		foreach($return as &$row)
		{
			// set url
			$row['full_url'] = $categoryLink . '/' . $row['url'];
			$row['parent_id'] = (int) $row['parent_id'];
			
			// selected
			$row['selected'] = $row['id'] == $selectedId ? true : false;

			// unserialize
			if(isset($row['meta_data'])) $row['meta_data'] = @unserialize($row['meta_data']);
		}
		
		return $return;
	}
	
	
	/**
	 * Get all categories used
	 *
	 * @return array
	 */
	public static function getAllCategoriesWithImage()
	{
	  $return =  (array) FrontendModel::getContainer()->get('database')->getRecords('SELECT c.id, c.title AS label, m.url, img.filename, img.set_id,
	                            m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
	                            m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
	                            m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.data AS meta_data
	                            FROM photogallery_categories AS c
	                            INNER JOIN photogallery_categories_albums AS ca ON ca.category_id = c.id
								INNER JOIN photogallery_albums AS i ON i.id = ca.album_id
	                            INNER JOIN photogallery_sets_images AS img ON i.set_id = img.set_id
	                            INNER JOIN meta AS m ON m.id = c.meta_id
	                            WHERE c.language = ? AND i.hidden = ?  AND i.show_in_albums = ? AND i.publish_on <= ?  AND i.num_images > 0
	                            ORDER BY i.sequence DESC, img.sequence ASC',
	                            array(FRONTEND_LANGUAGE, 'N', 'Y', FrontendModel::getUTCDate('Y-m-d H:i') . ':00'), 'id');

	  $categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');

	  foreach($return as &$row)
	  {
	    $row['full_url'] = $categoryLink . '/' . $row['url'];
	    // unserialize
	    if(isset($row['meta_data'])) $row['meta_data'] = @unserialize($row['meta_data']);
	  }

	  return $return;
	}
	
	/*
	public static function getAllCategoriesWithImage()
	{
		$return =  (array) FrontendModel::getContainer()->get('database')->getRecords('SELECT c.id, c.title AS label, m.url, COUNT(c.id) AS total,
															m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
															m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
															m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.data AS meta_data
															FROM photogallery_categories AS c
															INNER JOIN meta AS m ON m.id = c.meta_id
															INNER JOIN photogallery_albums AS i ON c.id = i.category_id AND c.language = i.language
															INNER JOIN photogallery_sets_images AS img ON i.set_id = img.set_id
															WHERE c.language = ?
															GROUP BY c.id ORDER BY c.sequence ASC',
															array(FRONTEND_LANGUAGE), 'id');

		if(empty($return)) return array();

		$categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');

		foreach($return as &$row)
		{
			$row['full_url'] = $categoryLink . '/' . $row['url'];

			// unserialize
			if(isset($row['meta_data'])) $row['meta_data'] = @unserialize($row['meta_data']);

			$row['album'] = (array) FrontendModel::getContainer()->get('database')->getRecord('SELECT *
															FROM photogallery_albums
															WHERE category_id = ? AND hidden = ? AND publish_on <= ?
															ORDER BY sequence ASC LIMIT 1', array($row['id'], 'N', FrontendModel::getUTCDate('Y-m-d H:i') . ':00'));

			$row['image'] = (array) FrontendModel::getContainer()->get('database')->getRecord('SELECT *
															FROM photogallery_sets_images
															WHERE set_id = ?
															ORDER BY sequence ASC LIMIT 1', array($row['album']['set_id']));
		}
		
		return $return;
	}
	*/
	
	/**
	 * Get the number of items in a given category
	 *
	 * @param string $URL The URL for the category.
	 * @return int
	 */
	public static function getAllForCategoryCount($categoryURL)
	{
		return (int) FrontendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(DISTINCT i.id) AS count
			FROM photogallery_albums AS i
				INNER JOIN photogallery_categories_albums AS a ON i.id = a.album_id
				INNER JOIN photogallery_categories AS c ON a.category_id = c.id
				INNER JOIN meta AS m ON m.id = c.meta_id
			WHERE i.language = ?
				AND i.hidden = ?  AND i.show_in_albums = ?
				AND i.publish_on <= ?
				AND m.url = ?
				AND i.num_images > 0',
			array(
				FRONTEND_LANGUAGE,
				'N', 'Y',
				FrontendModel::getUTCDate('Y-m-d H:i') . ':00',
				(string) $categoryURL
			)
		);
	}

	/**
	 * Get all items in a category (at least a chunk)
	 *
	 * @param string $categoryURL The URL of the category to retrieve the posts for.
	 * @param int[optional] $limit The number of items to get.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function getAllForCategory($categoryURL, $limit = 10, $offset = 0, $ignoreLimit = false)
	{
		$db = FrontendModel::getContainer()->get('database');
		
		// get the items
		$return = (array) $db->getRecords(
			'SELECT i.id, i.language, i.title, i.introduction, i.text, i.num_images, i.set_id, 
			c.title AS category_title, cm.url AS category_url,
			UNIX_TIMESTAMP(i.publish_on) AS publish_on,
			m.url, GROUP_CONCAT(ab.category_id) AS category_ids
			FROM photogallery_albums AS i
			INNER JOIN photogallery_categories_albums AS a ON i.id = a.album_id
			LEFT OUTER JOIN photogallery_categories_albums AS ab ON i.id = ab.album_id
			INNER JOIN photogallery_categories AS c ON a.category_id = c.id
			INNER JOIN meta AS m ON i.meta_id = m.id
			INNER JOIN meta AS cm ON cm.id = c.meta_id
			WHERE  i.language = ? AND i.hidden = ?  AND i.show_in_albums = ? AND i.publish_on <= ? AND cm.url = ?  AND i.num_images > 0
			GROUP BY i.id
			ORDER BY i.sequence ASC' .
			($ignoreLimit ? '/* LIMIT ?, ? */' : ' LIMIT ?, ?'),
			array(
				FRONTEND_LANGUAGE,
				'N', 'Y',
				FrontendModel::getUTCDate('Y-m-d H:i') . ':00',
				(string) $categoryURL,
				(int) $offset,
				(int) $limit
			),
			'id'
		);
		
		
		// no results?
		if(empty($return)) return array();

		// init var
		$albumLink = FrontendNavigation::getURLForBlock('photogallery', 'detail');
		$categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');

		// loop
		foreach($return as &$row)
		{
			// URLs
			$row['full_url'] = $albumLink . '/' . $row['url'];
			$row['image'] =  (array) $db->getRecord('SELECT i.filename, m.url, c.title, c.text, i.set_id, c.data
														FROM  photogallery_sets_images AS i
														INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
														INNER JOIN meta AS m ON m.id = c.meta_id
														WHERE i.set_id = ? AND c.language = ? AND i.hidden = ?
														ORDER BY sequence DESC LIMIT 1',
														array((int) $row['set_id'], FRONTEND_LANGUAGE, 'N'));

			$row['category_ids'] = ($row['category_ids'] != '') ? (array) explode(',', $row['category_ids']) : null;

			if($row['category_ids'] !== null)
			{
				$row['categories'] = (array) $db->getRecords('SELECT i.title, i.id, m.url
																FROM photogallery_categories as i
																INNER JOIN meta as m ON m.id = i.meta_id
																WHERE i.id IN (' . implode(', ', $row['category_ids']) . ')
																ORDER BY i.sequence ASC
															');

				foreach($row['categories']as &$category)
				{
					$category['full_url'] = $categoryLink . '/' . $category['url'];
				}
			}
		}
		
		// return
		return $return;
	}

	/**
	 * Get all breadcrumbs for a specific category
	 *
	 * @return array
	 */
	public static function getBreadcrumbsForCategory($id = 0, $depth = 0)
	{
		if(!$id) return array();

		// get db
		$db = FrontendModel::getContainer()->get('database');

		// get category
		$category = self::getCategoryById((int) $id);
		if($depth == 0) $category['selected'] = true;
		else if($depth == 1) $category['beforeSelected'] = true;
		else $category['selected'] = $category['selected'] = false;
		if ((bool) $category['parent_id'] == "0") $category['firstChild'] = true;
		$output[] = $category;
		
		if($category['parent_id']) $output = array_merge($output, self::getBreadcrumbsForCategory($category['parent_id'], $depth + 1));
		//else $output[] = array("root" => true, "title" => Spoonfilter::ucfirst(FL::lbl('CategoryRoot')), "beforeSelected" => isset($category['selected']) && $category['selected'] ? true : false);
		
		return $output;
	}

	/**
	 * Get all data for a given id
	 *
	 * @param int $id The id of the category to fetch.
	 * @return array
	 */
	public static function getCategoryById($id)
	{
		// get db
		$db = FrontendModel::getContainer()->get('database');

		$category = (array) $db->getRecord(
			'SELECT i.*,
				m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
				m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
				m.title AS page_title, m.title_overwrite AS page_title_overwrite,
				m.url, m.url_overwrite
			FROM photogallery_categories AS i
				INNER JOIN meta AS m ON m.id = i.meta_id
			WHERE i.id = ?',
			array(
				(int) $id
			)
		);

		return $category;
		
	}

	/**
	 * Get all items in a category (at least a chunk)
	 *
	 * @param string $categoryURL The URL of the category to retrieve the posts for.
	 * @return array
	 */
	public static function getAllForCategoryNavigation($categoryURL)
	{
		$db = FrontendModel::getContainer()->get('database');
		
		// get the items
		$return = (array) $db->getRecords(
			'SELECT i.id, i.language, i.title, i.introduction, i.text, i.num_images, i.set_id, 
				c.title AS category_title, cm.url AS category_url,
				UNIX_TIMESTAMP(i.publish_on) AS publish_on,
				m.url, GROUP_CONCAT(ab.category_id) AS category_ids
			FROM photogallery_albums AS i
				INNER JOIN photogallery_categories_albums AS a ON i.id = a.album_id
				LEFT OUTER JOIN photogallery_categories_albums AS ab ON i.id = ab.album_id
				INNER JOIN photogallery_categories AS c ON a.category_id = c.id
				INNER JOIN meta AS m ON i.meta_id = m.id
				INNER JOIN meta AS cm ON cm.id = c.meta_id
			WHERE  i.language = ?
				AND i.hidden = ?  AND ishow_in_albums = ?
				AND i.publish_on <= ?
				AND cm.url = ?
				AND i.num_images > 0
			GROUP BY i.id
			ORDER BY i.sequence ASC',
			array(
				FRONTEND_LANGUAGE,
				'N', 'Y',
				FrontendModel::getUTCDate('Y-m-d H:i') . ':00',
				(string) $categoryURL
			),
			'id'
		);
		
		
		// no results?
		if(empty($return)) return array();

		// init var
		$albumLink = FrontendNavigation::getURLForBlock('photogallery', 'detail');
		$categoryLink = FrontendNavigation::getURLForBlock('photogallery', 'category');

		// loop
		foreach($return as &$row)
		{
			// URLs
			$row['full_url'] = $albumLink . '/' . $row['url'];
			$row['image'] =  (array) $db->getRecord('SELECT i.filename, m.url, c.title, c.text, i.set_id, c.data
														FROM  photogallery_sets_images AS i
														INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
														INNER JOIN meta AS m ON m.id = c.meta_id
														WHERE i.set_id = ? AND c.language = ? AND i.hidden = ?
														ORDER BY sequence DESC LIMIT 1',
														array((int) $row['set_id'], FRONTEND_LANGUAGE, 'N'));

			$row['category_ids'] = ($row['category_ids'] != '') ? (array) explode(',', $row['category_ids']) : null;

			if($row['category_ids'] !== null)
			{
				$row['categories'] = (array) $db->getRecords('SELECT i.title, i.id, m.url
																FROM photogallery_categories as i
																INNER JOIN meta as m ON m.id = i.meta_id
																WHERE i.id IN (' . implode(', ', $row['category_ids']) . ')
																ORDER BY i.sequence ASC
															');

				foreach($row['categories']as &$category)
				{
					$category['full_url'] = $categoryLink . '/' . $category['url'];
				}
			}
		}
		
		// return
		return $return;
	}
	
	public static function search(array $ids)
	{
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.id, i.title, i.introduction, i.text, m.url
			 FROM photogallery_albums AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.hidden = ?  AND i.show_in_albums = ? AND i.language = ? AND i.publish_on <= ? AND i.id IN (' . implode(',', $ids) . ') AND i.num_images_not_hidden > ?',
			array('N','Y', FRONTEND_LANGUAGE, date('Y-m-d H:i') . ':00', 0), 'id'
		);

		$url = FrontendNavigation::getURLForBlock('photogallery', 'detail');
		
		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] = $url . '/' . $item['url'];
		}

		// return
		return $items;
	}
	
	public static function getRelatedByCategories($URL, $limit = 10, $offset = 0)
	{
		// Get item with 
		$item = self::get($URL);
		
		if(empty($item['category_ids'])) return array();
		
		$db = FrontendModel::getContainer()->get('database');
		
		// Get other projects
		$related = (array) $db ->getRecords(
			'SELECT i.id, i.title, i.introduction, i.text, m.url, i.set_id
			 FROM photogallery_categories_albums AS c
			 INNER JOIN photogallery_albums as i ON i.id = c.album_id
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.hidden = ?  AND ishow_in_albums = ? AND i.language = ? AND i.num_images_not_hidden > ? AND i.publish_on <= ? AND c.category_id IN (' . implode(',', $item['category_ids']) . ') AND i.id != ?
			 LIMIT ?, ?',
			array('N', 'Y', FRONTEND_LANGUAGE, 0, date('Y-m-d H:i') . ':00', (int) $item['id'], (int) $offset, (int) $limit), 'id'
		);
		
		foreach($related as &$row)
		{
			$row['full_url'] = FrontendNavigation::getURLForBlock('photogallery', 'detail') . '/' .  $row['url'];
			
			$row['image'] =  (array) $db->getRecord('SELECT i.filename, m.url, c.title, c.text, i.set_id, c.data
														FROM  photogallery_sets_images AS i
														INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
														INNER JOIN meta AS m ON m.id = c.meta_id
														WHERE i.set_id = ? AND c.language = ? AND i.hidden = ?
														ORDER BY sequence ASC LIMIT 1',
														array((int) $row['set_id'], FRONTEND_LANGUAGE, 'N'));

			$row['image']['data'] = $row['image']['data'] != null ? unserialize($row['image']['data']) : null;
		}
		
		return $related;
	}
	
	public static function getRelatedByTags($URL, $limit = 10, $offset = 0)
	{
		// Get item with 
		$item = self::get($URL);
		
		if(empty($item)) return array();
		
		$db = FrontendModel::getContainer()->get('database');
		
		$related = FrontendTagsModel::getRelatedItemsByTags((int) $item['id'], 'photogallery', 'photogallery');
		
		if(empty($related)) return array();
		
		// Get other projects
		$related = (array) $db->getRecords(
			'SELECT i.id, i.title, i.introduction, i.text, m.url, i.set_id
			 FROM photogallery_categories_albums AS c
			 INNER JOIN photogallery_albums as i ON i.id = c.album_id
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.hidden = ?  AND i.show_in_albums = ? AND i.language = ? AND i.num_images_not_hidden > ? AND i.publish_on <= ? AND i.id IN (' . implode(',', $related) . ') AND i.id != ?
			 LIMIT ?, ?',
			array('N','Y', FRONTEND_LANGUAGE, 0, date('Y-m-d H:i') . ':00', (int) $item['id'], (int) $offset, (int) $limit), 'id'
		);
		
		foreach($related as &$row)
		{
			$row['full_url'] = FrontendNavigation::getURLForBlock('photogallery', 'detail') . '/' .  $row['url'];
			
			$row['image'] =  (array) $db->getRecord('SELECT i.filename, m.url, c.title, c.text, i.set_id, c.data
														FROM  photogallery_sets_images AS i
														INNER JOIN photogallery_sets_images_content AS c ON i.id = c.set_image_id
														INNER JOIN meta AS m ON m.id = c.meta_id
														WHERE i.set_id = ? AND c.language = ? AND i.hidden = ?
														ORDER BY sequence ASC LIMIT 1',
														array((int) $row['set_id'], FRONTEND_LANGUAGE, 'N'));
														
			$row['image']['data'] = $row['image']['data'] != null ? unserialize($row['image']['data']) : null;
		}
		
		return $related;
	}
}