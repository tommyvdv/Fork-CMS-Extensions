<?php

/*
 * This file is part of the products module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This mass products action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendProductsMassAction extends BackendBaseAction
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
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('index') . '&error=no-products-selected');

		// at least one id
		else
		{
			// redefine id's
			$ids = (array) $_GET['id'];

			// delete comment(s)
			if($action == 'delete')
			{

				$deleted = BackendProductsModel::deleteProduct($ids);
				$emptySetsAfterDelete =  $deleted['empty_set_ids'];
				$setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';
				
				if(BackendProductsHelper::existsAmazonS3())
				{
					// First the cronjobs need to be deleted.
					foreach($emptySetsAfterDelete as $id)
					{
						// Are there any cronjobs with the same prefix? Delete them
						BackendAmazonS3Model::deleteCronjobByFullPathLike($this->URL->getModule(), $this->URL->getModule() . '/sets/' . $id);
					}
				}
				

				foreach($emptySetsAfterDelete as $id)
				{
					SpoonDirectory::delete($setsFilesPath . '/frontend/' . $id);
					SpoonDirectory::delete($setsFilesPath . '/backend/' . $id);
					SpoonDirectory::delete($setsFilesPath . '/original/' . $id);

					$cronjob = array();
					$cronjob['module'] = $this->URL->getModule();
					$cronjob['path'] = $this->URL->getModule() . '/sets/' . $id;
					$cronjob['full_path'] = $cronjob['path'] ;
					$cronjob['data'] = serialize(array('set_id' => $id, 'image_id' => null));
					$cronjob['action'] = 'delete';
					$cronjob['location'] = 's3';
					$cronjob['created_on'] =  BackendModel::getUTCDate();
					$cronjob['execute_on'] = BackendModel::getUTCDate();
					if(BackendProductsHelper::existsAmazonS3()) BackendAmazonS3Model::insertCronjob($cronjob);
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
				BackendProductsModel::updateProductsHidden($ids);
			}

			// published
			elseif($action == 'publish')
			{
				// set new status
				BackendProductsModel::updateProductsPublished($ids);
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
