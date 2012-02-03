&lt;?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the {$item.name|ucfirst} module
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class {$item.name|camelcase}Installer extends ModuleInstaller
{

	/**
	 * Install the module
	 */
	public function install()
	{
		$this-&gt;importSQL(dirname(__FILE__) . &#x27;/data/install.sql&#x27;);

		$this-&gt;addModule(&#x27;{$item.name}&#x27;);

		$this-&gt;importLocale(dirname(__FILE__) . &#x27;/data/locale.xml&#x27;);

		$this-&gt;makeSearchable(&#x27;{$item.name}&#x27;);
		$this-&gt;setModuleRights(1, &#x27;{$item.name}&#x27;);

		{iteration:actions}
$this-&gt;setActionRights(1, &#x27;{$item.name}&#x27;, &#x27;{$actions.action}&#x27;);
		{/iteration:actions}

		// set navigation
		$navigationModulesId = $this-&gt;setNavigation(null, &#x27;Modules&#x27;);
		$navigation{$item.name|ucfirst}Id = $this-&gt;setNavigation($navigationModulesId, &#x27;{$item.name|camelcase}&#x27;);
		$this-&gt;setNavigation($navigation{$item.name|ucfirst}Id, &#x27;Overview&#x27;, &#x27;{$item.name}/index&#x27;, array(&#x27;{$item.name}/add&#x27;,	&#x27;{$item.name}/edit&#x27;));
		$this-&gt;setNavigation($navigation{$item.name|ucfirst}Id, &#x27;Categories&#x27;, &#x27;{$item.name}/categories&#x27;, array(&#x27;{$item.name}/add_category&#x27;,	&#x27;{$item.name}/edit_category&#x27;));
		$navigationSettingsId = $this-&gt;setNavigation(null, &#x27;Settings&#x27;);
		$navigationModulesId = $this-&gt;setNavigation($navigationSettingsId, &#x27;Modules&#x27;);
		$this-&gt;setNavigation($navigationModulesId, &#x27;{$item.name|camelcase}&#x27;, &#x27;{$item.name}/settings&#x27;);
		
		// insert extras
		$this-&gt;insertExtra(&#x27;{$item.name}&#x27;, &#x27;block&#x27;, &#x27;{$item.name|camelcase}&#x27;);
	}
}
