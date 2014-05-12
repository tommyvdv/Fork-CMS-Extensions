<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This delete extra action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryDeleteExtra extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendPhotogalleryModel::existsExtra($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			$record = BackendPhotogalleryModel::getExtra($this->id);
			$resolutions = BackendPhotogalleryModel::getExtraResolutions($this->id);

			// delete item
			BackendPhotogalleryModel::deleteExtra($this->id);
			$setsFilesPath = FRONTEND_FILES_PATH . '/' . $this->URL->getModule() . '/sets';

			// Delete resolutions
			foreach($resolutions as $resolution)
			{
				// Does the old resolution exists in the database
				$exists = BackendPhotogalleryModel::existsResolution($resolution['width'], $resolution['height'], $resolution['method'] );

				// No
				if(!$exists)
				{
					// Delete old resolutions
					foreach(BackendPhotogalleryModel::getAllSets() as $set)
					{
						$to = $setsFilesPath . '/frontend/' . $set['id'] . '/' . $resolution['width'] . 'x' . $resolution['height'] . '_' . $resolution['method'];
						SpoonDirectory::delete($to);
					}
				}
			}

			// deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('extras') . '&report=deleted-extra');
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('extras') . '&error=non-existing');
	}

}
