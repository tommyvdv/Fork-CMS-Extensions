<?php

namespace Frontend\Modules\Photogallery\Widgets;

use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Photogallery\Engine\Model as FrontendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;

/**
 * Related list by categories
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class RelatedListByCategories extends FrontendBaseWidget
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
            $limit = FrontendModel::getModuleSetting('photogallery', 'related_list_categories_number_of_items', 10);
            
            if($this->URL->getParameter(1) !== null) $this->records = FrontendPhotogalleryModel::getRelatedByCategories($this->URL->getParameter(1), $limit);
        }
    }

    /**
     * Parse into template
     *
     * @return void
     */
    private function parse()
    {
        $this->tpl->assign('widgetPhotogalleryRelatedListByCategories', $this->records);
    }
}