<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;

/**
 * Add category action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class AddCategory extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get id
        $this->category_id = $this->getParameter('category_id', 'int', 0);
        $this->category = BackendPhotogalleryModel::getCategory($this->category_id);
        $this->categories_depth = is_null(BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth')) ? false : true;

        // load the form
        $this->loadForm();

        // validate the form
        $this->validateForm();

        // parse the dataGrid
        $this->parse();

        // display the page
        $this->display();
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        // call parent
        parent::parse();

        // assign category (if there is one)
        if($this->category) $this->tpl->assign('category', $this->category);
        $this->tpl->assign('categories', $this->categories);
        $this->tpl->assign('categories_depth', $this->categories_depth);
        $this->tpl->assign('categories_count', $this->categories_count);

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'category');
        $url404 = BackendModel::getURL(404);

        // parse additional variables
        if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('addCategory');

        // determine depth
        $allowedDepth = BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth');
        $allowedDepthStart = BackendModel::getModuleSetting($this->URL->getModule(), 'categories_depth_start');

        // get categories
        $this->categories_count = BackendPhotogalleryModel::getCategoriesCount();
        $this->categories = BackendPhotogalleryModel::getCategoriesForDropdown(
            array(
                $allowedDepthStart,
                $allowedDepth
            )
        );

        // get categories
        /*
        $this->categories = BackendPhotogalleryModel::getCategoriesForDropdown(
            BackendModel::getModuleSetting('photogallery', 'categories_depth')
        );
        */

        // create elements
        $this->frm->addText('title', null, 255, 'inputText title', 'inputTextError title');
        $this->frm->addDropdown('parent_id', $this->categories, \SpoonFilter::getGetValue('category_id', null, null, 'int'))->setDefaultElement('');

        // meta
        $this->meta = new BackendMeta($this->frm, null, 'title', true);
        
        $this->meta->setURLCallback('Backend\Modules\Photogallery\engine\Model', 'getURLForCategory');
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

            // validate meta
            $this->meta->validate();

            // no errors?
            if($this->frm->isCorrect())
            {
                // build item
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['language'] = BL::getWorkingLanguage();
                $item['meta_id'] = $this->meta->save();
                $item['sequence'] = (int) BackendPhotogalleryModel::getSequenceCategory() + 1;
                $item['parent_id'] = (int) $this->frm->getField('parent_id')->getValue();

                // insert the item
                $item['id'] = BackendPhotogalleryModel::insertCategory($item);

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('categories') . ($item['parent_id'] ? '&category_id=' . $item['parent_id'] : '') . '&report=added-category&var=' . urlencode($item['title']));
            }
        }
    }
}
