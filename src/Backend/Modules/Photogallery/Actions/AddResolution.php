<?php

namespace Backend\Modules\Photogallery\Actions;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\File;

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
class AddResolution extends BackendBaseActionAdd
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

        // parse the dataGrid
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
        $this->frm = new BackendForm('addResolution');

        $this->methods = array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize'));

        // create elements
        $this->frm->addText('kind', null, null, 'inputText title', 'inputTextError title');
        $this->frm->addText('width');
        $this->frm->addCheckbox('width_null');
        $this->frm->addText('height');
        $this->frm->addCheckbox('height_null');
        $this->frm->addDropdown('method', $this->methods)->setDefaultElement(\SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));
    }

    /**
     * Validate the resolution
     *
     * @param string $field The field to validate
     */
    private function validateResolution($field)
    {
        if(!$this->frm->getField($field . '_null')->isChecked())
        {
            if($this->frm->getField($field)->isFilled(BL::getError('FieldIsRequired')))
            {
                if($this->frm->getField($field)->isFloat(BL::getError('InvalidNumber')))
                {
                    if($this->frm->getField($field)->isGreaterThan(0, \SpoonFilter::ucfirst(BL::getError('FieldMustBeGreatherThenZero'))))
                        return true;
                }
            }
        }
        else
        {
            return false;
        }
    }

    private function validateKindTitle($field)
    {
        $this->frm->getField($field)->isValidAgainstRegexp('|^([a-z0-9_-])+$|i', BL::err('InvalidName'));
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
            $methodDisabled = $this->frm->getField('width_null')->isChecked() || $this->frm->getField('height_null')->isChecked() ? true : false;

            if(!$methodDisabled) $this->frm->getField('method')->isFilled(BL::getError('FieldIsRequired'));
            
            $save_width = self::validateResolution('width');
            $save_height = self::validateResolution('height');

            $this->validateKindTitle('kind');

            // no errors?
            if($this->frm->isCorrect())
            {
                // build item
                $item['width'] = $this->frm->getField('width')->getValue();
                $item['width_null'] = $this->frm->getField('width_null')->isChecked() ? 'Y' : 'N';
                $item['height'] = $this->frm->getField('height')->getValue();
                $item['height_null'] = $this->frm->getField('height_null')->isChecked() ? 'Y' : 'N';
                $item['method'] = $methodDisabled ? 'resize' : $this->frm->getField('method')->getValue();
                $item['kind'] = $this->frm->getField('kind')->getValue();

                $id = BackendPhotogalleryModel::insertResolution($item);

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('resolutions') . '&report=added-resolution&highlight=row-' . $id);
            }
        }
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        parent::parse();

        // parse data
        $this->header->addJsData('photogallery', 'allow_watermark', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_watermark', false));
    }
}
