<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This installer
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class PhotogalleryInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'photogallery' as a module
		$this->addModule('photogallery', 'The multilingual photogallery with dynamic widgets.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'photogallery');

		// action rights
		$this->setActionRights(1, 'photogallery', 'add');
		$this->setActionRights(1, 'photogallery', 'add_category');
		$this->setActionRights(1, 'photogallery', 'add_images_choose');
		$this->setActionRights(1, 'photogallery', 'add_images_existing');
		$this->setActionRights(1, 'photogallery', 'add_images_upload');
		$this->setActionRights(1, 'photogallery', 'add_images_upload_zip');
		$this->setActionRights(1, 'photogallery', 'add_widget_categories');
		$this->setActionRights(1, 'photogallery', 'add_widget_choose');
		$this->setActionRights(1, 'photogallery', 'add_widget_lightbox');
		$this->setActionRights(1, 'photogallery', 'add_widget_paged');
		$this->setActionRights(1, 'photogallery', 'add_widget_related_by_categories');
		$this->setActionRights(1, 'photogallery', 'add_widget_related_by_tags');
		$this->setActionRights(1, 'photogallery', 'add_widget_slideshow');
		$this->setActionRights(1, 'photogallery', 'index');
		$this->setActionRights(1, 'photogallery', 'categories');
		$this->setActionRights(1, 'photogallery', 'delete');
		$this->setActionRights(1, 'photogallery', 'delete_category');
		$this->setActionRights(1, 'photogallery', 'delete_extra');
		$this->setActionRights(1, 'photogallery', 'delete_image');
		$this->setActionRights(1, 'photogallery', 'edit');
		$this->setActionRights(1, 'photogallery', 'edit_category');
		$this->setActionRights(1, 'photogallery', 'edit_image');
		$this->setActionRights(1, 'photogallery', 'edit_module');
		$this->setActionRights(1, 'photogallery', 'edit_widget_categories');
		$this->setActionRights(1, 'photogallery', 'edit_widget_lightbox');
		$this->setActionRights(1, 'photogallery', 'edit_widget_paged');
		$this->setActionRights(1, 'photogallery', 'edit_widget_related_by_categories');
		$this->setActionRights(1, 'photogallery', 'edit_widget_related_by_tags');
		$this->setActionRights(1, 'photogallery', 'edit_widget_slideshow');
		$this->setActionRights(1, 'photogallery', 'extras');
		$this->setActionRights(1, 'photogallery', 'index');
		$this->setActionRights(1, 'photogallery', 'mass_action');
		$this->setActionRights(1, 'photogallery', 'add_category');
		$this->setActionRights(1, 'photogallery', 'images_sequence');
		$this->setActionRights(1, 'photogallery', 'sequence');
		$this->setActionRights(1, 'photogallery', 'category_sequence');
		$this->setActionRights(1, 'photogallery', 'add_images_upload_multiple');
		$this->setActionRights(1, 'photogallery', 'upload_image');
		$this->setActionRights(1, 'photogallery', 'settings');
		$this->setActionRights(1, 'photogallery', 'copy');

		// make module searchable
		$this->makeSearchable('photogallery');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationId = $this->setNavigation($navigationModulesId, 'Photogallery', 'photogallery/index',  array(
			'photogallery/add_images_upload',
			'photogallery/add_images_upload_multiple',
			'photogallery/add_images_upload_zip',
			'photogallery/add_images_choose',
			'photogallery/add_images_existing',
			'photogallery/edit_image',
			'photogallery/add',
			'photogallery/edit',
		));

		$this->setNavigation($navigationId, 'Albums', 'photogallery/index', array(
			'photogallery/index'
		));

		$this->setNavigation($navigationId, 'Categories', 'photogallery/categories', array(
			'photogallery/add_category',
			'photogallery/edit_category'
		));

		$this->setNavigation($navigationId, 'Extras', 'photogallery/extras', array(
			'photogallery/add_widget_choose',
			'photogallery/edit_widget_slideshow',
			'photogallery/edit_widget_lightbox',
			'photogallery/edit_widget_paged',
			'photogallery/edit_widget_categories',
			'photogallery/add_widget_categories',
			'photogallery/edit_block',
			'photogallery/add_widget_slideshow',
			'photogallery/add_widget_lightbox',
			'photogallery/add_widget_paged',
			'photogallery/edit_module',
			'photogallery/add_widget_related_by_categories',
			'photogallery/edit_widget_related_by_categories',
			'photogallery/add_widget_related_by_tags',
			'photogallery/edit_widget_related_by_tags',
		));
		
		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Photogallery', 'photogallery/settings');
		
		// Settings
		$this->setSetting('photogallery', 'awsAccessKey', '');
		$this->setSetting('photogallery', 'awsSecretKey', '');
		$this->setSetting('photogallery', 's3_url', '');
		$this->setSetting('photogallery', 's3_account', false);
		$this->setSetting('photogallery', 's3_region', '');
		
		// ping service (feedburner)
		$this->setSetting('photogallery', 'ping_services', false);

		$db = $this->getDB();

		// Block
		$blockDataSettings = array(
									'show_close_button' => 'false',
									'show_arrows' => 'true',
									'show_caption' => 'true',
									'caption_type' => 'outside',
									'padding' =>  25,
									'margin' => 20,
									'modal' => 'false',
									'show_hover_icon' => 'true',
									'close_click' => 'false',
									'media_helper' => 'true',
									'navigation_effect' => 'none',
									'open_effect' => 'none',
									'close_effect' => 'none',
									'play_speed' => 3000,
									'loop' => 'true',
									'show_thumbnails' => 'true',
									'thumbnails_position' => 'bottom',
									'thumbnail_navigation_width' => 50,
									'thumbnail_navigation_height' => 50,
									'show_overlay' => 'true',
									'overlay_color' => 'rgba(255, 255, 255, 0.85)'
							);

		$extraId = $db->insert('photogallery_extras', array('data' => serialize(array('action' => 'lightbox', 'display' => 'albums', 'settings' => $blockDataSettings)), 'action' => null, 'kind' => 'module', 'allow_delete' => 'N', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 1200, 'height' => 1200, 'method' => 'resize', 'kind' => 'large'));
		$db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 125, 'height' => 125, 'method' => 'crop', 'kind' => 'album_detail_overview_thumbnail'));
		$db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 200, 'height' => 200, 'method' => 'crop', 'kind' => 'album_overview_thumbnail'));
		
		// Module Extra
		$extraBlockId = $this->insertExtra('photogallery', 'block', 'Photogallery', null, serialize(array('action' => 'lightbox', 'display' => 'albums', 'extra_id' => $extraId)));


		// Slideshow
		/*
		$extraId = $db->insert('photogallery_extras', array('action' => 'slideshow', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 600, 'height' => 350, 'method' => 'crop', 'kind' => 'large'));
		*/
		
		// Lightbox
		/*
		$extraId = $db->insert('photogallery_extras', array('action' => 'lightbox', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 800, 'height' => 600, 'method' => 'resize', 'kind' => 'large'));
		$db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 75, 'height' => 75, 'method' => 'crop', 'kind' => 'thumbnail'));
		*/

		// Paged
		/*
		$extraId = $db->insert('photogallery_extras', array('action' => 'paged', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 75, 'height' => 75, 'method' => 'crop', 'kind' => 'thumbnail'));
		*/

		// Category widget
		/*
		$extraId = $db->insert('photogallery_extras', array('action' => 'categories', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('photogallery_extras_resolutions', array('extra_id' => $extraId, 'width' => 500, 'height' => 350, 'method' => 'crop', 'kind' => 'large'));
		*/
		
		// Widgets
		$this->insertExtra('photogallery', 'widget', 'CategoryNavigation', 'category_navigation');
		$this->insertExtra('photogallery', 'widget', 'RelatedListByCategories', 'related_list_by_categories');
		$this->insertExtra('photogallery', 'widget', 'RelatedListByTags', 'related_list_by_tags');
		
		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// feedburner URL
			$this->setSetting('photogallery', 'feedburner_url_' . $language, '');

			// RSS settings
			$this->setSetting('photogallery', 'rss_meta_' . $language, true);
			$this->setSetting('photogallery', 'rss_title_' . $language, 'RSS');
			$this->setSetting('photogallery', 'rss_description_' . $language, '');
		}
		
		// Insert page
		self::insertPhotogalleryPage('Photogallery', $extraBlockId );
		
		// Do API Call
		self::doApiCall();
	}

	private function insertPhotogalleryPage($title, $extraId)
	{
		// loop languages
		foreach($this->getLanguages() as $language)
		{
			
			// check if a page for blog already exists in this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												WHERE b.extra_id = ? AND p.language = ?',
												array($extraId, $language)))
			{
				$this->insertPage(
					array('title' =>  $title, 'language' => $language, 'type' => 'root'),
					null,
					array('extra_id' => $extraId, 'position' => 'main')
				);
			}
		}
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
				'name' => 'photogallery',
				'version' => '3.1.3',
				'email' => SpoonSession::get('email'),
				'license_name' => '',
				'license_key' => '',
				'license_domain' => ''
			);
		
			// call
			$api = new ApiCall();
			$api->setApiURL('http://www.fork-cms-extensions.com/api/1.0');
			$return = $api->doCall('products.insertProductInstallation', $parameters, false);
			$this->setSetting('photogallery', 'api_call_id', (string) $return->data->id);
		} 
		catch(Exception $e) 
		{}
	}
}