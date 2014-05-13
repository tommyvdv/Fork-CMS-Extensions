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
 * Add image choose action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class AddImagesChoose extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('album_id', 'int');

        // does the item exists
        if($this->id !== null && BackendPhotogalleryModel::existsAlbum($this->id))
        {

            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get all data for the item we want to edit
            $this->getData();

            // load the form
            $this->loadForm();

            // validate the form
            $this->validateForm();

            // parse
            $this->parse();

            // display the page
            $this->display();
        }
        // no item found, throw an exception, because somebody is fucking with our URL
        else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('choose');

        // set hidden values
        $rbtOptionValues[] = array('label' => \SpoonFilter::ucfirst(BL::getLabel('UploadImages')), 'value' => 'upload');
        $rbtOptionValues[] = array('label' => \SpoonFilter::ucfirst(BL::getLabel('UseExistingAlbum')), 'value' => 'existing');

        $this->frm->addRadiobutton('options', $rbtOptionValues, 'upload');
    }

    /**
     * Get the data for a question
     */
    private function getData()
    {
        // get the record
        $this->record = BackendPhotogalleryModel::getAlbum($this->id);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing-album');

        $this->sets = BackendPhotogalleryModel::getSetsForDropdown();

        if(empty($this->sets)) $this->redirect(BackendModel::createURLForAction('add_images_upload_multiple') . '&album_id=' . $this->id);

        // If set_id is not null and set exists
        if($this->record['set_id'] !== null && !BackendPhotogalleryModel::existsSet($this->record['set_id']))
        {
            // Reset set_id of it the set doesn't exists anymore
            BackendPhotogalleryModel::updateAlbum(array('id' => $this->id, 'set_id' => null));

            $this->redirect(BackendModel::createURLForAction('add_images_choose') . '&album_id=' . $this->id);
        }
        // If set_id is not null and set doesn't exists anymore
        elseif($this->record['set_id'] !== null && BackendPhotogalleryModel::existsSet($this->record['set_id']))
        {
            $this->redirect(BackendModel::createURLForAction('add_images_upload_multiple') . '&album_id=' . $this->id);
        }
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        // call parent
        parent::parse();

        $this->tpl->assign('record', $this->record);
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

            // no errors?
            if($this->frm->isCorrect())
            {
                $option = $this->frm->getField('options')->getValue();

                switch($option)
                {
                    case 'upload':
                        $this->redirect(BackendModel::createURLForAction('add_images_upload_multiple') . '&album_id=' . $this->id);
                        break;
                    case 'existing':
                        $this->redirect(BackendModel::createURLForAction('add_images_existing') . '&album_id=' . $this->id);
                        break;
                }
            }
        }
    }
}
