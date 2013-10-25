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

		// hide contenTitle, in the template the title is wrapped with an inverse-option
		$this->tpl->assign('hideContentTitle', true);

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
		// init
		$this->category_view = false;
		$this->categories_view = false;
		
		// get categories
		$categories = FrontendPhotogalleryModel::getAllCategories();
		$possibleCategories = array();
		foreach($categories as $category) $possibleCategories[$category['url']] = $category['id'];

		// requested category
		$requestedCategory = SpoonFilter::getValue($this->URL->getParameter(1, 'string'), array_keys($possibleCategories), 'false');

		// requested page
		$requestedPage = $this->URL->getParameter('page', 'int', 1);

		// get resolution
		$thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'album_overview_thumbnail');

   		$this->tpl->assign('modulePhotogalleryCategoryThumbnailResolution', $thumbnail_resolution);

		// validate category
		if($requestedCategory == 'false')
		{
			$this->categories_view = true;

			//$this->redirect(FrontendNavigation::getURL(404));

			// set URL and limit
			$this->pagination['url'] = FrontendNavigation::getURLForBlock('photogallery', 'category') . '/' . $requestedCategory;
			$this->pagination['limit'] = FrontendModel::getModuleSetting('photogallery', 'overview_categories_number_of_items', 10);

			// populate count fields in pagination
			$this->pagination['num_items'] = FrontendPhotogalleryModel::getAllForCategoryCount($requestedCategory);
			$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

			// redirect if the request page doesn't exists
			if($requestedPage > $this->pagination['num_pages'] && $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

			// populate calculated fields in pagination
			$this->pagination['requested_page'] = $requestedPage;
			$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

			// set categories
			$this->categories = $categories;
			
			// set items per category
			foreach($this->categories as $cat_key => $category_row)
			{
				// add albums
				$this->categories[$cat_key]['albums'] = FrontendPhotogalleryModel::getAllForCategory(
					$category_row['url'],
					$this->pagination['limit'],
					$this->pagination['offset']
				);

				// loop items
				foreach($this->categories[$cat_key]['albums'] as $album_cat_key => $album_cat_row)
				{
					$this->categories[$cat_key]['albums'][$album_cat_key]['tags'] = FrontendTagsModel::getForItem($this->getModule(), $album_cat_row['id']);
				}
			}
		}
		else
		{
			$this->category_view = true;

			// set category
			$this->category = $categories[$possibleCategories[$requestedCategory]];

			// set URL and limit
			$this->pagination['url'] = FrontendNavigation::getURLForBlock('photogallery', 'category') . '/' . $requestedCategory;
			$this->pagination['limit'] = FrontendModel::getModuleSetting('photogallery', 'overview_categories_number_of_items', 10);

			// populate count fields in pagination
			$this->pagination['num_items'] = (int) FrontendPhotogalleryModel::getAllForCategoryCount($requestedCategory);
			$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

			// redirect if the request page doesn't exists
			//if($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

			// populate calculated fields in pagination
			$this->pagination['requested_page'] = $requestedPage;
			$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

			// get articles
			$this->items = FrontendPhotogalleryModel::getAllForCategory(
				$requestedCategory,
				$this->pagination['limit'],
				$this->pagination['offset']
			);
			
			foreach($this->items as &$row)
			{
				$row['tags'] = FrontendTagsModel::getForItem($this->getModule(), $row['id']);
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
		if($this->category)
		{
			$this->breadcrumb->addElement(SpoonFilter::ucfirst(FL::getLabel('Category')), FrontendNavigation::getURLForBlock('photogallery', 'category'));
			//$this->breadcrumb->addElement($this->category['label']);

			// get parent, parents parent, etc…
			$this->breadcrumbs = array_reverse(FrontendPhotogalleryModel::getBreadcrumbsForCategory($this->category['id']));
			
			// add breadcrumbs one by one
			foreach($this->breadcrumbs as $breadcrumb) $this->breadcrumb->addElement($breadcrumb['title'], FrontendNavigation::getURLForBlock('photogallery', 'category') . '/' . $breadcrumb['url']);
		}
		else
		{
			$this->breadcrumb->addElement(SpoonFilter::ucfirst(FL::getLabel('Categories')));
		}

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

		// assign
		$this->tpl->assign('blockPhotogalleryCategoryView', $this->category_view);
		$this->tpl->assign('blockPhotogalleryCategory', !empty($this->category) ? $this->category : array());
		
		$this->tpl->assign('blockPhotogalleryCategoriesView', $this->categories_view);
		$this->tpl->assign('blockPhotogalleryCategories', !empty($this->categories) ? $this->categories : array());

		// assign articles
		$this->tpl->assign('blockPhotogalleryCategoryAlbums', !empty($this->items) ? $this->items : array());

		// parse the pagination
		$this->parsePagination();

		$this->tpl->mapModifier('createimagephotogallery', array('FrontendPhotogalleryHelper', 'createImage'));
	}
}