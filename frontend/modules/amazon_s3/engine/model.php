<?php

/*
 * This file is part of the amazon_s3 module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class FrontendAmazonS3Model
{

	/**
	 * Checks if an extra exists
	 *
	 * @param string $module The module.
	 * @param string $path The path.
	 * @param string $action The action.
	 * @return bool
	 */
	public static function existsCronjobPutByFullPath($module, $path, $action = 'put')
	{
		return (bool) FrontendModel::getDB()->getVar(
					'SELECT i.id
					 FROM amazon_s3_cronjobs AS i
					 WHERE i.module = ?, i.full_path = ? AND action = ?',
					array((string) $module, (string) $path, (string) $action)
				);
	}	
}