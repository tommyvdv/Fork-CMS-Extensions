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
class FrontendPhotogalleryWidgetSlideshow extends FrontendBaseWidget
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
		$this->record = FrontendPhotogalleryModel::getAlbum($this->data);
		$this->large_resolution = false;
		
		if(!empty($this->record))
		{
			// get tags
			$this->record['tags'] = FrontendTagsModel::getForItem($this->getModule(), $this->record['id']);

			$this->large_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'large');

			foreach($this->record['images'] as &$image)
			{
				$image['large_url'] = FrontendPhotogalleryHelper::getImageURL($this->getModule(), $image, $this->large_resolution);
			}
			
			$this->tpl->assign('widgetPhotogallerySlideshow', $this->record);		}
	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function parse()
	{

		$this->header->addCSS('/frontend/modules/' . $this->getModule() . '/layout/css/photogallery.css');

		$this->header->addCSS(
			FrontendPhotogalleryHelper::getPathJS('/flexslider/' . FrontendPhotogalleryModel::FLEXSLIDER_VERSION . '/flexslider.css', $this->getModule())
		);
		
		$this->header->addJS(
				FrontendPhotogalleryHelper::getPathJS('/flexslider/' . FrontendPhotogalleryModel::FLEXSLIDER_VERSION . '/jquery.flexslider.js', $this->getModule()),
				false
		);

		$this->header->addJS(
				FrontendPhotogalleryHelper::getPathJS('/flexslider-init.js', $this->getModule())
		);	

	}
}