<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\TemplateModifiers as BackendTemplateModifiers;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryHelper;

/**
 * This extras overview action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Extras extends BackendBaseActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // load dataGrids
        $this->loadDataGrid();

        // parse page
        $this->parse();

        // display the page
        $this->display();
    }

    /**
     * Load the dataGrid
     */
    private function loadDataGrid()
    {
        // create dataGrid
        $this->dataGrid = new BackendDataGridDB(BackendPhotogalleryModel::QRY_DATAGRID_BROWSE_EXTRAS);
        
        // sorting columns
        $this->dataGrid->setSortingColumns(array('action', 'kind'), 'kind');
    
        // add columns
        $this->dataGrid->addColumn('resolutions', \SpoonFilter::ucfirst(BL::getLabel('Resolutions')));

        // functions
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'getResolutionsForDataGrid'), array('[id]'), 'resolutions', true);
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'getSeperateResolutionsForDataGrid'), array('[id]'), 'resolutions', true);

        // add title
        $this->dataGrid->addColumn('title', \SpoonFilter::ucfirst(BL::getLabel('Title')));
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'getExtraTitleForDataGrid'), array('[data]'), 'title', true);
        
        $this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::getLabel('Edit'));

        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'getExtraEditURLForKind'), array('[id]','[kind]', '[action]'), 'edit', true);

        // set colum URLs
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'toLabel'), array('[action]'), 'action', true);
        
        $this->dataGrid->setColumnFunction(array('Backend\Core\Engine\TemplateModifiers', 'toLabel'), array('[kind]'), 'kind', true);
        $this->dataGrid->setColumnURL('action', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

        // hide data
        $this->dataGrid->setColumnHidden('data');

        // disable paging
        $this->dataGrid->setPaging(false);

        // sequence columns
        $this->dataGrid->setColumnsSequence(array('title', 'kind', 'action', 'resolutions', 'edit'));
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        // parse dataGrid
        $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

        // parse paging & sorting
        $this->tpl->assign('offset', (int) $this->dataGrid->getOffset());
        $this->tpl->assign('order', (string) $this->dataGrid->getOrder());
        $this->tpl->assign('sort', (string) $this->dataGrid->getSort());
    }
}
