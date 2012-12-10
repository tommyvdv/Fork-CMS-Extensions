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
class FrontendPhotogalleryCategory extends FrontendBaseBlock
{
	/**
	 * The items
	 *
	 * @var array
	 */
	private $items;

	/**
	 * The requested category
	 *
	 * @var array
	 */
	private $category;

	/**
	 * The pagination array
	 * It will hold all needed parameters, some of them need initialization
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
		// S3
		$this->amazonS3Account = FrontendPhotogalleryHelper::existsAmazonS3();
		
		// get categories
		$categories = FrontendPhotogalleryModel::getAllCategories();
		$possibleCategories = array();
		foreach($categories as $category) $possibleCategories[$category['url']] = $category['id'];

		// requested category
		$requestedCategory = SpoonFilter::getValue($this->URL->getParameter(1, 'string'), array_keys($possibleCategories), 'false');

		// requested page
		$requestedPage = $this->URL->getParameter('page', 'int', 1);

		// validate category
		if($requestedCategory == 'false') $this->redirect(FrontendNavigation::getURL(404));

		// set category
		$this->category = $categories[$possibleCategories[$requestedCategory]];

		// set URL and limit
		$this->pagination['url'] = FrontendNavigation::getURLForBlock('photogallery', 'category') . '/' . $requestedCategory;
		$this->pagination['limit'] = FrontendModel::getModuleSetting('photogallery', 'overview_categories_number_of_items', 10);

		// populate count fields in pagination
		$this->pagination['num_items'] = FrontendPhotogalleryModel::getAllForCategoryCount($requestedCategory);
		$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

		// redirect if the request page doesn't exists
		if($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

		// populate calculated fields in pagination
		$this->pagination['requested_page'] = $requestedPage;
		$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

		// get articles
		$this->items = FrontendPhotogalleryModel::getAllForCategory($requestedCategory, $this->pagination['limit'], $this->pagination['offset']);
		
		$thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'album_overview_thumbnail');
			
		foreach($this->items as &$row)
		{
			$row['tags'] = FrontendTagsModel::getForItem($this->getModule(), $row['id']);
			if(!empty($row['image']))
			{
				// No account has been linked
				if(!$this->amazonS3Account)
				{
					$row['image']['thumbnail_url'] =  FRONTEND_FILES_URL . '/' . $this->getModule() . '/sets/frontend/' . $row['image']['set_id'] . '/' . $thumbnail_resolution['width'] . 'x' . $thumbnail_resolution['height'] . '_' . $thumbnail_resolution['method'] . '/' . $row['image']['filename'];
				}
				elseif($this->amazonS3Account)
				{
					// Thumbnail res.
					$row['image']['thumbnail_url']  = FrontendPhotogalleryHelper::getImageURL(
						$this->getModule() . '/sets/frontend/' . $row['image']['set_id'] . '/' . $thumbnail_resolution['width'] . 'x' . $thumbnail_resolution['height'] . '_' . $thumbnail_resolution['method'] . '/' . $row['image']['filename']
					);
				}
				else
				{
					$row['image']['thumbnail_url'] = array();
				}
			}
		}
	}

	/**
	 * Parse the data into the template
	 *
	 * @return void
	 */
	private function parse()
	{
		// add into breadcrumb
		$this->breadcrumb->addElement(SpoonFilter::ucfirst(FL::getLabel('Category')));
		$this->breadcrumb->addElement($this->category['label']);

		// set meta
		$this->header->setPageTitle($this->category['meta_title'], ($this->category['meta_title_overwrite'] == 'Y'));
		$this->header->addMetaDescription($this->category['meta_description'], ($this->category['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaKeywords($this->category['meta_keywords'], ($this->category['meta_keywords_overwrite'] == 'Y'));

		// advanced SEO-attributes
		if(isset($this->category['meta_data']['seo_index'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->category['meta_data']['seo_index']));
		if(isset($this->category['meta_data']['seo_follow'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->category['meta_data']['seo_follow']));
		
		// get RSS-link
		$rssLink = FrontendModel::getModuleSetting('photogallery', 'feedburner_url_' . FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('photogallery', 'rss');

		// add RSS-feed
		$this->header->addLink(array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => FrontendModel::getModuleSetting('photogallery', 'rss_title_' . FRONTEND_LANGUAGE), 'href' => $rssLink), true);
	

		// assign category
		$this->tpl->assign('blockPhotogalleryCategory', $this->category);

		// assign articles
		$this->tpl->assign('blockPhotogalleryCategoryAlbums', $this->items);

		// parse the pagination
		$this->parsePagination();
	}
}