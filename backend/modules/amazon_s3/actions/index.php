<?php

/*
 * This file is part of the amazon_s3 module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This index action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendAmazonS3Index extends BackendBaseActionIndex
{

	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->redirect(BackendModel::createURLForAction('settings'));
	}
}