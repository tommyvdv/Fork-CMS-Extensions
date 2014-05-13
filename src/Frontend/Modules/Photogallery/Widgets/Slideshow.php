<?php

namespace Backend\Modules\Photogallery\Widgets;

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;

/**
 * Slideshow widget
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Slideshow extends BackendBaseWidget
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
            $this->record['extra'] = FrontendPhotogalleryModel::getExtra($this->data['extra_id']);

            $large_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'large');
            $this->tpl->assign('widgetPhotogallerySlideshow', $this->record);
            $this->tpl->assign('widgetPhotogallerySlideshowResolution', $large_resolution);
            $this->tpl->assign('widgetPhotogallerySlideshowShowCaption', $this->record['extra']['data']['settings']['show_caption'] == 'true');
            $this->tpl->assign('widgetPhotogallerySlideshowNavigationThumnails', $this->record['extra']['data']['settings']['pagination_type'] == 'thumbnails');
            $this->tpl->assign('widgetPhotogallerySlideshowNavigationNumbers', $this->record['extra']['data']['settings']['pagination_type'] == 'numbers');
        }
    }

    /**
     * Parse into template
     *
     * @return void
     */
    private function parse()
    {

        $this->header->addCSS(
            FrontendPhotogalleryHelper::getPathJS('/flexslider/' . FrontendPhotogalleryModel::FLEXSLIDER_VERSION . '/flexslider.css', $this->getModule())
        );
        
        $this->header->addJS(
            FrontendPhotogalleryHelper::getPathJS('/flexslider/' . FrontendPhotogalleryModel::FLEXSLIDER_VERSION . '/jquery.flexslider.js', $this->getModule()),
            false
        );

        $this->header->addCSS('/frontend/modules/' . $this->getModule() . '/layout/css/photogallery.css');


        $this->header->addJS(
                FrontendPhotogalleryHelper::getPathJS('/flexslider-init.js', $this->getModule())
        );

        $this->tpl->mapModifier('createimagephotogallery', array(new FrontendPhotogalleryHelper(), 'createImage'));

        $this->addJSData('slideshow_settings_' . $this->record['id'], $this->record['extra']['data']['settings']);
    }
}
