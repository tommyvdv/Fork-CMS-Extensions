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
class FrontendPhotogalleryIndex extends FrontendBaseBlock
{
	/**
	 * The records
	 *
	 * @var	array
	 */
	private $items;

	/**
	 * The pagination array
	 * It will hold all needed parameters, some of them need initialization.
	 *
	 * @var	array
	 */
	protected $pagination = array('limit' => 10, 'offset' => 0, 'requested_page' => 1, 'num_items' => null, 'num_pages' => null);

	/**
	 * Execute the extra
	 *
	 * @return void
	 */
	public function execute()
	{		
		// call the parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// load the data
		$this->getData();

		// parse
		$this->parse();
	}

	/**
	 * Load the data, don't forget to validate the incoming data
	 *
	 * @return void
	 */
	private function getData()
	{		
		$thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'album_overview_thumbnail');

		$this->tpl->assign('modulePhotogalleryIndexResolution', $thumbnail_resolution);


		if(FrontendModel::getModuleSetting('photogallery', 'force_default_category_index', false) && FrontendModel::getModuleSetting('photogallery', 'default_category'))
		{
			$this->redirect(SITE_URL . FrontendNavigation::getURLForBlock('photogallery', 'category') . '/' . FrontendPhotogalleryModel::getCategoryUrlById(FrontendModel::getModuleSetting('photogallery', 'default_category')));
		}
		elseif($this->data['display'] == 'albums')
		{
			// requested page
			$requestedPage = $this->URL->getParameter('page', 'int', 1);

			// set URL and limit
			$this->pagination['url'] = FrontendNavigation::getURLForBlock('photogallery');
			$this->pagination['limit'] = FrontendModel::getModuleSetting('photogallery', 'overview_albums_number_of_items', 10);

			// populate count fields in pagination
			$this->pagination['num_items'] = FrontendPhotogalleryModel::getAlbumsCount();
			$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

			// num pages is always equal to at least 1
			if($this->pagination['num_pages'] == 0) $this->pagination['num_pages'] = 1;

			// redirect if the request page doesn't exist
			if($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

			// populate calculated fields in pagination
			$this->pagination['requested_page'] = $requestedPage;
			$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

			// get articles
			$this->items = FrontendPhotogalleryModel::getAll($this->pagination['limit'], $this->pagination['offset']);

			foreach($this->items as &$item)
			{
				$item['tags'] = FrontendTagsModel::getForItem($this->getModule(), $item['id']);
			}
			
			// assign
			$this->tpl->assign('modulePhotogalleryAlbums', $this->items);

			// parse the pagination
			$this->parsePagination();
			
		}
		elseif($this->data['display'] == 'categories')
		{
			$categories = FrontendPhotogalleryModel::getAllCategoriesWithImage();

			$this->tpl->assign('modulePhotogalleryCategories', $categories);
		}
	}

	/**
	 * Parse the data into the template
	 *
	 * @return void
	 */
	private function parse()
	{
		$this->tpl->assign('display' . SpoonFilter::toCamelCase($this->data['display']), true);
		
		
		// get RSS-link
		$rssLink = FrontendModel::getModuleSetting('photogallery', 'feedburner_url_' . FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('photogallery', 'rss');
	
		// add RSS-feed
		$this->header->addLink(array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => FrontendModel::getModuleSetting('photogallery', 'rss_title_' . FRONTEND_LANGUAGE), 'href' => $rssLink), true);
		
		$this->tpl->mapModifier('createimagephotogallery', array('FrontendPhotogalleryHelper', 'createImage'));
	}
}