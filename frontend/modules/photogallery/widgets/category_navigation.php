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
		// Get categories html
		$this->navigation = FrontendPhotogalleryModel::buildCategoriesNavigation(0, $this->URL->getParameter(1));
	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function parse()
	{
		$this->tpl->assign('widgetPhotogalleryCategoryNavigation', $this->navigation);
		$this->tpl->assign('isRoot', !isset($this->data['id']) ? true : false);
		
	}
}
