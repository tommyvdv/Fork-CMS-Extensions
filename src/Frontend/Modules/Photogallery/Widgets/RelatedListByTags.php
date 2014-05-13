<?php

namespace Backend\Modules\Photogallery\Widgets;

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;

/**
 * Related list by tags
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class RelatedListByTags extends BackendBaseWidget
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
            $limit = FrontendModel::getModuleSetting('photogallery', 'related_list_tags_number_of_items', 10);
            
            if($this->URL->getParameter(1) !== null) $this->records = FrontendPhotogalleryModel::getRelatedByTags($this->URL->getParameter(1), $limit);
        }
    }

    /**
     * Parse into template
     *
     * @return void
     */
    private function parse()
    {
        $this->tpl->assign('widgetPhotogalleryRelatedListByTags', $this->records);
    }
}
