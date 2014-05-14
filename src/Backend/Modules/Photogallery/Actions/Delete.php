<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;

/**
 * Delete action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Execute this action.
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if($this->id !== null && BackendPhotogalleryModel::existsAlbum($this->id))
        {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get all data for the item we want to edit
            $this->record = (array) BackendPhotogalleryModel::getAlbum($this->id);

            // reset some data
            if(empty($this->record)) $this->record['title'] = '';

            // delete record
            $deleted = BackendPhotogalleryModel::deleteAlbum($this->id);
            $emptySetsAfterDelete =  $deleted['empty_set_ids'];
            $setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';
            
            foreach($emptySetsAfterDelete as $id)
            {

                \SpoonDirectory::delete($setsFilesPath . '/frontend/' . $id);
                \SpoonDirectory::delete($setsFilesPath . '/backend/' . $id);
                \SpoonDirectory::delete($setsFilesPath . '/original/' . $id);
                
            }

            // delete search indexes
            if(is_callable(array('BackendSearchModel', 'removeIndex'))) BackendSearchModel::removeIndex($this->getModule(), (int) $this->id);
            
            // deleted, so redirect
            $this->redirect(BackendModel::createURLForAction('index') . '&report=deleted-album&var=' . urlencode($this->record['title']));
        }

        // something went wrong
        else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
    }
}
