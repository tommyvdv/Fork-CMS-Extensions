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
class FrontendPhotogalleryWidgetCategories extends FrontendBaseWidget
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
		// S3
		$this->amazonS3Account = FrontendPhotogalleryHelper::existsAmazonS3();
		
		$this->categories = FrontendPhotogalleryModel::getAllCategoriesWithImage();
		
		if(!empty($this->categories))
		{
			$thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'large');
		
			foreach($this->categories as &$item)
			{
				// No account has been linked
				if(!$this->amazonS3Account)
				{
					$item['filename_url'] =  FRONTEND_FILES_URL . '/' . $this->getModule() . '/sets/frontend/' . $item['set_id'] . '/' . $thumbnail_resolution['width'] . 'x' . $thumbnail_resolution['height'] . '_' . $thumbnail_resolution['method'] . '/' . $item['filename'];
				}
				elseif($this->amazonS3Account)
				{
					// Thumbnail res.
					$item['filename_url'] = FrontendPhotogalleryHelper::getImageURL(
						$this->getModule() . '/sets/frontend/' . $item['set_id'] . '/' . $thumbnail_resolution['width'] . 'x' . $thumbnail_resolution['height'] . '_' . $thumbnail_resolution['method'] . '/' . $item['filename']
					);
				}
				else
				{
					$item['filename_url'] = array();
				}
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
		$this->tpl->assign('widgetPhotogalleryCategories', $this->categories);
	}
}