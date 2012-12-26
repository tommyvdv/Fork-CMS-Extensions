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
class FrontendPhotogalleryWidgetRelatedListByTags extends FrontendBaseWidget
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
		$this->records = array();
		
		// Are we on a detail
		if($this->URL->getParameter(0) == FL::getAction('Detail'))
		{
			$limit = FrontendModel::getModuleSetting('photogallery', 'related_list_tags_number_of_items', 10);
			
			if($this->URL->getParameter(1) !== null) $this->records = FrontendPhotogalleryModel::getRelatedByTags($this->URL->getParameter(1), $limit);
		}
	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function parse()
	{
		$this->tpl->assign('widgetPhotogalleryRelatedListByTags', $this->records);
	}
}