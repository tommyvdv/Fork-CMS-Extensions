<?php

namespace Frontend\Modules\Photogallery\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Rss as FrontendRSS;
use Frontend\Core\Engine\RssItem as FrontendRSSItem;
use Frontend\Core\Engine\User as FrontendUser;
use Frontend\Modules\Photogallery\Engine\Model as FrontendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;

/**
 * This is the RSS-feed
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Rss extends FrontendBaseBlock
{
    /**
     * The articles
     *
     * @var array
     */
    private $items;

    /**
     * The settings
     *
     * @var array
     */
    private $settings;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->getData();
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {

        $this->tpl->mapModifier('createimagephotogallery', array(new FrontendPhotogalleryHelper(), 'createImage'));

        $thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'album_overview_thumbnail');

        // requested page
        $requestedPage = $this->URL->getParameter('page', 'int', 1);

        // set URL and limit
        $this->pagination['url'] = FrontendNavigation::getURLForBlock('photogallery');
        $this->pagination['limit'] = FrontendModel::getModuleSetting('photogallery', 'overview_albums_number_of_items', 10);

        // populate count fields in pagination
        $this->pagination['num_items'] = FrontendPhotogalleryModel::getAlbumsCount();
        $this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

        // num pages is always equal to at least 1
        if($this->pagination['num_pages'] == 0) $this->pagination['num_pages'] = 1;

        // redirect if the request page doesn't exist
        if($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

        // populate calculated fields in pagination
        $this->pagination['requested_page'] = $requestedPage;
        $this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

        // get articles
        $this->items = FrontendPhotogalleryModel::getAllWithImages($this->pagination['limit'], $this->pagination['offset']);

        // get resolutions
        $this->thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'album_detail_overview_thumbnail');

        $this->settings = FrontendModel::getModuleSettings('photogallery');
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // get vars
        $title = (isset($this->settings['rss_title_' . FRONTEND_LANGUAGE])) ? $this->settings['rss_title_' . FRONTEND_LANGUAGE] : FrontendModel::getModuleSetting('photogallery', 'rss_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
        $link = SITE_URL . FrontendNavigation::getURLForBlock('photogallery');
        $description = (isset($this->settings['rss_description_' . FRONTEND_LANGUAGE])) ? $this->settings['rss_description_' . FRONTEND_LANGUAGE] : null;

        // create new rss instance
        $rss = new FrontendRSS($title, $link, $description);

        // loop articles
        foreach($this->items as $item)
        {
            // init vars
            $title = $item['title'];
            $link = $item['full_url'];
            $description = ($item['introduction'] != '') ? $item['introduction'] : $item['text'];

            // meta is wanted
            if(FrontendModel::getModuleSetting('photogallery', 'rss_meta_' . FRONTEND_LANGUAGE, true))
            {
                // append meta
                $description .= '<div class="meta">' . "\n";
                $description .= '   <p><a href="' . $link . '" title="' . $title . '">' . $title . '</a>';

                // has image
                if(isset($item['images']))
                {
                    // loop images
                    foreach($item['images'] as $image)
                    {
                        $description .= '   <p>';
                        if($this->data['action'] == 'paged')
                        {
                            // append image
                            $description .= '       <a href="' . SITE_URL . $image['full_url'] . '"><img src="' . SITE_URL . FrontendPhotogalleryHelper::createImage(null, $image['set_id'], $image['filename'], $this->thumbnail_resolution['width'], $this->thumbnail_resolution['height'], $this->thumbnail_resolution['method']) . '" /></a>';
                        }
                        elseif($this->data['action'] == 'lightbox')
                        {
                            // append image
                            $description .= '       <a href="' . FrontendModel::addURLParameters($link, array(FL::act('LightboxImage') => $image['id'])) . '"><img src="' . SITE_URL . FrontendPhotogalleryHelper::createImage(null, $image['set_id'], $image['filename'], $this->thumbnail_resolution['width'], $this->thumbnail_resolution['height'], $this->thumbnail_resolution['method'])  . '" /></a>';
                        }
                        else
                        {
                            // append image
                            $description .= '       <img src="' . SITE_URL . FrontendPhotogalleryHelper::createImage(null, $image['set_id'], $image['filename'], $this->thumbnail_resolution['width'], $this->thumbnail_resolution['height'], $this->thumbnail_resolution['method']) . '" />';
                        }
                        $description .= '   </p>';
                    }
                }

                // any tags
                if(isset($item['tags']))
                {
                    if(!empty($item['tags']))
                    {
                        // append tags-paragraph
                        $description .= '   <p>' . \SpoonFilter::ucfirst(FL::lbl('Tags')) . ': ';
                        $first = true;

                        // loop tags
                        foreach($item['tags'] as $tag)
                        {
                            // prepend separator
                            if(!$first) $description .= ', ';

                            // add
                            $description .= '<a href="' . $tag['full_url'] . '" rel="tag" title="' . $tag['name'] . '">' . $tag['name'] . '</a>';

                            // reset
                            $first = false;
                        }

                        // end
                        $description .= '.</p>' . "\n";
                    }
                    
                }

                // any categories
                if(isset($item['categories']))
                {
                    if(!empty($item['categories']))
                    {
                        // append tags-paragraph
                        $description .= '   <p>' . \SpoonFilter::ucfirst(FL::lbl('Categories')) . ': ';
                        $first = true;

                        // loop tags
                        foreach($item['categories'] as $category)
                        {
                            // prepend separator
                            if(!$first) $description .= ', ';

                            // add
                            $description .= '<a href="' . $category['full_url'] . '" rel="tag" title="' . $category['title'] . '">' . $category['title'] . '</a>';

                            // reset
                            $first = false;
                        }

                        // end
                        $description .= '.</p>' . "\n";
                    }
                }


                // end HTML
                $description .= '</div>' . "\n";
            }

            // create new instance
            $rssItem = new FrontendRSSItem($title, $link, $description);

            // set item properties
            $rssItem->setPublicationDate($item['publish_on']);

            // add item
            $rss->addItem($rssItem);
        }


        // output
        $rss->parse();
    }
}
