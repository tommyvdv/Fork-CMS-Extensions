<?php

/*
 * This file is part of the products module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This installer
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class ProductsInstaller extends ModuleInstaller
{
	/**
	 * Install the module
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'products' as a module
		$this->addModule('products', 'The multilingual photogallery with dynamic widgets.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'products');

		// action rights
		$this->setActionRights(1, 'products', 'add');
		$this->setActionRights(1, 'products', 'add_category');
		$this->setActionRights(1, 'products', 'add_images_choose');
		$this->setActionRights(1, 'products', 'add_images_existing');
		$this->setActionRights(1, 'products', 'add_images_upload');
		$this->setActionRights(1, 'products', 'add_images_upload_zip');
		$this->setActionRights(1, 'products', 'add_widget_categories');
		$this->setActionRights(1, 'products', 'add_widget_choose');
		$this->setActionRights(1, 'products', 'add_widget_lightbox');
		$this->setActionRights(1, 'products', 'add_widget_paged');
		$this->setActionRights(1, 'products', 'add_widget_related_by_categories');
		$this->setActionRights(1, 'products', 'add_widget_related_by_tags');
		$this->setActionRights(1, 'products', 'add_widget_slideshow');
		$this->setActionRights(1, 'products', 'index');
		$this->setActionRights(1, 'products', 'categories');
		$this->setActionRights(1, 'products', 'create_amazon_s3_cronjobs');
		$this->setActionRights(1, 'products', 'delete');
		$this->setActionRights(1, 'products', 'delete_category');
		$this->setActionRights(1, 'products', 'delete_extra');
		$this->setActionRights(1, 'products', 'delete_image');
		$this->setActionRights(1, 'products', 'edit');
		$this->setActionRights(1, 'products', 'edit_category');
		$this->setActionRights(1, 'products', 'edit_image');
		$this->setActionRights(1, 'products', 'edit_module');
		$this->setActionRights(1, 'products', 'edit_widget_categories');
		$this->setActionRights(1, 'products', 'edit_widget_lightbox');
		$this->setActionRights(1, 'products', 'edit_widget_paged');
		$this->setActionRights(1, 'products', 'edit_widget_related_by_categories');
		$this->setActionRights(1, 'products', 'edit_widget_related_by_tags');
		$this->setActionRights(1, 'products', 'edit_widget_slideshow');
		$this->setActionRights(1, 'products', 'extras');
		$this->setActionRights(1, 'products', 'index');
		$this->setActionRights(1, 'products', 'mass_action');
		$this->setActionRights(1, 'products', 'add_category');
		$this->setActionRights(1, 'products', 'images_sequence');
		$this->setActionRights(1, 'products', 'sequence');
		$this->setActionRights(1, 'products', 'category_sequence');
		
		// make module searchable
		$this->makeSearchable('products');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationId = $this->setNavigation($navigationModulesId, 'Products', 'products/index',  array(
			'products/add_images_upload',
			'products/add_images_upload_zip',
			'products/add_images_choose',
			'products/add_images_existing',
			'products/edit_image',
			'products/add',
			'products/edit',
		));

		$this->setNavigation($navigationId, 'index', 'products/index', array(
			'products/index'
		));

		$this->setNavigation($navigationId, 'Categories', 'products/categories', array(
			'products/add_category',
			'products/edit_category'
		));

		$this->setNavigation($navigationId, 'Extras', 'products/extras', array(
			'products/add_widget_choose',
			'products/edit_widget_slideshow',
			'products/edit_widget_lightbox',
			'products/edit_widget_paged',
			'products/edit_widget_categories',
			'products/add_widget_categories',
			'products/edit_block',
			'products/add_widget_slideshow',
			'products/add_widget_lightbox',
			'products/add_widget_paged',
			'products/edit_module',
			'products/add_widget_related_by_categories',
			'products/edit_widget_related_by_categories',
			'products/add_widget_related_by_tags',
			'products/edit_widget_related_by_tags',
		));
		
		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Products', 'products/settings');
		
		// Settings
		$this->setSetting('products', 'awsAccessKey', '');
		$this->setSetting('products', 'awsSecretKey', '');
		$this->setSetting('products', 's3_url', '');
		$this->setSetting('products', 's3_account', false);
		$this->setSetting('products', 's3_region', '');
		
		// ping service (feedburner)
		$this->setSetting('products', 'ping_services', false);

		$db = $this->getDB();

		// Block
		$extraId = $db->insert('products_extras', array('data' => serialize(array('action' => 'lightbox', 'display' => 'index')), 'action' => null, 'kind' => 'module', 'allow_delete' => 'N', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('products_extras_resolutions', array('extra_id' => $extraId, 'width' => 1200, 'height' => 1200, 'method' => 'resize', 'kind' => 'large'));
		$db->insert('products_extras_resolutions', array('extra_id' => $extraId, 'width' => 125, 'height' => 125, 'method' => 'crop', 'kind' => 'product_detail_overview_thumbnail'));
		$db->insert('products_extras_resolutions', array('extra_id' => $extraId, 'width' => 200, 'height' => 200, 'method' => 'crop', 'kind' => 'product_overview_thumbnail'));
		
		// Module Extra
		$extraBlockId = $this->insertExtra('products', 'block', 'Products', null, serialize(array('action' => 'lightbox', 'display' => 'index', 'extra_id' => $extraId)));

		// Slideshow
		$extraId = $db->insert('products_extras', array('action' => 'slideshow', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('products_extras_resolutions', array('extra_id' => $extraId, 'width' => 600, 'height' => 350, 'method' => 'crop', 'kind' => 'large'));

		// Lightbox
		$extraId = $db->insert('products_extras', array('action' => 'lightbox', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('products_extras_resolutions', array('extra_id' => $extraId, 'width' => 800, 'height' => 600, 'method' => 'resize', 'kind' => 'large'));
		$db->insert('products_extras_resolutions', array('extra_id' => $extraId, 'width' => 75, 'height' => 75, 'method' => 'crop', 'kind' => 'thumbnail'));

		// Paged
		$extraId = $db->insert('products_extras', array('action' => 'paged', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('products_extras_resolutions', array('extra_id' => $extraId, 'width' => 75, 'height' => 75, 'method' => 'crop', 'kind' => 'thumbnail'));

		// Category widget
		$extraId = $db->insert('products_extras', array('action' => 'categories', 'kind' => 'widget', 'allow_delete' => 'Y', 'edited_on' => gmdate('Y-m-d H:i:00'), 'created_on' => gmdate('Y-m-d H:i:00')));
		$db->insert('products_extras_resolutions', array('extra_id' => $extraId, 'width' => 500, 'height' => 350, 'method' => 'crop', 'kind' => 'large'));
		
		// Widgets
		$this->insertExtra('products', 'widget', 'CategoryNavigation', 'category_navigation');
		$this->insertExtra('products', 'widget', 'RelatedListByCategories', 'related_list_by_categories');
		$this->insertExtra('products', 'widget', 'RelatedListByTags', 'related_list_by_tags');
		
		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// feedburner URL
			$this->setSetting('products', 'feedburner_url_' . $language, '');

			// RSS settings
			$this->setSetting('products', 'rss_meta_' . $language, true);
			$this->setSetting('products', 'rss_title_' . $language, 'RSS');
			$this->setSetting('products', 'rss_description_' . $language, '');
		}
		
		// Insert page
		self::insertProductsPage('Photogallery', $extraBlockId );
		
		// Do API Call
		self::doApiCall();
	}

	private function insertProductsPage($title, $extraId)
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
				'name' => 'products',
				'version' => '2.1',
				'email' => SpoonSession::get('email'),
				'license_name' => '',
				'license_key' => '',
				'license_domain' => ''
			);
		
			// call
			$api = new ApiCall();
			$api->setApiURL('http://www.fork-cms-extensions.com/api/1.0');
			$return = $api->doCall('products.insertProductInstallation', $parameters, false);
			$this->setSetting('products', 'api_call_id', (string) $return->data->id);
		} 
		catch(Exception $e) 
		{}
	}
}
