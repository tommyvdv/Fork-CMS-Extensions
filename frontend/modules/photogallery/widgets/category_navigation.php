<?php

/*
 * This file is part of the projects module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class FrontendPhotogalleryWidgetCategoryNavigation extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 *
	 * @return void
	 */
	public function execute()
	{
		// parent execute
		parent::execute();
		
		// data
		$this->getData();
		
		// load template
		$this->loadTemplate();
		
		// parse
		$this->parse();
	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function getData()
	{	
		// Get categories and their projects
		$this->categories =  FrontendPhotogalleryModel::getAllCategories(isset($this->data['id']) ? $this->data['id'] : 0);
	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function parse()
	{
		$this->header->addCSS('/frontend/modules/' . $this->getModule() . '/layout/css/photogallery.css');

		$onDetailURL = $this->URL->getParameter(0) == FL::getAction('Detail');
		$onCategoryURL = $this->URL->getParameter(0) == FL::getAction('Category');

		// Are we on a detail?
		if($onDetailURL)
		{
			foreach($this->categories as &$category)
			{
				$this->record = FrontendPhotogalleryModel::get($this->URL->getParameter(1));
				if(!empty($this->record))
				{
					$category['items'] = FrontendPhotogalleryModel::getAllForCategoryNavigation($category['url'], null, null, true);
					$category['selected'] = false;
					if(isset($this->record['category_id'])) $category['selected'] = (int) $category['id'] == (int) $this->record['category_id'] ? true : false;

					foreach($category['items'] as &$item)
					{
						if((int) $item['id'] == (int) $this->record['id'])
						{
							$item['selected'] = true;
							$category['selected'] = true;
						}
					}
				}
			}
		}
			
		// Are we on a category detail?
		if($onCategoryURL)
		{
			foreach($this->categories as &$category)
			{
				$category['items'] = FrontendPhotogalleryModel::getAllForCategoryNavigation($category['url']);
				$category['selected'] = (string) $category['url'] ==  (string) $this->URL->getParameter(1) ? true : false;
			}
		}
		
		$this->tpl->assign('widgetPhotogalleryCategoryNavigation', $this->categories);
		$this->tpl->assign('widgetPhotogalleryCategoryNavigationParentId', (int) (isset($this->data['id']) ? $this->data['id'] : 0));
	}
}