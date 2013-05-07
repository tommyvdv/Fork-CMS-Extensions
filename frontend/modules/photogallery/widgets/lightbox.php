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
class FrontendPhotogalleryWidgetLightbox extends FrontendBaseWidget
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

		
		if(!empty($this->record))
		{
			$this->record['extra'] = FrontendPhotogalleryModel::getExtra($this->data['extra_id']);
			// get tags
			$this->record['tags'] = FrontendTagsModel::getForItem($this->getModule(), $this->record['id']);

			$thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'thumbnail');
			$large_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'large');
			
			$this->tpl->assign('widgetPhotogalleryLightboxLargeResolution', $large_resolution);
			$this->tpl->assign('widgetPhotogalleryLightboxThumbnailResolution', $thumbnail_resolution);
		}

	}

	/**
	 * Parse into template
	 *
	 * @return void
	 */
	private function parse()
	{
		
		
		// Lightbox
		$this->header->addCSS(
			FrontendPhotogalleryHelper::getPathJS('/fancybox/' . FrontendPhotogalleryModel::FANCYBOX_VERSION . '/jquery.fancybox.css', $this->getModule())
		);

		$this->header->addJS(
			FrontendPhotogalleryHelper::getPathJS('/fancybox/' . FrontendPhotogalleryModel::FANCYBOX_VERSION . '/jquery.fancybox.js', $this->getModule())
		);
		
		// Buttons
		$this->header->addCSS(
			FrontendPhotogalleryHelper::getPathJS('/fancybox/' . FrontendPhotogalleryModel::FANCYBOX_VERSION . '/helpers/jquery.fancybox-buttons.css', $this->getModule())
		);
		$this->header->addJS(
			FrontendPhotogalleryHelper::getPathJS('/fancybox/' . FrontendPhotogalleryModel::FANCYBOX_VERSION . '/helpers/jquery.fancybox-buttons.js', $this->getModule())
			);
		
		// Thumbs
		$this->header->addCSS(
			FrontendPhotogalleryHelper::getPathJS('/fancybox/' . FrontendPhotogalleryModel::FANCYBOX_VERSION . '/helpers/jquery.fancybox-thumbs.css', $this->getModule())
		);
		$this->header->addJS(
			FrontendPhotogalleryHelper::getPathJS('/fancybox/' . FrontendPhotogalleryModel::FANCYBOX_VERSION . '/helpers/jquery.fancybox-thumbs.js', $this->getModule())
		);	
		
		// Link Icon
		$this->header->addCSS(
			FrontendPhotogalleryHelper::getPathJS('/link-icon/link-icon.css', $this->getModule())
		);	

		$this->header->addJS(
			FrontendPhotogalleryHelper::getPathJS('/link-icon/link-icon.js', $this->getModule())
		);	

		$this->header->addJS(
			FrontendPhotogalleryHelper::getPathJS('/link-icon-init.js', $this->getModule())
		);

		// Initialize
		$this->header->addJS(
			FrontendPhotogalleryHelper::getPathJS('/fancybox-init.js', $this->getModule())
		);

		$this->header->addCSS('/frontend/modules/' . $this->getModule() . '/layout/css/photogallery.css');
		
		$this->tpl->assign('widgetPhotogalleryLightbox', $this->record);
		$this->tpl->mapModifier('createimagephotogallery', array('FrontendPhotogalleryHelper', 'createImage'));
		$this->addJSData('lightbox_settings_' . $this->record['id'], $this->record['extra']['data']['settings']);

	}
}