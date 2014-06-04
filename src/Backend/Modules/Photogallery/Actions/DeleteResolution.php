<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryHelper;

/**
 * Delete action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class DeleteResolution extends BackendBaseActionDelete
{
    /**
     * Execute this action.
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if($this->id !== null && BackendPhotogalleryModel::existsRes($this->id))
        {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get all data for the item we want to edit
            $this->record = (array) BackendPhotogalleryModel::getResolution($this->id);

            if(!$this->record['allow_delete']) 
                $this->redirect(BackendModel::createURLForAction('resolutions') . '&error=not-allowed-action&var=' . urlencode($this->record['kind']));

            // delete record
            $deleted = BackendPhotogalleryModel::deleteResolution($this->id);

            BackendPhotogalleryHelper::refreshResolution($this->record);
            
            // deleted, so redirect
            $this->redirect(BackendModel::createURLForAction('resolutions') . '&report=deleted-resolution&var=' . urlencode($this->record['kind']));
        }

        // something went wrong
        else $this->redirect(BackendModel::createURLForAction('resolutions') . '&error=non-existing');
    }
}
