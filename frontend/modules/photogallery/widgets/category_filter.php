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
class FrontendJeramWidgetCategoryFilter extends FrontendBaseWidget
{
	private $hideWidget = false;

	/**
	 * The filter array
	 *
	 * @var array
	 */
	private $filter;

	/**
	 * Sets the filter based on the $_GET array.
	 */
	private function setFilter()
	{
		$this->filter['city'] = $this->URL->getParameter('city');
		$this->filter['rooms'] = $this->URL->getParameter('rooms');
		$this->filter['minRentalPrice'] = $this->URL->getParameter('minRentalPrice');
		$this->filter['maxRentalPrice'] = $this->URL->getParameter('maxRentalPrice');
		$this->filter['availableFrom'] = $this->URL->getParameter('availableFrom');

		// build query for filter
		$this->filterQuery = '&' . http_build_query($this->filter);
	}

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
		$this->frm = new FrontendForm('jeramWidgetCategoryFilterForm', null, 'get');

		$this->cities = FrontendJeramModel::getCitiesForDropdown();
		$this->frm->addDropdown('city', $this->cities, null)->setDefaultElement(null);

		$this->frm->addText('rooms');
		$this->frm->addText('minRentalPrice');
		$this->frm->addText('maxRentalPrice');
		$this->frm->addText('availableFrom', null, null, 'inputText inputDatefieldNormal');		// datepicker
		$this->frm->getField('availableFrom', null)->setAttribute('data-mask', 'dd/mm/yy');			// date mask
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
