<?php

namespace Frontend\Modules\Photogallery\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Photogallery\Engine\Model as FrontendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;

/**
 * This is the image-action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Image extends FrontendBaseBlock
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
        if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));
        
        $this->record = FrontendPhotogalleryModel::getImage($this->URL->getParameter(1));

        // anything found?
        if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));
        
        // get tags
        $this->record['tags'] = FrontendTagsModel::getForItem($this->getModule(), $this->record['album_id']);
        
        $large_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'large');
        
        $this->tpl->assign('modulePhotogalleryImageLargeResolution', $large_resolution);
    }

    /**
     * Parse the data into the template
     *
     * @return void
     */
    private function parse()
    {
        // add into breadcrumb
        $this->breadcrumb->addElement($this->record['album_title'], $this->record['album_full_url']);
        $this->breadcrumb->addElement(\SpoonFilter::ucfirst(FL::getLabel('Image')));
        if($this->record['title'] != '') $this->breadcrumb->addElement($this->record['title']);
        
        // Page title: album name
        $this->header->setPageTitle($this->record['album_title']);
        
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
        $this->tpl->assign('blockPhotogalleryAlbumImage', $this->record);

        // assign navigation
        $this->tpl->assign('blockPhotogalleryAlbumImageNavigation', FrontendPhotogalleryModel::getImageNavigation($this->record['id'], $this->record['album_id'], $this->record['sequence']));      
        
        $this->tpl->mapModifier('createimagephotogallery', array(new FrontendPhotogalleryHelper(), 'createImage'));
    }
}
