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
class FrontendPhotogalleryWidgetPaged extends FrontendBaseWidget
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
		
		if(!empty($this->record))
		{
			// get tags
			$this->record['tags'] = FrontendTagsModel::getForItem($this->getModule(), $this->record['id']);
		
			$thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'thumbnail');
			
			// No account has been linked
			if(!$this->amazonS3Account)
			{
				foreach($this->record['images'] as &$image)
				{
					$image['thumbnail_url'] = FRONTEND_FILES_URL . '/' . $this->getModule() . '/sets/frontend/' . $image['set_id'] . '/' . $thumbnail_resolution['width'] . 'x' . $thumbnail_resolution['height'] . '_' . $thumbnail_resolution['method'] . '/' . $image['filename'];
				}
			}
			elseif($this->amazonS3Account)
			{
				foreach($this->record['images'] as &$image)
				{
					// Thumbnail res.
					$image['thumbnail_url']  = FrontendPhotogalleryHelper::getImageURL(
						$this->getModule() . '/sets/frontend/' . $image['set_id'] . '/' . $thumbnail_resolution['width'] . 'x' . $thumbnail_resolution['height'] . '_' . $thumbnail_resolution['method'] . '/' . $image['filename']
					);
				}
			}
			else
			{
				// Reset
				$this->record['images'] = array();
			}
		}
	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function parse()
	{
		$this->tpl->assign('widgetPhotogalleryPaged', $this->record);
	}
}