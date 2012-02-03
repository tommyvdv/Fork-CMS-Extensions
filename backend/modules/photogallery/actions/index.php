<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This album overview action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryIndex extends BackendBaseActionIndex
{

	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->redirect(BackendModel::createURLForAction('albums'));
	}
}