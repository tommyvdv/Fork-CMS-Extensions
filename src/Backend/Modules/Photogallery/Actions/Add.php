<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\TemplateModifiers as BackendTemplateModifiers;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryHelper;

/**
 * Add action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Add extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // load the form
        $this->loadForm();

        // validate the form
        $this->validateForm();

        // parse
        $this->parse();

        // display the page
        $this->display();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('add');

        // set hidden values
        $rbtHiddenValues[] = array('label' => BL::getLabel('Hidden', $this->URL->getModule()), 'value' => 'Y');
        $rbtHiddenValues[] = array('label' => BL::getLabel('Published'), 'value' => 'N');

        $today = mktime(00, 00, 00);

        //$this->categories = BackendPhotogalleryModel::getCategoriesForDropdown(true);
        $allowedDepth = BackendModel::getModuleSetting('photogallery', 'categories_depth', 0);
        $allowedDepthStart = BackendModel::getModuleSetting('photogallery', 'categories_depth_start', 0);
        $this->categories = BackendPhotogalleryModel::getCategoriesForDropdown(
            array(
                $allowedDepthStart,
                $allowedDepth == 0 ? 0 : $allowedDepth + 1
            )
        );

        // create elements
        $this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
        $this->frm->addEditor('text');
        $this->frm->addEditor('introduction');
        $this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');

        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
        $this->frm->addDropdown('categories', $this->categories, \SpoonFilter::getGetValue('category', null, null, 'int'), true, 'select categoriesBox', 'selectError categoriesBox');

        $this->frm->addDate('publish_on_date');
        $this->frm->addTime('publish_on_time');

        $this->frm->addCheckbox('new', false);
        $this->frm->addDate('new_date_from', null, null, $today);
        $this->frm->addDate('new_date_until', null, null, $today);
        if(!$this->frm->getField('new')->isChecked())
        {
            $this->frm->getField('new_date_from')->setAttribute('disabled', 'disabled');
            $this->frm->getField('new_date_until')->setAttribute('disabled', 'disabled');
        }

        $this->frm->addCheckbox('show_in_albums', true);

        // meta
        $this->meta = new BackendMeta($this->frm, null, 'title', true);

        $this->meta->setURLCallback('Backend\Modules\Photogallery\engine\Model', 'getURLForAlbum');
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        // call parent
        parent::parse();

        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');

        $url404 = BackendModel::getURL(404);

        // parse additional variables
        if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
        
        // parse categories to template
        $this->tpl->assign('categories', $this->categories);
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
            $this->frm->getField('publish_on_date')->isValid(BL::getError('DateIsInvalid'));
            $this->frm->getField('publish_on_time')->isValid(BL::getError('TimeIsInvalid'));

            if($this->frm->getField('new')->isChecked())
            {
                $this->frm->getField('new_date_from')->isValid(BL::getError('DateIsInvalid'));
                $this->frm->getField('new_date_until')->isValid(BL::getError('DateIsInvalid'));
            }

            // validate meta
            $this->meta->validate();

            // no errors?
            if($this->frm->isCorrect())
            {
                // build item
                $item['meta_id'] = $this->meta->save();
                $item['language'] = BL::getWorkingLanguage();
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['introduction'] = $this->frm->getField('introduction')->getValue();
                $item['text'] = $this->frm->getField('text')->getValue();
                $item['created_on'] = BackendModel::getUTCDate();
                $item['edited_on'] = BackendModel::getUTCDate();
                $item['hidden'] = $this->frm->getField('hidden')->getValue();
                $item['show_in_albums'] = $this->frm->getField('show_in_albums')->isChecked() ? 'Y' : 'N';
                $item['publish_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('publish_on_date'), $this->frm->getField('publish_on_time')));
                $item['sequence'] = (int) BackendPhotogalleryModel::getSequenceAlbum() + 1;
                $item['user_id'] = BackendAuthentication::getUser()->getUserId();

                if($this->frm->getField('new')->isChecked())
                {
                    $item['new_from'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('new_date_from')));
                    $item['new_until'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('new_date_until')));
                }
                else
                {
                    $item['new_from'] = null;
                    $item['new_until'] = null;
                }

                // insert the item
                $id = BackendPhotogalleryModel::insertAlbum($item);
            
                // Categories
                $categories = array();
                $categorySequence = 1;

                // loop selected categories
                foreach((array) $this->frm->getField('categories')->getValue() as $categoryId)
                {
                    // add
                    $categories[] = array('album_id' => $id, 'category_id' => $categoryId, 'sequence' => $categorySequence++);
                }

                // update categories
                BackendPhotogalleryModel::updateAlbumCategories($id, $categories);

                $item['id'] = $id;

                // save the tags
                BackendTagsModel::saveTags($item['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());


                // Generate the widgets
                foreach(BackendPhotogalleryModel::getAllExtrasWidgets() as $widget)
                {
                    $resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($widget['id']);

                    $label = $item['title'] . ' | ' . BackendTemplateModifiers::toLabel($widget['action']) . ' | ' . $resolutionsLabel;
                    
                    $extra['module'] = $this->getModule();
                    $extra['label'] = $widget['action'];
                    $extra['action'] = $widget['action'];
                    $extra['data'] = serialize(
                                        array(
                                            'id' => $item['id'],
                                            'extra_label' => $label,
                                            'extra_id' => $widget['id'],
                                            'language' => BL::getWorkingLanguage(),
                                            'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id']
                                        )
                                    );
                    
                    $extraId = BackendPhotogalleryModel::insertModulesExtraWidget($extra);

                    BackendPhotogalleryModel::insertExtraId(array('album_id' => $item['id'], 'extra_id' => $widget['id'], 'modules_extra_id' => $extraId));
                }

                // add search index
                BackendSearchModel::saveIndex('photogallery', $item['id'], array('title' => $item['title'], 'text' => $item['text']));

                // ping
                if(BackendModel::getModuleSetting($this->getModule(), 'ping_services', false)) BackendModel::ping(SITE_URL . BackendModel::getURLForBlock('photogallery', 'detail') . '/' . $this->meta->getURL());

                // everything is saved, so redirect
                $this->redirect(BackendModel::createURLForAction('add_images_choose') . '&report=added-album&album_id=' . $item['id']);
            }
        }
    }
}
