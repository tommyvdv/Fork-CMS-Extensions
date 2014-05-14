<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryHelper;

/**
 * Edit image action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class EditImage extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');
        $this->album_id = $this->getParameter('album_id', 'int');

        // does the item exists
        if($this->id !== null && BackendPhotogalleryModel::existsImage($this->id))
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
     * Get the data
     */
    private function getData()
    {
        // get the record
        $this->record = (array) BackendPhotogalleryModel::getImageWithContent($this->id, $this->album_id);

        // no item found, throw an exceptions, because somebody is fucking with our URL
        if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('edit');

        // set hidden values
        $rbtHiddenValues[] = array('label' => BL::getLabel('Hidden', $this->URL->getModule()), 'value' => 'Y');
        $rbtHiddenValues[] = array('label' => BL::getLabel('Published'), 'value' => 'N');

        $this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
        $this->frm->addEditor('text', $this->record['text']);
        $this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);

        $this->frm->addCheckbox('title_hidden', $this->record['title_hidden'] == 'Y');

        // link
        $linkValue = 'none';
        if(isset($this->record['data']['internal_link']['page_id'])) $linkValue = 'internal';
        if(isset($this->record['data']['external_link']['url'])) $linkValue = 'external';
        if(isset($this->record['data']['iframe']['url'])) $linkValue = 'iframe';
        if(isset($this->record['data']['embed']['code'])) $linkValue = 'embed';

        $linkValues = array(
            array('value' => 'none', 'label' => \SpoonFilter::ucfirst(BL::lbl('None'))),
            array('value' => 'internal', 'label' => \SpoonFilter::ucfirst(BL::lbl('InternalLink')), 'variables' => array('isInternal' => true)),
            array('value' => 'external', 'label' => \SpoonFilter::ucfirst(BL::lbl('ExternalLink')), 'variables' => array('isExternal' => true)),
            array('value' => 'embed', 'label' => \SpoonFilter::ucfirst(BL::lbl('Embed')), 'variables' => array('isEmbed' => true)),
            array('value' => 'iframe', 'label' => \SpoonFilter::ucfirst(BL::lbl('Iframe')), 'variables' => array('isIframe' => true)),
        );
        $this->frm->addRadiobutton('link', $linkValues, $linkValue);
        $this->frm->addDropdown('internal_link', BackendPagesModel::getPagesForDropdown(), ($linkValue == 'internal') ? $this->record['data']['internal_link']['page_id'] : null);
        $this->frm->addText('external_link', ($linkValue == 'external') ? $this->record['data']['external_link']['url'] : null, null, null, null, true);

        $this->frm->addText('iframe', ($linkValue == 'iframe') ? $this->record['data']['iframe']['url'] : null, null, null, null, true);
        $this->frm->addText('embed', ($linkValue == 'embed') ? $this->record['data']['embed']['code'] : null, null, null, null, true);

        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);

        // set callback for generating an unique URL
        $this->meta->setUrlCallback('Backend\Modules\Photogallery\engine\Model', 'getURLForImage', array($this->record['image_content_id']));
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        // call parent
        parent::parse();

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'image');
        $url404 = BackendModel::getURL(404);

        // parse additional variables
        if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);

        // fetch proper slug
        $this->record['url'] = $this->meta->getURL();


        $this->tpl->assign('record', $this->record);
        $this->tpl->assign('album_id', $this->album_id);

        $this->tpl->assign('previewImageHTML', BackendPhotogalleryHelper::getPreviewHTML128x128_crop($this->record['set_id'], $this->getModule(), $this->record['filename']));
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

            // validate meta

            //$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));

            // validate redirect
            $linkValue = $this->frm->getField('link')->getValue();
            if($linkValue == 'internal') $this->frm->getField('internal_link')->isFilled(BL::err('FieldIsRequired'));
            if($linkValue == 'external') $this->frm->getField('external_link')->isURL(BL::err('InvalidURL'));
            if($linkValue == 'iframe') $this->frm->getField('iframe')->isURL(BL::err('InvalidURL'));
            if($linkValue == 'embed') $this->frm->getField('embed')->isFilled(BL::err('FieldIsRequired'));

            // no errors?
            if($this->frm->isCorrect())
            {
                // init var
                $data = null;

                // build data
                if($linkValue == 'internal') $data['internal_link'] = array('page_id' => $this->frm->getField('internal_link')->getValue());
                if($linkValue == 'external') $data['external_link'] = array('url' => $this->frm->getField('external_link')->getValue());
                if($linkValue == 'iframe') $data['iframe'] = array('url' => $this->frm->getField('iframe')->getValue());
                if($linkValue == 'embed') $data['embed'] = array('code' => $this->frm->getField('embed')->getValue());

                // build item
                $content['meta_id'] = $this->meta->save(true);
                $content['title'] = $this->frm->getField('title')->getValue();
                $content['text'] = $this->frm->getField('text')->getValue();
                $content['title_hidden'] = $this->frm->getField('title_hidden')->isChecked() ? 'Y' : 'N';
                $content['edited_on'] = BackendModel::getUTCDate();
                $content['set_image_id'] = $this->id;
                $content['album_id'] = $this->album_id;
                $content['data'] = ($data !== null) ? serialize($data) : null;

                $item['edited_on'] = BackendModel::getUTCDate();
                $item['hidden'] = $this->frm->getField('hidden')->getValue();
                $item['id'] = $this->id;

                // update the item
                $item['id'] = BackendPhotogalleryModel::updateImage($item, $content);

                // Update some statistics
                BackendPhotogalleryModel::updateSetStatistics($this->record['set_id']);


                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('edit_image') . '&report=edited-image&album_id=' . $this->album_id . '&id=' . $this->id );
            }
        }
    }
}
