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
 * Add widget slideshow action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class AddWidgetSlideshow extends BackendBaseActionAdd
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
        $this->frm = new BackendForm('addWidget');

        // resolution mode
        $this->frm->addDropdown('large_resolution', BackendPhotogalleryModel::getResolutionsForDropdown(), 'large');

        // create elements
        $this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
        $this->frm->addText('large_width');
        $this->frm->addText('large_height');
        $this->frm->addDropdown('large_method', array('crop' => BL::getLabel('Crop'), 'resize' => BL::getLabel('Resize')))->setDefaultElement(\SpoonFilter::ucfirst(BL::getLabel('ChooseAResizeMethod')));
        
        $this->frm->addDropdown('show_caption', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'false');
        $this->frm->addDropdown('show_pagination', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'true');
        $this->frm->addDropdown('show_arrows', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'true');
        $this->frm->addDropdown('pause_on_hover', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'true');
        $this->frm->addDropdown('random', array('false' => ucfirst(BL::getLabel('No')), 'true' => ucfirst(BL::getLabel('Yes'))), 'false');
        $this->frm->addText('slideshow_speed', 7000);
        $this->frm->addText('animation_speed', 600);
        $this->frm->addDropdown('pagination_type', array('bullets' => ucfirst(BL::getLabel('Bullets')), 'numbers' => ucfirst(BL::getLabel('Numbers')) , 'thumbnails' => ucfirst(BL::getLabel('Thumbnails'))), 'bullets');
        
        $this->frm->addDropdown('animation', array('fade' => ucfirst(BL::getLabel('Fade')), 'slide' => ucfirst(BL::getLabel('Slide'))));
        $this->frm->addText('slideshow_item_width', 0);
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
            //self::validateResolution('large_width');
            //self::validateResolution('large_height');
            //$this->frm->getField('large_method')->isFilled(BL::getError('FieldIsRequired'));
        
            $this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));
            $this->frm->getField('slideshow_speed')->isFilled(BL::getError('FieldIsRequired'));
            $this->frm->getField('animation_speed')->isFilled(BL::getError('FieldIsRequired'));

            if($this->frm->getField('slideshow_item_width')->isFilled(BL::getError('FieldIsRequired'))) $this->frm->getField('slideshow_item_width')->isNumeric(BL::getError('InvalidNumber'));

            // no errors?
            if($this->frm->isCorrect())
            {
                $title = $this->frm->getField('title')->getValue();

                // build item
                $item['kind'] = 'widget';
                $item['action'] = 'slideshow';
                $item['allow_delete'] = 'Y';
                $item['created_on'] = BackendModel::getUTCDate();
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
                $item['id'] = BackendPhotogalleryModel::insertExtra($item);

                $resolutionLarge['extra_id'] = $item['id'];
                $resolutionLarge['width'] = $this->frm->getField('large_width')->getValue();
                $resolutionLarge['height'] = $this->frm->getField('large_height')->getValue();
                $resolutionLarge['method'] = $this->frm->getField('large_method')->getValue();
                $resolutionLarge['kind'] = 'large';
                $resolutionLarge['resolution'] = $this->frm->getField('large_resolution')->getValue();

                BackendPhotogalleryModel::insertExtraResolution($resolutionLarge);


                // Create all widgets for each album
                foreach(BackendPhotogalleryModel::getAllAlbums() as $album)
                {
                    
                    $resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($item['id']);

                    $label = $album['title'] . ' | ' . BackendTemplateModifiers::toLabel($item['action']) . ' | '  . $title . ' | ' . $resolutionsLabel;

                    $extra['module'] = $this->getModule();
                    $extra['label'] = ucfirst($item['action']);
                    $extra['action'] = ucfirst($item['action']);
                    $extra['data'] = serialize(
                                        array(
                                            'id' => $album['id'],
                                            'extra_label' => $label,
                                            'extra_id' => $item['id'],
                                            'language' => $album['language'],
                                            'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $album['id']
                                        )
                                    );
                                    
                    $id = BackendPhotogalleryModel::insertModulesExtraWidget($extra);

                    BackendPhotogalleryModel::insertExtraId(array('album_id' => $album['id'], 'extra_id' => $item['id'], 'modules_extra_id' => $id));
                }

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('extras') . '&report=added-widget&highlight=row-' . $item['id']);
            }
        }
    }
}
