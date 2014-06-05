<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;

/**
 * Edit category action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class EditCategory extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // get id
        $this->category_id = $this->getParameter('category_id', 'int', 0);

        // does the item exists
        if($this->id !== null && BackendPhotogalleryModel::existsCategory($this->id))
        {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get all data for the item we want to edit
            $this->getData();

            // load the form
            $this->loadForm();

            // validate the form
            $this->validateForm();

            // parse the dataGrid
            $this->parse();

            // display the page
            $this->display();
        }

        // no item found, throw an exceptions, because somebody is fucking with our URL
        else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->record = BackendPhotogalleryModel::getCategory($this->id);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('editCategory');

        // get categories
        $allowedDepth = BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth');
        $allowedDepthStart = BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth_start');
        $this->categoriesCount = BackendPhotogalleryModel::getCategoriesCount();
        $this->categories = BackendPhotogalleryModel::getCategoriesForDropdown(
            array(
                $allowedDepthStart,
                $allowedDepth
            )
        );
        
        // create elements
        $this->frm->addText('title', $this->record['title'], null, 'inputText title');
        $this->frm->addDropdown('parent_id', $this->categories, $this->record['parent_id'])->setDefaultElement('');
        $this->tpl->assign('deleteAllowed', BackendPhotogalleryModel::deleteCategoryAllowed($this->id));

        // meta object
        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);

        // set callback for generating an unique URL
        $this->meta->setUrlCallback('Backend\Modules\Photogallery\engine\Model', 'getURLForCategory', array($this->record['id']));
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        // call parent
        parent::parse();

        // assign
        $this->tpl->assign('item', $this->record);

        // assign category (if there is one)
        $this->tpl->assign('category', $this->record);
        $this->tpl->assign('categories', $this->categories);
        $this->tpl->assign('categories_depth', is_null(BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth')) ? false : true);
        $this->tpl->assign('categoriesCount', $this->categoriesCount);
        //$this->tpl->assign('imageIsAllowed', $this->imageIsAllowed);

        // delete allowed?
        $this->tpl->assign('deleteAllowed', BackendPhotogalleryModel::deleteCategoryAllowed($this->id));

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'category');
        $url404 = BackendModel::getURL(404);

        // parse additional variables
        if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);

        // fetch proper slug
        $this->record['url'] = $this->meta->getURL();
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if($this->frm->isSubmitted())
        {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate fields
            $this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));

            // parented to self?
            if($this->frm->getField('parent_id')->getValue() == $this->record['id'])
            {
                $this->frm->getField('parent_id')->addError(BL::getError('CanNotParentToSelf'));
            }

            // no errors?
            if($this->frm->isCorrect())
            {
                // build item
                $item['id'] = $this->id;
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['meta_id'] = $this->meta->save(true);
                $item['parent_id'] = $this->frm->getField('parent_id')->getValue();

                // upate the item
                BackendPhotogalleryModel::updateCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('categories') . ($this->category_id ? '&category_id=' . $this->category_id : '') . '&report=edited-category&var=' . urlencode($item['title']));
            }
        }
    }
}
