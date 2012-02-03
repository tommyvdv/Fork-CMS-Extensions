<?php

/**
 * BackendModulemanagerModel
 * In this file we store all generic functions that we will be using in the module_manager module
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerModel
{
	
	const QRY_DATAGRID_BROWSE_ACTIONS = 'SELECT i.id, i.action, g.name as group_name, i.level
										FROM groups_rights_actions AS i
										INNER JOIN groups as g ON i.group_id = g.id
										WHERE i.module = ?';
	/**
	 * Modules which are part of the core and can not be managed.
	 *
	 * @var	array
	 */
	private static $ignoredModules = array(
		'authentication', 'dashboard',
		'error', 'extensions', 'settings'
	);
	
	/**
	 * Get modules based on the directory listing in the backend application.
	 *
	 * If a module contains a info.xml it will be parsed.
	 *
	 * @return array
	 */
	public static function getModules()
	{
		// get installed modules
		$installedModules = (array) BackendModel::getDB()->getRecords('SELECT name FROM modules', null, 'name');

		// get modules present on the filesystem
		$modules = SpoonDirectory::getList(BACKEND_MODULES_PATH);

		// all modules that are managable in the backend
		$managableModules = array();

		// get more information for each module
		foreach($modules as $moduleName)
		{
			// skip ignored modules
			if(in_array($moduleName, self::$ignoredModules)) continue;

			// init module information
			$module = array();
			$module['id'] = 'module_' . $moduleName;
			$module['raw_name'] = $moduleName;
			$module['name'] = ucfirst(BL::getLabel(SpoonFilter::toCamelCase($moduleName)));
			$module['installed'] = false;

			// the module is present in the database, that means its installed
			if(isset($installedModules[$moduleName])) $module['installed'] = true;

			// add to list of managable modules
			$managableModules[] = $module;
		}

		return $managableModules;
	}
	
	
	/**
	 * Install a module.
	 *
	 * @param string $module The name of the module to be installed.
	 */
	public static function installModule($module)
	{
		// we need the installer
		require_once BACKEND_CORE_PATH . '/installer/installer.php';
		require_once BACKEND_MODULES_PATH . '/' . $module . '/installer/installer.php';

		// installer class name
		$class = SpoonFilter::toCamelCase($module) . 'Installer';

		// possible variables available for the module installers
		$variables = array();

		// init installer
		$installer = new $class(
			BackendModel::getDB(true),
			BL::getActiveLanguages(),
			array_keys(BL::getInterfaceLanguages()),
			false,
			$variables
		);

		// execute installation
		$installer->install();

		// clear the cache so locale (and so much more) gets rebuilded
		self::clearCache();
	}
	
	
	/**
	 * Clear all applications cache.
	 *
	 * Note: we do not need to rebuild anything, the core will do this when noticing the cache files are missing.
	 */
	public static function clearCache()
	{
		// list of cache files to be deleted
		$filesToDelete = array();

		// backend navigation
		$filesToDelete[] = BACKEND_CACHE_PATH . '/navigation/navigation.php';

		// backend locale
		foreach(SpoonFile::getList(BACKEND_CACHE_PATH . '/locale', '/\.php$/') as $file)
		{
			$filesToDelete[] = BACKEND_CACHE_PATH . '/locale/' . $file;
		}

		// frontend navigation
		foreach(SpoonFile::getList(FRONTEND_CACHE_PATH . '/navigation', '/\.(php|js)$/') as $file)
		{
			$filesToDelete[] = FRONTEND_CACHE_PATH . '/navigation/' . $file;
		}

		// frontend locale
		foreach(SpoonFile::getList(FRONTEND_CACHE_PATH . '/locale', '/\.php$/') as $file)
		{
			$filesToDelete[] = FRONTEND_CACHE_PATH . '/locale/' . $file;
		}

		// delete the files
		foreach($filesToDelete as $file) SpoonFile::delete($file);
	}
	
	/**
	 * Check if an action exists
	 *
	 * @return	boolean
	 * @param	int $id	The id of the action.
	 */
	public static function actionExists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM groups_rights_actions AS i
														WHERE i.id = ?',
														array((int) $id));
	}



	/**
	 * Delete a module
	 *
	 * @return	void
	 * @param	string $module		The name of the module.
	 */
	public static function delete($module)
	{
		$db = BackendModel::getDB();
		$module = (string) $module;

		$db->delete('modules', 'name = ?', array($module));
		$db->delete('modules_settings', 'module = ?', array($module));
		$db->delete('groups_rights_actions', 'module = ?', array($module));
		$db->delete('groups_rights_modules', 'module = ?', array($module));
		$db->delete('modules_tags', 'module = ?', array($module));
		$db->delete('locale', 'module = ?', array($module));
		$db->delete('search_index', 'module = ?', array($module));

		BackendModel::deleteExtra($module);
		
		self::clearCache();
	}


	/**
	 * Insert the action in the database
	 *
	 * @return	void
	 * @param	int $id		The id of the action.
	 */
	public static function deleteAction($id)
	{
		BackendModel::getDB()->delete('groups_rights_actions', 'id = ?', array((int) $id));
	}


	/**
	 * Check if a module exists
	 *
	 * @return	boolean
	 * @param	string $module	The module name.
	 */
	public static function exists($module)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.name
														FROM modules AS i
														WHERE i.name = ?',
														array((string) $module));
	}


	/**
	 * Get the module
	 *
	 * @return	array
	 * @param	string $module	The module name.
	 */
	public static function get($module)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
															FROM modules AS i
															WHERE i.name = ? LIMIT 1',array((string) $module));
	}


	/**
	 * Get an action
	 *
	 * @return	array
	 * @param	int $id	The id of the action.
	 */
	public static function getAction($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
														FROM groups_rights_actions AS i
														WHERE i.id = ? LIMIT 1',
														array((int) $id));
	}


	/**
	 * Get all the right groups for a dropdown.
	 *
	 * @return	array
	 */
	public static function getGroupsForDropdown()
	{
		return (array) BackendModel::getDB()->getPairs('SELECT i.id, i.name
															FROM groups AS i');
	}


	/**
	 * Get the missing moddule actions
	 *
	 * @return	array
	 * @param	string $module	The module name.
	 */
	public static function getMissingActions($module)
	{
		$module_actions_array = self::getModuleActionsFromFiles($module);
		$actions_from_database = BackendModulemanagerModel::getModuleActions($module);
		
		foreach($module_actions_array as $key => $existing_action_file)
		{

			if(in_array($existing_action_file['action'], $actions_from_database))
			{
				unset($module_actions_array[$key]);
			}
		}
		
		return $module_actions_array;
	}
	
	
	public static function getModuleActionsFromFiles($module)
	{
		$module_actions_path = BACKEND_MODULES_PATH . '/' . $module . '/actions';
		$action_files = SpoonFile::getlist($module_actions_path, '/(.*).php/');
		
		$module_ajax_path = BACKEND_MODULES_PATH . '/' . $module . '/ajax';
		$ajax_files = SpoonFile::getlist($module_ajax_path, '/(.*).php/');
		
		$module_actions_array = array();
		
		foreach($action_files as $file)
		{
			$path = $module_actions_path . '/' . $file;
			$fileInfo = SpoonFile::getInfo($path);
			$module_actions_array[] = array('file' => $file, 'path' => $path, 'action' => $fileInfo['name']);
		}

		foreach($ajax_files as $file)
		{
			$path = $module_ajax_path . '/' . $file;
			$fileInfo = SpoonFile::getInfo($path);
			$module_actions_array[] = array('file' => $file, 'path' => $path, 'action' => $fileInfo['name']);
		}

		return $module_actions_array;
	}


	/**
	 * Get all the actions of a module
	 *
	 * @return	array
	 * @param	string $module	The module name.
	 */
	public static function getModuleActions($module)
	{
		return (array) BackendModel::getDB()->getColumn('SELECT i.action
															FROM groups_rights_actions AS i
															WHERE i.module = ?',array((string) $module));
	}


	/**
	 * Get all the modules groups for a dropdown.
	 *
	 * @return	array
	 */
	public static function getModulesForDropdown()
	{
		return (array) BackendModel::getDB()->getPairs('SELECT i.name AS id, i.name FROM modules AS i');
	}


	/**
	 * Insert the action in the database
	 *
	 * @return	int
	 * @param	array $item		The data that need to be inserted.
	 */
	public static function insertAction(array $item)
	{
		return BackendModel::getDB(true)->insert('groups_rights_actions', $item);
	}



	/**
	 * Check if the action exists for a specific group, module and level
	 *
	 * @return	boolean
	 * @param	int $group_id	The id of the group.
	 * @param	string $action	The name of the actions.
	 * @param	string $module	The name of the module.
	 * @param	int $level		The level of the action.
	 */
	public static function rightActionExists($group_id, $action, $module, $level)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM groups_rights_actions AS i
														WHERE i.group_id = ? AND i.module = ? AND i.action = ? AND i.level = ?',
														array((int) $group_id, (string) $module, (string) $action,(int) $level));
	}


	/**
	 * Update the action
	 *
	 * @return	id
	 * @param	array $item		An array of data.
	 */
	public static function updateAction(array $item)
	{
		BackendModel::getDB(true)->update('groups_rights_actions', $item, 'id = ?', array( (int) $item['id']));
	}
	

}

?>