<?php

/*
 * This file is part of the projects module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 *
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class FrontendPhotogalleryWidgetFilter extends FrontendBaseWidget
{
	private $hideWidget = false;

	/**
	 * The filter array
	 *
	 * @var array
	 */
	private $filter;

	/**
	 * Execute the extra
	 *
	 * @return void
	 */
	public function execute()
	{
		// parent execute
		parent::execute();
		
		$this->setFilter();
		
		// data
		$this->getData();

		// load form
		$this->loadForm();
		
		// load template
		$this->loadTemplate();
		
		// parse
		$this->parse();
	}

	private function loadForm()
	{
		// the form
		$this->frm = new FrontendForm('photogalleryWidgetFilterForm', null, 'get');

		$allowedDepth = FrontendModel::getModuleSetting('photogallery', 'categories_depth', 0);
		$allowedDepthStart = FrontendModel::getModuleSetting('photogallery', 'categories_depth_start', 0);
		$this->categories = FrontendPhotogalleryModel::getCategoriesForDropdown(
			array(
				$allowedDepthStart,
				$allowedDepth == 0 ? 0 : $allowedDepth + 1
			)
		);
		
		$this->frm->addDropdown('categories', $this->categories, null, true, 'select categoriesBox', 'selectError categoriesBox');

		$this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');

		$this->frm->addText('title');
		$this->frm->addCheckbox('images');
		$this->frm->addText('publishedBefore', null, null, 'inputText inputDatefieldNormal'); // datepicker
		$this->frm->getField('publishedBefore', null)->setAttribute('data-mask', 'dd/mm/yy'); // date mask
		$this->frm->addText('publishedAfter', null, null, 'inputText inputDatefieldNormal'); // datepicker
		$this->frm->getField('publishedAfter', null)->setAttribute('data-mask', 'dd/mm/yy'); // date mask
	}

	/**
	 * Sets the filter based on the $_GET array.
	 */
	private function setFilter()
	{
		$this->filter['categories'] = $this->URL->getParameter('categories');
		$this->filter['title'] = $this->URL->getParameter('title');
		$this->filter['images'] = $this->URL->getParameter('images');
		$this->filter['publishedBefore'] = $this->URL->getParameter('publishedBefore');
		$this->filter['publishedAfter'] = $this->URL->getParameter('publishedAfter');

		// build query for filter
		$this->filterQuery = '&' . http_build_query($this->filter);
	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function getData()
	{
		
	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);
	}
}
