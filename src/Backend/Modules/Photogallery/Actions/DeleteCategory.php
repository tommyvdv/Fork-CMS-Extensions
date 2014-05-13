<?php

namespace Backend\Modules\Photogallery\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Photogallery\Engine\Model as BackendPhotogalleryModel;

/**
 * Delete category action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class DeleteCategory extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if($this->id !== null && BackendPhotogalleryModel::existsCategory($this->id))
        {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get all data for the item we want to edit
            $this->record = (array) BackendPhotogalleryModel::getCategory($this->id);

            // delete item
            BackendPhotogalleryModel::deleteCategory($this->id);

            // deleted, so redirect
            $this->redirect(BackendModel::createURLForAction('categories') . '&report=deleted-category&var=' . urlencode($this->record['title']));
        }

        // something went wrong
        else $this->redirect(BackendModel::createURLForAction('categories') . '&error=non-existing');
    }
}
