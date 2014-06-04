<?php

namespace Frontend\Modules\Photogallery\Actions;

use Common\Cookie as CommonCookie;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Photogallery\Engine\Model as FrontendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;

/**
 * This is the detail-action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Detail extends FrontendBaseBlock
{
    /**
     * The record
     *
     * @var array
     */
    private $record;

    /**
     * Execute the extra
     *
     * @return void
     */
    public function execute()
    {
        // call the parent
        parent::execute();

        // hide contenTitle, in the template the title is wrapped with an inverse-option
        $this->tpl->assign('hideContentTitle', true);

        // load template
        $this->loadTemplate();

        // load the data
        $this->getData();

        // parse
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     *
     * @return void
     */
    private function getData()
    {   
        // validate incoming parameters
        if($this->URL->getParameter(1) === null && $this->URL->getParameter(0) === null) $this->redirect(FrontendNavigation::getURL(404));
        
        $this->record = FrontendPhotogalleryModel::get($this->URL->getParameter(0));
        if(empty($this->record)) $this->record = FrontendPhotogalleryModel::get($this->URL->getParameter(1));
        
        // anything found?
        if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

        // get tags
        $this->record['tags'] = FrontendTagsModel::getForItem($this->getModule(), $this->record['id']);
        $this->record['extra'] = FrontendPhotogalleryModel::getExtra($this->data['extra_id']);
        $this->record['data'] = $this->data;


        $thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'album_detail_overview_thumbnail');
        $large_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'large');
        
        $this->tpl->assign('modulePhotogalleryDetailLargeResolution', $large_resolution);
        $this->tpl->assign('modulePhotogalleryDetailThumbnailResolution', $thumbnail_resolution);
    }

    /**
     * Parse the data into the template
     *
     * @return void
     */
    private function parse()
    {
        // map modifiers
        FrontendPhotogalleryHelper::mapModifiers($this->tpl);
        
        // add into breadcrumb
        $this->breadcrumb->addElement($this->record['title']);
        
        $this->header->addCSS('/src/Frontend/Modules/Photogallery/Layout/Css/photogallery.css');
        
        // Load lightbox
        if($this->data['action'] == 'lightbox')
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

            // Media
            $this->header->addJS(
                FrontendPhotogalleryHelper::getPathJS('/fancybox/' . FrontendPhotogalleryModel::FANCYBOX_VERSION . '/helpers/jquery.fancybox-media.js', $this->getModule())
            );  

            // Link Icon
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
            
            $this->tpl->assign('lightbox', true);
        }
        elseif($this->data['action'] == 'paged')
        {
            $this->tpl->assign('paged', true);
        }
        
        // set meta
        $this->header->setPageTitle($this->record['meta_title'], ($this->record['meta_title_overwrite'] == 'Y'));
        $this->header->addMetaDescription($this->record['meta_description'], ($this->record['meta_description_overwrite'] == 'Y'));
        $this->header->addMetaKeywords($this->record['meta_keywords'], ($this->record['meta_keywords_overwrite'] == 'Y'));

        // advanced SEO-attributes
        if(isset($this->record['meta_data']['seo_index'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_index']));
        if(isset($this->record['meta_data']['seo_follow'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_follow']));
    
        // get RSS-link
        $rssLink = FrontendModel::getModuleSetting('photogallery', 'feedburner_url_' . FRONTEND_LANGUAGE);
        if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('photogallery', 'rss');

        // add RSS-feed
        $this->header->addLink(array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => FrontendModel::getModuleSetting('photogallery', 'rss_title_' . FRONTEND_LANGUAGE), 'href' => $rssLink), true);

        // assign article
        $this->tpl->assign('blockPhotogalleryAlbum', $this->record);
        $this->addJSData('lightbox_settings_' . $this->data['extra_id'], $this->record['extra']['data']['settings']);
        
        // assign navigation
        $this->tpl->assign('blockPhotogalleryAlbumNavigation', FrontendPhotogalleryModel::getNavigation($this->record['id']));

        $this->tpl->mapModifier('createimagephotogallery', array('Frontend\Modules\Photogallery\Engine\Helper', 'createImage'));
    }
}
