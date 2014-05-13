<?php

namespace Frontend\Modules\Photogallery\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Photogallery\Engine\Model as FrontendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;

/**
 * This widget will show the latest comments
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class Paged extends FrontendBaseWidget
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
