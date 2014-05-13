<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;
use Backend\Modules\Photogallery\Engine\Helper as BackendPhotogalleryHelper;

/**
 * Mass action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class MassAction extends BackendBaseAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // action to execute
        $action = \SpoonFilter::getGetValue('action', array('delete','hide','publish'), 'delete');

        // no id's provided
        if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('index') . '&error=no-albums-selected');

        // at least one id
        else
        {
            // redefine id's
            $ids = (array) $_GET['id'];

            // delete comment(s)
            if($action == 'delete')
            {

                $deleted = BackendPhotogalleryModel::deleteAlbum($ids);
                $emptySetsAfterDelete =  $deleted['empty_set_ids'];
                $setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';
                
                foreach($emptySetsAfterDelete as $id)
                {
                    \SpoonDirectory::delete($setsFilesPath . '/frontend/' . $id);
                    \SpoonDirectory::delete($setsFilesPath . '/backend/' . $id);
                    \SpoonDirectory::delete($setsFilesPath . '/original/' . $id);
                }

                foreach($ids as $id)
                {
                    // delete search indexes
                    if(is_callable(array('BackendSearchModel', 'removeIndex'))) BackendSearchModel::removeIndex($this->getModule(), (int) $id);
                }
            }

            // hidden
            elseif($action == 'hide')
            {
                // set new status
                BackendPhotogalleryModel::updateAlbumsHidden($ids);
            }

            // published
            elseif($action == 'publish')
            {
                // set new status
                BackendPhotogalleryModel::updateAlbumsPublished($ids);
            }

            // define report
            $report = (count($ids) > 1) ? 'items-' : 'item-';

            // init var
            if($action == 'delete') $report .= 'deleted';
            if($action == 'hidden') $report .= 'hidden';
            if($action == 'published') $report .= 'published';

            // redirect
            $this->redirect(BackendModel::createURLForAction('index') . '&report=' . $report);
        }
    }
}
