<?php

namespace Backend\Modules\Photogallery\Actions;

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
 * Edit widget slideshow action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class EditWidgetSlideshow extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exists
        if($this->id !== null && BackendPhotogalleryModel::existsExtra($this->id))
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
        else $this->redirect(BackendModel::createURLForAction('extras') . '&error=non-existing');
    }

    /**
     * Get the data
     */
    private function getData()
    {
        // get the record
        $this->record = (array) BackendPhotogalleryModel::getExtra($this->id);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

        $this->large = BackendPhotogalleryModel::getExtraResolutionForKind($this->id, 'large');
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        parent::parse();
        $this->record['allow_delete'] = $this->record['allow_delete'] == 'Y' ? true : false;

        $this->tpl->assign('item', $this->record);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('editWidget');

        $this->frm->addText('title', $this->record['data']['settings']['title'], null, 'inputText title', 'inputTextError title');

        $this->frm->addText('large_width', $this->large['width']);
        $this->frm->addText('large_height', $this->large['height']);
        $this->frm->addDropdown('large_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')), $this->large['method'])->setDefaultElement(\SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));
        
        $this->frm->addDropdown('show_caption', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_caption']);
        $this->frm->addDropdown('show_pagination', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_pagination']);
        $this->frm->addDropdown('show_arrows', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['show_arrows']);
        $this->frm->addDropdown('pause_on_hover', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['pause_on_hover']);
        $this->frm->addDropdown('random', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), $this->record['data']['settings']['random']);
        $this->frm->addText('slideshow_speed', $this->record['data']['settings']['slideshow_speed']);
        $this->frm->addText('animation_speed', $this->record['data']['settings']['animation_speed']);
        $this->frm->addDropdown('pagination_type', array('bullets' => ucfirst(BL::getLabel('Bullets')), 'numbers' => ucfirst(BL::getLabel('Numbers')) , 'thumbnails' => ucfirst(BL::getLabel('Thumbnails'))), $this->record['data']['settings']['pagination_type']);
        
        $this->frm->addDropdown('animation', array('fade' => ucfirst(BL::getLabel('Fade')), 'slide' => ucfirst(BL::getLabel('Slide'))), $this->record['data']['settings']['animation']);
        $this->frm->addText('slideshow_item_width', $this->record['data']['settings']['slideshow_item_width']);
    }

    /**
     * Validate the resolution
     *
     * @param string $field The field to validate
     */
    private function validateResolution($field)
    {
        if($this->frm->getField($field)->isFilled(BL::getError('FieldIsRequired')))
        {
            if($this->frm->getField($field)->isFloat(BL::getError('InvalidNumber')))
            {
                $this->frm->getField($field)->isGreaterThan(0, \SpoonFilter::ucfirst(BL::getError('FieldMustBeGreatherThenZero')));
            }
        }
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
            self::validateResolution('large_width');
            self::validateResolution('large_height');

            $this->frm->getField('large_method')->isFilled(BL::getError('FieldIsRequired'));

            $this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));
            $this->frm->getField('slideshow_speed')->isFilled(BL::getError('FieldIsRequired'));
            $this->frm->getField('animation_speed')->isFilled(BL::getError('FieldIsRequired'));

            if($this->frm->getField('slideshow_item_width')->isFilled(BL::getError('FieldIsRequired'))) $this->frm->getField('slideshow_item_width')->isNumeric(BL::getError('InvalidNumber'));

            // no errors?
            if($this->frm->isCorrect())
            {
                $title = $this->frm->getField('title')->getValue();

                // build item
                $item['id'] = $this->id;
                $item['edited_on'] = BackendModel::getUTCDate();
                $item['data'] = serialize(
                                    array(
                                        'settings' => array(
                                                'title' => $title,
                                                'show_caption' => $this->frm->getField('show_caption')->getValue(),
                                                'show_pagination' => $this->frm->getField('show_pagination')->getValue(),
                                                'show_arrows' => $this->frm->getField('show_arrows')->getValue(),
                                                'pause_on_hover' => $this->frm->getField('pause_on_hover')->getValue(),
                                                'random' => $this->frm->getField('random')->getValue(),
                                                'slideshow_speed' => $this->frm->getField('slideshow_speed')->getValue(),
                                                'animation_speed' => $this->frm->getField('animation_speed')->getValue(),
                                                'pagination_type' => $this->frm->getField('pagination_type')->getValue(),
                                                'animation' => $this->frm->getField('animation')->getValue(),
                                                'slideshow_item_width' => $this->frm->getField('slideshow_item_width')->getValue(),
                                        )
                                    )
                                );

                // insert the item
                BackendPhotogalleryModel::updateExtra($item);

                $resolutionLarge['width'] = $this->frm->getField('large_width')->getValue();
                $resolutionLarge['height'] = $this->frm->getField('large_height')->getValue();
                $resolutionLarge['method'] = $this->frm->getField('large_method')->getValue();
                $resolutionLarge['kind'] = 'large';
                $resolutionLarge['id'] = $this->large['id'];

                $setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';
                $extraHasChanged = false;

                // --------

                // The thumbnail settings changed!
                if($resolutionLarge['width'] != $this->large['width'] || $resolutionLarge['height'] != $this->large['height'] || $resolutionLarge['method'] != $this->large['method'])
                {
                    $extraHasChanged = true;

                    // Does the updated one exists in the database
                    $exists = BackendPhotogalleryModel::existsResolution($resolutionLarge['width'], $resolutionLarge['height'], $resolutionLarge['kind']);

                    // No, generate the new images
                    if(!$exists)
                    {
                        foreach(BackendPhotogalleryModel::getAllImages() as $image)
                        {
                            $from = $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
                            
                            \SpoonDirectory::create(FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id']);
                            
                            $from = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets/original/' . $image['set_id'] . '/' . $image['filename'];
                            
                            $to = $setsFilesPath . '/frontend/' . $image['set_id'] . '/' . $resolutionLarge['width'] . 'x' . $resolutionLarge['height'] . '_' . $resolutionLarge['method'] . '/' . $image['filename'];

                            // Does the source file exists?
                            if(\SpoonFile::exists($from))
                            {
                                $resize = $resolutionLarge['method'] == 'resize' ? true : false;
                                $thumb = new \SpoonThumbnail($from, $resolutionLarge['width'] , $resolutionLarge['height']);
                                $thumb->setAllowEnlargement(true);
                                $thumb->setForceOriginalAspectRatio($resize);
                                $thumb->parseToFile($to);
                            }
                        }
                    }

                    // Update the resolution
                    BackendPhotogalleryModel::updateExtraResolution($resolutionLarge);

                    // Does the old resolution exists in the database
                    $existsOldResolution = BackendPhotogalleryModel::existsResolution($this->large['width'], $this->large['height'], $this->large['method'] );

                    // No, generate the new images
                    if(!$exists)
                    {
                        // Delete old resolutions
                        foreach(BackendPhotogalleryModel::getAllSets() as $set)
                        {
                            $to = $setsFilesPath . '/frontend/' . $set['id'] . '/' . $this->large['width'] . 'x' . $this->large['height'] . '_' . $this->large['method'];
                            \SpoonDirectory::delete($to);
                        }
                    }
                }

                
                // Get all module_extra_ids for the extra and loop them
                foreach(BackendPhotogalleryModel::getAllModuleExtraIds($this->id) as $extra)
                {
                    $resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($extra['extra_id']);

                    $album = BackendPhotogalleryModel::getAlbum($extra['album_id']);
                    

                    $label = $album['title'] . ' | ' . BackendTemplateModifiers::toLabel($this->record['action']) . ' | '  . $title . ' | ' . $resolutionsLabel;

                    $extraItem['label'] = $this->record['action'];
                    $extraItem['id'] = $extra['modules_extra_id'];
                    $extraItem['data'] = serialize(array('id' => $extra['album_id'],
                                                        'extra_id' => $extra['extra_id'],
                                                        'extra_label' => $label,
                                                        'language' => $album['language'],
                                                        'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $extra['album_id']));
                
                    BackendPhotogalleryModel::updateModulesExtraWidget($extraItem);
                }
                

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('edit_widget_slideshow') . '&report=edited-widget&id=' . $this->record['id']);
            }
        }
    }
}
