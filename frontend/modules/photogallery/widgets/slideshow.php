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
		$this->amazonS3Account = FrontendPhotogalleryHelper::existsAmazonS3();
		$this->record = FrontendPhotogalleryModel::getAlbum($this->data);
		$this->large_resolution = false;
		
		if(!empty($this->record))
		{
			// get tags
			$this->record['tags'] = FrontendTagsModel::getForItem($this->getModule(), $this->record['id']);

			$this->large_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'large');

			// No account has been linked
			if(!$this->amazonS3Account)
			{
				foreach($this->record['images'] as &$image)
				{
					$image['large_url'] = FRONTEND_FILES_URL . '/' . $this->getModule() . '/sets/frontend/' . $image['set_id'] . '/' . $this->large_resolution['width'] . 'x' . $this->large_resolution['height'] . '_' . $this->large_resolution['method'] . '/' . $image['filename'];
				}
			}
			elseif($this->amazonS3Account)
			{
				foreach($this->record['images'] as &$image)
				{
					// Large res.
					$image['large_url']  = FrontendPhotogalleryHelper::getImageURL(
						$this->getModule() . '/sets/frontend/' . $image['set_id'] . '/' . $this->large_resolution['width'] . 'x' . $this->large_resolution['height'] . '_' . $this->large_resolution['method'] . '/' . $image['filename']
					);
				}
			}
			else
			{
				// Reset
				$this->record['images'] = array();
			}
			
			$this->tpl->assign('widgetPhotogallerySlideshow', $this->record);
			$this->tpl->assign('large_resolution', $this->large_resolution);
		}
	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function parse()
	{
		$this->header->addCSS('/frontend/modules/' . $this->getModule() . '/layout/css/slideshow.css');
		
		$this->header->addJS(
				FrontendPhotogalleryHelper::getPathJS('/cycle/2.995/jquery.cycle.all.js', $this->getModule()),
				false
		);

		$this->header->addJS(
				FrontendPhotogalleryHelper::getPathJS('/cycle-init.js', $this->getModule())
		);	

	}
}