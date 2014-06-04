<?php

namespace Backend\Modules\Photogallery\Actions;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\File;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\TemplateModifiers as BackendTemplateModifiers;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryHelper;

/**
 * Edit action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class EditResolution extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exists
        if($this->id !== null && BackendPhotogalleryModel::existsRes($this->id))
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

        // no item found, throw an exception, because somebody is fucking with our URL
        else $this->redirect(BackendModel::createURLForAction('resolutions') . '&error=non-existing');
    }

    /**
     * Get the data
     */
    private function getData()
    {
        // get the record
        $this->record = (array) BackendPhotogalleryModel::getResolution($this->id);
        //BackendPhotogalleryHelper::refreshResolution($this->record);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('resolutions') . '&error=non-existing');
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        parent::parse();

        // parse data
        $this->tpl->assign('record', $this->record);
        $this->header->addJsData('photogallery', 'allow_edit', $this->record['allow_edit']);
        $this->header->addJsData('photogallery', 'allow_watermark', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_watermark', false));
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('editResolution');

        $this->methods = array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize'));

        // create elements
        $this->frm->addText('kind', $this->record['kind'], null, 'inputText title', 'inputTextError title');
        $this->frm->addText('width', $this->record['width']);
        $this->frm->addCheckbox('width_null', $this->record['width_null'] == 'Y' ? true : false);
        $this->frm->addText('height', $this->record['height']);
        $this->frm->addCheckbox('height_null', $this->record['height_null'] == 'Y' ? true : false);
        $this->frm->addCheckbox('allow_watermark', $this->record['allow_watermark'] == 'Y' ? true : false);
        $this->frm->addDropdown('method', $this->methods, $this->record['method'])->setDefaultElement(\SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));

        $this->frm->addCheckbox('regenerate', $this->record['regenerate'] == 'Y' ? true : false);

        // set hidden values
        $rbtPositionValues[] = array('label' => ucfirst(BL::getLabel('TopLeft')), 'value' => 1);
        $rbtPositionValues[] = array('label' => ucfirst(BL::getLabel('TopCenter')), 'value' => 2);
        $rbtPositionValues[] = array('label' => ucfirst(BL::getLabel('TopRight')), 'value' => 3);
        $rbtPositionValues[] = array('label' => ucfirst(BL::getLabel('CenterLeft')), 'value' => 4);
        $rbtPositionValues[] = array('label' => ucfirst(BL::getLabel('CenterCenter')), 'value' => 5);
        $rbtPositionValues[] = array('label' => ucfirst(BL::getLabel('CenterRight')), 'value' => 6);
        $rbtPositionValues[] = array('label' => ucfirst(BL::getLabel('BottomLeft')), 'value' => 7);
        $rbtPositionValues[] = array('label' => ucfirst(BL::getLabel('BottomCenter')), 'value' => 8);
        $rbtPositionValues[] = array('label' => ucfirst(BL::getLabel('BottomRight')), 'value' => 9);
        $this->frm->addRadiobutton('position', $rbtPositionValues, $this->record['watermark_position'] ? (int) $this->record['watermark_position'] : NULL);
        $this->frm->addText('watermark_padding', (int) $this->record['watermark_padding']);

        $this->frm->addImage('watermark');
        $this->frm->addCheckbox('delete_watermark');
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

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if($this->frm->isSubmitted() && $this->record['allow_edit'])
        {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate fields
            //$this->frm->getField('kind')->isFilled(BL::getError('FieldIsRequired'));
            $methodDisabled = $this->frm->getField('width_null')->isChecked() || $this->frm->getField('height_null')->isChecked() ? true : false;

            if(!$methodDisabled) $this->frm->getField('method')->isFilled(BL::getError('FieldIsRequired'));
            
            $save_width = self::validateResolution('width');
            $save_height = self::validateResolution('height');

            // no errors?
            if($this->frm->isCorrect())
            {
                // build item
                $item['id'] = $this->id;
                $item['width'] = $this->frm->getField('width')->getValue();
                $item['width_null'] = $this->frm->getField('width_null')->isChecked() ? 'Y' : 'N';
                $item['height'] = $this->frm->getField('height')->getValue();
                $item['height_null'] = $this->frm->getField('height_null')->isChecked() ? 'Y' : 'N';
                $item['method'] = $methodDisabled ? 'resize' : $this->frm->getField('method')->getValue();
                $item['kind'] = $this->frm->getField('kind')->getValue();
                $item['allow_watermark'] = $this->frm->getField('allow_watermark')->isChecked() ? 'Y' : 'N';
                $item['regenerate'] = $this->frm->getField('regenerate')->isChecked() ? 'Y' : 'N';
                $item['watermark_position'] = (int) $this->frm->getField('position')->getValue();
                $item['watermark_padding'] = (int) $this->frm->getField('watermark_padding')->getValue();

                $item = self::uploadWatermark($item);

                $id = BackendPhotogalleryModel::updateResolution($item);

                if($this->frm->getField('regenerate')->isChecked())
                    BackendProductsHelper::refreshResolution($item);

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('resolutions') . '&report=edited-resolution&highlight=row-' . $id);
            }
        }
    }

    private function uploadWatermark($item)
    {
        $item['watermark'] = $this->record['watermark'];

        // the image path
        $imagePath = FRONTEND_FILES_PATH . '/products/watermarks';

        // create folders if needed
        $fs = new Filesystem();
        if(!$fs->exists($imagePath . '/source')) $fs->mkdir($imagePath . '/source');

        // if the image should be deleted
        if($this->frm->getField('delete_watermark')->isChecked())
        {
            $filename = $imagePath . '/source/' . $item['watermark'];
            if(is_file($filename))
            {
                // delete the image
                $fs->remove($filename);
                BackendModel::deleteThumbnails($imagePath, $item['watermark']);
            }

            // reset the name
            $item['watermark'] = null;
        }

        // new image given?
        if($this->frm->getField('watermark')->isFilled())
        {
            $filename = $imagePath . '/source/' . $this->record['watermark'];
            if(is_file($filename))
            {
                $fs->remove($filename);
                BackendModel::deleteThumbnails($imagePath, $this->record['watermark']);
            }

            // build the image name
            $item['watermark'] = $item['kind'] . '.' . $this->frm->getField('watermark')->getExtension();

            // upload the image & generate thumbnails
            $this->frm->getField('watermark')->generateThumbnails($imagePath, $item['watermark']);
        }

        // rename the old image
        elseif($item['watermark'] != null)
        {
            /*
            $image = new File($imagePath . '/source/' . $item['watermark']);
            $newName = $item['kind'] . '.' . $image->getExtension();

            // only change the name if there is a difference
            if($newName != $item['watermark'])
            {
                // loop folders
                foreach(BackendModel::getThumbnailFolders($imagePath, true) as $folder)
                {
                    // move the old file to the new name
                    $fs->rename($folder['path'] . '/' . $item['watermark'], $folder['path'] . '/' . $newName);
                }

                // assign the new name to the database
                $item['watermark'] = $newName;
            }
            */
        }

        return $item;
    }
}
