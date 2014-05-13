<?php

namespace Frontend\Modules\Photogallery\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Photogallery\Engine\Model as FrontendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;

/**
 * Lightbox widget
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Lightbox extends FrontendBaseWidget
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
            $this->record['tags'] = FrontendTagsModel::getForItem($this->getModule(), $this->record['id']);
            $this->record['data'] = $this->data;

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
        
        // Link Icon, only load when needed
        if(isset($this->record['extra']['data']['settings']['show_hover_icon']) && $this->record['extra']['data']['settings']['show_hover_icon'] == 'true')
        {           
            $this->header->addCSS(
                FrontendPhotogalleryHelper::getPathJS('/link-icon/link-icon.css', $this->getModule())
            );  

            $this->header->addJS(
                FrontendPhotogalleryHelper::getPathJS('/link-icon/link-icon.js', $this->getModule())
            );  
        }

        // Initialize
        $this->header->addJS(
            FrontendPhotogalleryHelper::getPathJS('/fancybox-init.js', $this->getModule())
        );
        
        $this->header->addCSS('/frontend/modules/' . $this->getModule() . '/layout/css/photogallery.css');

        $this->tpl->assign('widgetPhotogalleryLightbox', $this->record);
        $this->tpl->mapModifier('createimagephotogallery', array('Frontend\Modules\Photogallery\Engine\Helper', 'createImage'));
        $this->addJSData('lightbox_settings_' . $this->data['extra_id'], $this->record['extra']['data']['settings']);
    }
}
