<?php

namespace Backend\Modules\Photogallery\Widgets;

use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Frontend\Modules\Photogallery\Engine\Helper as FrontendPhotogalleryHelper;

/**
 * Category navigation widget
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class CategoryNavigation extends BackendBaseWidget
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
        // Get categories html
        $this->navigation = FrontendPhotogalleryModel::buildCategoriesNavigation(0, $this->URL->getParameter(1));
    }

    /**
     * Parse into template
     *
     * @return void
     */
    private function parse()
    {
        $this->tpl->assign('widgetPhotogalleryCategoryNavigation', $this->navigation);
        $this->tpl->assign('isRoot', !isset($this->data['id']) ? true : false);
    }
}
