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
 * Edit action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

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
            $this->loadDataGrid();
                
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
     * Get the data
     */
    private function getData()
    {
        // get the record
        $this->record = (array) BackendPhotogalleryModel::getAlbum($this->id);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('edit');
        
        // set hidden values
        $rbtHiddenValues[] = array('label' => BL::getLabel('Hidden', $this->URL->getModule()), 'value' => 'Y');
        $rbtHiddenValues[] = array('label' => BL::getLabel('Published'), 'value' => 'N');
        
        $today = mktime(00, 00, 00);
        
        // categories
        $allowedDepth = BackendModel::getModuleSetting('photogallery', 'categories_depth', 0);
        $allowedDepthStart = BackendModel::getModuleSetting('photogallery', 'categories_depth_start', 0);
        $this->categories = BackendPhotogalleryModel::getCategoriesForDropdown(
            array(
                $allowedDepthStart,
                $allowedDepth == 0 ? 0 : $allowedDepth + 1
            )
        );
        
        // create elements
        $this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
        $this->frm->addEditor('text', $this->record['text']);
        $this->frm->addEditor('introduction', $this->record['introduction']);
        $this->frm->addText('tags', BackendTagsModel::getTags($this->URL->getModule(), $this->record['id']), null, 'inputText tagBox', 'inputTextError tagBox');
        
        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
        
        $categoryIds = ($this->record['category_ids'] != '') ? (array) explode(',', $this->record['category_ids']) : null;
        $this->frm->addDropdown('categories', $this->categories , $categoryIds, true, 'select categoriesBox', 'selectError categoriesBox');
        
        $this->frm->addDate('publish_on_date', $this->record['publish_on']);
        $this->frm->addTime('publish_on_time',  date('H:i', $this->record['publish_on']));
        
        $this->frm->addCheckbox('new', ($this->record['new_from'] !== null || $this->record['new_until']));
        $this->frm->addDate('new_date_from', $this->record['new_from'], null, $today);
        $this->frm->addDate('new_date_until', $this->record['new_until'], null, $today);
        if(!$this->frm->getField('new')->isChecked())
        {
            $this->frm->getField('new_date_from')->setAttribute('disabled', 'disabled');
            $this->frm->getField('new_date_until')->setAttribute('disabled', 'disabled');
        }
        
        $this->frm->addCheckbox('show_in_albums', $this->record['show_in_albums'] === 'Y');

        // meta
        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
        
        $this->frm->addCheckbox('id');
        
        // set callback for generating an unique URL
        $this->meta->setUrlCallback('Backend\Modules\Photogallery\engine\Model', 'getURLForAlbum', array($this->record['id']));
    }

    /**
     * Load the dataGrids
     */
    private function loadDataGrid()
    {
        
        // create dataGrid
        $this->dataGrid = new BackendDataGridDB(BackendPhotogalleryModel::QRY_DATAGRID_BROWSE_IMAGES_FOR_SET, array($this->record['set_id'], $this->id, BL::getWorkingLanguage()));
        $this->dataGrid->setMassActionCheckboxes('checkbox', '[id]');

        // set drag and drop
        $this->dataGrid->enableSequenceByDragAndDrop();

        // disable paging
        $this->dataGrid->setPaging(false);

        // set colum URLs
        //$this->dataGrid->setColumnURL('preview', BackendModel::createURLForAction('edit_image') . '&amp;id=[id]&amp;album_id=' . $this->id);

        // set colums hidden
        // $this->dataGrid->setColumnsHidden(array('category_id', 'sequence'));

        // add edit column
        $this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_image') . '&amp;id=[id]&amp;album_id=' . $this->id, BL::lbl('Edit'));
        
        
        $this->dataGrid->addColumn('preview', \SpoonFilter::ucfirst(BL::lbl('Preview')));
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'getPreviewHTML50x50_crop'), array($this->record['set_id'], $this->getModule(), '[filename]'), 'preview', true);
        $this->dataGrid->setColumnFunction('strip_tags', '[text]', 'text', true);
        
        $this->dataGrid->setColumnFunction(array('Backend\Modules\Photogallery\Engine\Helper', 'translateBoolean'), array('[is_hidden]'), 'is_hidden', true);
        $this->dataGrid->setColumnFunction(create_function('$title_hidden, $title','return $title_hidden == "Y" ? "" : $title;'),array('[title_hidden]','[title]'),'title',true);

        // make sure the column with the handler is the first one
        $this->dataGrid->setColumnsSequence('dragAndDropHandle','checkbox','preview','is_hidden','title','text','edit');
        
        // Hidden
        $this->dataGrid->setColumnsHidden(array('filename', 'title_hidden'));

        // add a class on the handler column, so JS knows this is just a handler
        $this->dataGrid->setColumnAttributes('dragAndDropHandle', array('class' => 'dragAndDropHandle'));

        // our JS needs to know an id, so we can send the new order
        $this->dataGrid->setRowAttributes(array('id' => '[id]'));
        
        $this->dataGrid->setAttributes(array('data-action' => "images_sequence"));
        
        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown('action', array('delete' => BL::getLabel('Delete'), 'hide' => BL::getLabel('Hide'), 'publish' => BL::getLabel('Publish')), 'delete');
        $ddmMassAction->setAttribute('id', 'actionDelete');
        $this->dataGrid->setMassAction($ddmMassAction);
        $this->frm->add($ddmMassAction);
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        // call parent
        parent::parse();
        
        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
        $url404 = BackendModel::getURL(404);

        // parse additional variables
        if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);

        // fetch proper slug
        $this->record['url'] = $this->meta->getURL();
        
        $this->tpl->assign('record', $this->record);
        
        $this->tpl->assign('categories', $this->categories);
        
        // parse dataGrid
        $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
        
        $this->tpl->assign('add_images_choose_url', BackendModel::createURLForAction('add_images_choose') . '&amp;album_id=' . $this->id);
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
                $item['meta_id'] = $this->meta->save(true);
                $item['language'] = BL::getWorkingLanguage();
                $item['title'] = $this->frm->getField('title')->getValue();
                $item['introduction'] = $this->frm->getField('introduction')->getValue();
                $item['text'] = $this->frm->getField('text')->getValue();
                $item['edited_on'] = BackendModel::getUTCDate();
                $item['hidden'] = $this->frm->getField('hidden')->getValue();
                $item['show_in_albums'] = $this->frm->getField('show_in_albums')->isChecked() ? 'Y' : 'N';
                $item['publish_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('publish_on_date'), $this->frm->getField('publish_on_time')));
                $item['id'] = $this->id;
                
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
                
                $action = $this->frm->getField('action')->getValue();
                $ids = (array) $_POST['id'];

                // save the tags
                BackendTagsModel::saveTags($item['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());
                
                // Update the widgets
                
                
                foreach(BackendPhotogalleryModel::getExtraIdsForAlbum($this->id) as $extra)
                {
                    $resolutionsLabel = BackendPhotogalleryHelper::getResolutionsForExtraLabel($extra['extra_id']);
                    
                    $album = BackendPhotogalleryModel::getAlbum($extra['album_id']);
                        
                    $label = $item['title'] . ' | ' . BackendTemplateModifiers::toLabel($extra['action']) . ' | ' . $resolutionsLabel;
                    
                    $extraItem['label'] = $extra['action'];
                    $extraItem['id'] = $extra['modules_extra_id'];
                    $extraItem['data'] = serialize(array('id' => $this->id,
                                                        'extra_label' => $label,
                                                        'extra_id' => $extra['extra_id'],
                                                        'language' => BL::getWorkingLanguage(),
                                                        'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $this->id));
                
                    BackendPhotogalleryModel::updateModulesExtraWidget($extraItem);
                }
                
                
                BackendSearchModel::saveIndex('photogallery', $item['id'], array('title' => $item['title'], 'text' => $item['text']));

                // ping
                if(BackendModel::getModuleSetting($this->getModule(), 'ping_services', false)) BackendModel::ping(SITE_URL . BackendModel::getURLForBlock('photogallery', 'detail') . '/' . $this->meta->getURL());
                
                // save the item
                $id = BackendPhotogalleryModel::updateAlbum($item);
            
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

                BackendPhotogalleryModel::updateSetStatistics($this->record['set_id']);
                
                    
                // Mass action for images
                if($action == 'delete')
                {
                    // Delete files
                    $setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';
                    
                    foreach($ids as $id)
                    {
                        $image = BackendPhotogalleryModel::getImageWithContent($id, $this->id);
                    
                        // Backend resolutions
                        foreach(BackendPhotogalleryModel::$backendResolutions as $resolution)
                        {
                            \SpoonFile::delete($setsFilesPath . '/backend/' . $this->record['set_id'] . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'] . '/' . $image['filename']); 
                        }

                        // Delete original
                        \SpoonFile::delete($setsFilesPath . '/original/' . $this->record['set_id'] . '/' . $image['filename']); 

                        // Frontend image
                        $resolutions = BackendPhotogalleryModel::getUniqueExtrasResolutions();
                        foreach($resolutions as $resolution)
                        {
                            \SpoonFile::delete($setsFilesPath . '/frontend/' . $this->record['set_id'] . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'] . '/' . $image['filename']);
                        }
                    }
                    
                    $deleted = BackendPhotogalleryModel::deleteImage($ids);
                    $emptySetsAfterDelete =  $deleted['empty_set_ids'];
                    
                    // Delete empty sets
                    foreach($emptySetsAfterDelete as $id)
                    {
                        \SpoonDirectory::delete($setsFilesPath . '/' . $id);
                    }
                    
                    BackendPhotogalleryModel::updateSetStatistics($this->record['set_id']);
                    
                    // everything is saved, so redirect to the overview
                    $this->redirect(BackendModel::createURLForAction('edit') . '&report=edited-album&var=' . urlencode($item['title']) . '&id=' . $this->id . '#tabImages');
                    
                } 
                elseif($action == 'hide')
                {
                    // set new status
                    BackendPhotogalleryModel::updateImagesHidden($ids);
                    
                    BackendPhotogalleryModel::updateSetStatistics($this->record['set_id']);
                    
                    $this->redirect(BackendModel::createURLForAction('edit') . '&report=edited-album&var=' . urlencode($item['title']) . '&id=' . $this->id . '#tabImages');
                }
                elseif($action == 'publish')
                {
                    // set new status
                    BackendPhotogalleryModel::updateImagesPublished($ids);
                    
                    BackendPhotogalleryModel::updateSetStatistics($this->record['set_id']);
                    
                    $this->redirect(BackendModel::createURLForAction('edit') . '&report=edited-album&var=' . urlencode($item['title']) . '&id=' . $this->id . '#tabImages');
                }

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('edit') . '&report=edited-album&var=' . urlencode($item['title']) . '&id=' . $this->id);
            }
        }
    }
}
