<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This config class
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
final class BackendPhotogalleryConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var string
	 */
	protected $defaultAction = 'index';


	/**
	 * The disabled actions
	 *
	 * @var array
	 */
	protected $disabledActions = array();
	
	/**
	 * Check if all required settings have been set
	 *
	 * @param string $module The module.
	 */
	public function __construct($module)
	{
		parent::__construct($module);
		$this->loadEngineFiles();
	}

	/**
	 * Loads additional engine files
	 */
	private function loadEngineFiles()
	{
		require_once 'engine/helper.php';
	}
}