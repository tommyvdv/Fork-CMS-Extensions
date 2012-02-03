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
class BackendAmazonS3Model
{
	const DELETE_LOCAL_IN_TIME = '2 weeks';
	const CRONJOB_S3_PUT_LIMIT = 50;
	const CRONJOB_S3_DELETE_LIMIT = 50;
	const CRONJOB_LOCAL_DELETE_LIMIT = 200;


	/**
	 * Deletes one or more items
	 *
	 * @param  mixed $ids The ids to delete.
	 */
	public static function deleteCronjobByData($module, $data)
	{
		// get db
		$db = BackendModel::getDB(true);
			
		$db->delete('amazon_s3_cronjobs', 'module = ? AND data LIKE ?', array((string) $module, '%' . (string) $data . '%'));
	}
	
	/**
	 * Update an existing record
	 *
	 * @param array $item The new data.
	 * @return int
	 */
	public static function updateCronjob(array $item)
	{
		$db = BackendModel::getDB(true);

		// update category
		return $db->update('amazon_s3_cronjobs', $item, 'id = ? ', array((int) $item['id']));
	}

	/**
	 * Checks if an extra exists
	 *
	 * @param string $module The module.
	 * @param string $path The path.
	 * @return bool
	 */
	public static function existsCronjobByFullPath($module, $path)
	{
		return (bool) BackendModel::getDB()->getVar(
					'SELECT i.id
					 FROM amazon_s3_cronjobs AS i
					 WHERE i.module = ? AND i.full_path = ?',
					array((string) $module, (string) $path)
				);
	}

	/**
	 * Delete a cronjob by its full path
	 *
	 * @param string $module The module.
	 * @param string $path The path.
	 * @return void
	 */
	public static function deleteCronjobByFullPath($module, $path)
	{
		// get db
		$db = BackendModel::getDB(true);
			
		$db->delete('amazon_s3_cronjobs', 'module = ? AND full_path = ?', array((string) $module, (string) $path));
	}

	/**
	 * Delete a cronjob by its full path
	 *
	 * @param string $module The module.
	 * @param string $path The path.
	 * @return void
	 */
	public static function deleteCronjobByFullPathLike($module, $path)
	{
		// get db
		$db = BackendModel::getDB(true);
			
		$db->delete('amazon_s3_cronjobs', 'module = ? AND full_path LIKE ?', array((string) $module, (string) $path . '%'));
	}

	/**
	 * Deletes one item
	 *
	 * @param int $ids The id to delete.
	 */
	public static function deleteCronjobById($id)
	{
		// get db
		$db = BackendModel::getDB(true);
			
		$db->delete('amazon_s3_cronjobs', 'id = ?', array((int) $id));
	}

	/**
	 * Delete a cronjob by its full path
	 *
	 * @param string $action The action.
	 * @param string $location The location.
	 * @param int $limit The limit of actions to get
	 * @return array
	 */
	public static function getAllCronjobsByActionAndLocation($action = 'put', $location = 's3', $limit = 100)
	{
		$return =  (array) BackendModel::getDB()->getRecords(
			'SELECT i.*
			FROM amazon_s3_cronjobs AS i
			WHERE i.action = ? AND i.location = ? AND i.execute_on <= ?  LIMIT ?, ?',
			array((string) $action, (string) $location, BackendModel::getUTCDate('Y-m-d H:i') . ':00', 0, (int) $limit));
		
		foreach($return as &$row)
		{
			if($row['data'] !== null) $row['data'] = @unserialize($row['data']);
		}
		
		return $return;
	}

	/**
	 * Inserts to the database
	 *
	 * @param array $item The data to insert.
	 * @return array
	 */
	public static function insertCronjob(array $item)
	{
		$db = BackendModel::getDB(true);

		// insert and return the new id
		$item['id'] = $db->insert('amazon_s3_cronjobs', $item);

		return $item['id'] ;
	}
}