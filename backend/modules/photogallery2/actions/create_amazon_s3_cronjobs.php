<?php

/*
 * This file is part of the projects module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This add cronjobs action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryCreateAmazonS3Cronjobs extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();
		
		if(BackendPhotogalleryHelper::createAmazonS3Cronjobs($this->URL->getModule()))
		{
			$this->redirect(BackendModel::createURLForAction('photogallery') . '&report=added-cronjobs');
		}
		else
		{
			$this->redirect(BackendModel::createURLForAction('photogallery') . '&error=amazon-s3-not-configured');
		}
	}
}