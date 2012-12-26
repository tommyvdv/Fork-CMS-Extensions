<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This mass albums action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryMassAction extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// action to execute
		$action = SpoonFilter::getGetValue('action', array('delete','hide','publish'), 'delete');

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
					SpoonDirectory::delete($setsFilesPath . '/frontend/' . $id);
					SpoonDirectory::delete($setsFilesPath . '/backend/' . $id);
					SpoonDirectory::delete($setsFilesPath . '/original/' . $id);
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