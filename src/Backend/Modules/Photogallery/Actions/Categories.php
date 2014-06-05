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

/**
 * This categories overview action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Categories extends BackendBaseActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // god
        $this->isGod = BackendAuthentication::getUser()->isGod();

        // get id
        $this->category_id = $this->getParameter('category_id', 'int', 0);
        $this->category = $this->category_id ? BackendPhotogalleryModel::getCategory($this->category_id) : array();

        // get data
        $this->getData();

        // load dataGrids
        $this->loadDataGrid();

        // parse page
        $this->parse();

        // display the page
        $this->display();
    }

    /**
     * Get the data
     */
    private function getData()
    {
        // get parent, parents parent, etc…
        $this->breadcrumbs = array_reverse(BackendPhotogalleryModel::getBreadcrumbsForCategory($this->category_id));
        $this->depth = count($this->breadcrumbs)-1 > 0 ? count($this->breadcrumbs)-1 : 0;
        $this->start_depth = (int) BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth_start');
        $this->limit_depth = (int) BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth');
        $this->limit_depth_is_infinity = BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth') == "0";
        $this->limit_depth_is_allowed = !is_null(BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth'));
        $this->add_allowed = $this->depth >= $this->start_depth && (($this->depth <= $this->limit_depth) || $this->limit_depth_is_infinity);
        $this->add_child_allowed = $this->depth + 1 >= $this->start_depth && ($this->depth + 1 <= $this->limit_depth || $this->limit_depth_is_infinity);
/*
        \Spoon::dump(
            array(
                'depth' => $this->depth,
                'start_depth' => $this->start_depth,
                'limit_depth' => $this->limit_depth,
                'limit_depth_is_infinity' => $this->limit_depth_is_infinity,
                'limit_depth_is_allowed' => $this->limit_depth_is_allowed,
                'add allowed' => $this->add_allowed,
                'add child allowed' => $this->add_child_allowed
            )
        );
*/
    }

    /**
     * Loads the dataGrids
     */
    private function loadDataGrid()
    {
        // create dataGrid
        //$this->dataGrid = new BackendDataGridDB(BackendPhotogalleryModel::QRY_DATAGRID_BROWSE_CATEGORIES, BL::getWorkingLanguage());

        $column_sequence = array(
            'dragAndDropHandle',
            'title',
            'num_children',
            'num_albums',
            'edit'
        );

        // create dataGrid
        $this->dataGrid = new BackendDataGridDB(
            BackendPhotogalleryModel::QRY_DATAGRID_BROWSE_CATEGORIES,
            array(
                BL::getWorkingLanguage(),
                $this->category_id
            )
        );

        // sorting columns
        $this->dataGrid->setSortingColumns(array('title'), 'sequence');
        
        $this->dataGrid->enableSequenceByDragAndDrop();

        // add column
        //$this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_category') . '&amp;id=[id]', BL::getLabel('Edit'));
        $this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_category') . '&amp;id=[id]' . ($this->category_id ? '&amp;category_id=' . $this->category_id : ''), BL::getLabel('Edit'));
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'getTitleWithNumAlbums'), array('[num_albums]', '[title]'), 'title', true);

        $this->dataGrid->setColumnsHidden(array('sequence'));

        // add subcategories button (if depth is not greater than…)
        /*
        if(
            !is_null(BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth')) &&
            (
                count($this->breadcrumbs) <= BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth', 0) ||
                (int) BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth') === 0
            )
        )
        {
        */
            // build sequence
            $column_sequence = array(
                'dragAndDropHandle',
                'title',
                'num_children',
                'num_albums',
                'children'
            );

            // add children column
            $this->dataGrid->addColumn('children', null);
            $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'getNumchildrenButton'), array('[num_children]', '[id]'), 'children', true);
            //$this->dataGrid->setColumnFunction(create_function('$num_children,$id','return $num_children = $num_children ? "<a href=\"" . BackendModel::createURLForAction("categories") . "&amp;category_id=" . $id . "\">" . vsprintf(BL::lbl("ViewSubcategories"), $num_children) . "</a>" : BL::lbl("NoSubcategories");'),array('[num_children]', '[id]'),'children',true);
            
            // add add button
            if($this->add_child_allowed)
            {
                $this->dataGrid->addColumn('add_subcategory', null, sprintf(BL::lbl('AddSubCategory')), BackendModel::createURLForAction('add_category') . '&amp;category_id=[id]', BL::getLabel('msgCategoriesForParent'));
                $column_sequence[] = 'add_subcategory';
            }
            
            // add edit to sequence
            $column_sequence[] = 'edit';
        //}

        // disable paging
        $this->dataGrid->setPaging(false);      
        
        $this->dataGrid->setAttributes(array('data-action' => "category_sequence"));

        // column sequence
        $this->dataGrid->setColumnsSequence($column_sequence);
        $this->dataGrid->setColumnsHidden(array('num_children', 'num_albums'));
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        // assign
        $this->tpl->assign('addToParentURL', BackendModel::createURLForAction('add_category', 'photogallery', BL::getWorkingLanguage(), isset($this->category['id']) ? array('category_id' => $this->category['id']) : array()));
        $this->tpl->assign('category', $this->category);
        $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
        $this->tpl->assign('breadcrumbs', $this->breadcrumbs);
        
        $this->tpl->assign('depth', $this->depth);
        $this->tpl->assign('start_depth', $this->start_depth);
        $this->tpl->assign('limit_depth', $this->limit_depth);
        $this->tpl->assign('add_allowed', $this->add_allowed);
        $this->tpl->assign('add_child_allowed', $this->add_child_allowed);

        $this->tpl->assign('isGod', $this->isGod);
    }
}
