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
class FrontendAmazonS3Helper
{
	/**
	 * Get a setting
	 *
	 * @param string $setting The setting
	 * @return mixed
	 */
	public static function getSetting($setting)
	{
		return (string) FrontendModel::getModuleSetting('amazon_s3', (string) $setting);
	}
}