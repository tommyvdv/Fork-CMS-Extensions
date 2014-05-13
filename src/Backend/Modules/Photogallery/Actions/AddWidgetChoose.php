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
 * Add widget choose action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class AddWidgetChoose extends BackendBaseActionAdd
{
    /**
     * Execute the action
     */
    public function execute()
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

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('choose');

        // set hidden values
        $rbtOptionValues[] = array('label' => \SpoonFilter::ucfirst(BL::getLabel('Slideshow')), 'value' => 'slideshow');
        $rbtOptionValues[] = array('label' => \SpoonFilter::ucfirst(BL::getLabel('Lightbox')), 'value' => 'lightbox');
        
        $this->frm->addRadiobutton('options', $rbtOptionValues, 'slideshow');
    }

    /**
     * Get the data for a question
     */
    private function getData()
    {

    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        // call parent
        parent::parse();
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
                    case 'slideshow':
                        $this->redirect(BackendModel::createURLForAction('add_widget_slideshow'));
                        break;
                    case 'lightbox':
                        $this->redirect(BackendModel::createURLForAction('add_widget_lightbox'));
                        break;
                    case 'paged':
                        $this->redirect(BackendModel::createURLForAction('add_widget_paged'));
                        break;
                    case 'categories':
                        $this->redirect(BackendModel::createURLForAction('add_widget_categories'));
                        break;
                    case 'related_by_tags':
                        $this->redirect(BackendModel::createURLForAction('add_widget_related_by_tags'));
                        break;
                    case 'related_by_categories':
                        $this->redirect(BackendModel::createURLForAction('add_widget_related_by_categories'));
                        break;
        }
            }
        }
    }
}
