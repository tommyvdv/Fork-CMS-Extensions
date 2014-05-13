<?php

namespace Backend\Modules\Photogallery\Widgets;

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;

/**
 * Paged widget
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class RelatedByCategories extends BackendBaseWidget
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
        $this->records = array();

        // Are we on a detail
        if($this->URL->getParameter(0) == FL::getAction('Detail'))
        {
            $limit = FrontendModel::getModuleSetting('photogallery', 'related_categories_number_of_items', 10);
            
            // validate incoming parameters
            if($this->URL->getParameter(1) !== null) $this->records = FrontendPhotogalleryModel::getRelatedByCategories($this->URL->getParameter(1), $limit);
            
            if(!empty($this->records))
            {
                $this->amazonS3Account = FrontendPhotogalleryHelper::existsAmazonS3();
                $thumbnail_resolution = FrontendPhotogalleryModel::getExtraResolutionForKind($this->data['extra_id'], 'thumbnail');
                
                foreach($this->records as &$item)
                {
                    if(!empty($item['image']))
                    {
                        // No account has been linked
                        if(!$this->amazonS3Account)
                        {
                            $item['image']['thumbnail_url'] =  FRONTEND_FILES_URL . '/' . $this->getModule() . '/sets/frontend/' . $item['image']['set_id'] . '/' . $thumbnail_resolution['width'] . 'x' . $thumbnail_resolution['height'] . '_' . $thumbnail_resolution['method'] . '/' . $item['image']['filename'];
                        }
                        elseif($this->amazonS3Account)
                        {
                            // Thumbnail res.
                            $item['image']['thumbnail_url'] = FrontendPhotogalleryHelper::getImageURL(
                                $this->getModule() . '/sets/frontend/' . $item['set_id'] . '/' . $thumbnail_resolution['width'] . 'x' . $thumbnail_resolution['height'] . '_' . $thumbnail_resolution['method'] . '/' . $item['image']['filename']
                            );
                        }
                        
                    }
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
        $this->tpl->assign('widgetPhotogalleryRelatedByCategories', $this->records);
    }
}
