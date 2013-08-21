<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This add album action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */

class BackendPhotogalleryCopy extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$this->from = $this->getParameter('from');
		$this->to = $this->getParameter('to');

		// validate
		if($this->from == '') throw new BackendException('Specify a from-parameter.');
		if($this->to == '') throw new BackendException('Specify a to-parameter.');

		// copy pages
		BackendPhotogalleryModel::copy($this->from, $this->to);

		// redirect
		$this->redirect(BackendModel::createURLForAction('index') . '&report=copy-added&var=' . urlencode($this->to));

	}

}
