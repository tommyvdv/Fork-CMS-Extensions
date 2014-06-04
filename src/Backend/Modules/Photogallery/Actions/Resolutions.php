<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryHelper;

/**
 * Index action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Resolutions extends BackendBaseActionIndex
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
        $this->dataGrid = new BackendDataGridDB(BackendPhotogalleryModel::QRY_DATAGRID_BROWSE_RESOLUTIONS);
        
        // sorting columns
        $this->dataGrid->setSortingColumns(array('resolution', 'kind'), 'kind');

        // add edit column
        $this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'));
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'getResolutionEditButton'), array('[id]', '[allow_edit]'), 'edit', true);
        //$this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_resolution') . '&amp;id=[id]', BL::getLabel('Edit'));

        // column functions
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'translateYes'), array('[allow_delete]'), 'allow_delete', true);
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'translateYes'), array('[allow_edit]'), 'allow_edit', true);

        // hide redundant columns
        $this->dataGrid->setColumnsHidden(array('allow_delete', 'allow_edit'));
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');

        // add fields
        $this->frm->addText('title', $this->filter['title'])->setAttributes(array('id' => 'noMetaTitle'));
        $this->frm->addDropdown('hidden', array('Y' => BL::getLabel('Hidden'), 'N' => BL::getLabel('Published')), $this->filter['hidden']);

        $this->frm->getField('hidden')->setdefaultelement(\SpoonFilter::ucfirst(BL::getLabel('ChooseAStatus')));

        // manually parse fields
        $this->frm->parse($this->tpl);
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
