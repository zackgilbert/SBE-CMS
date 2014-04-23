<?php

	function call_plugin_func($_plugin, $_function, $_param = false) {
		load_plugin($_plugin);
		return call_user_func($_function, $_param);
	} // call_plugin_func
	
	function get_available_plugins() {	
		if (($_plugins = get_var('_available_plugins')) && is_array($_plugins)) return $_plugins;
		$_plugins = array();

		if (is_dir(ABSPATH . 'plugins') && ($handle = opendir(ABSPATH . 'plugins'))) {

			while (false !== ($_plugin = readdir($handle))) {
				if ((substr($_plugin, 0, 1) !== '.') && is_dir(PLUGINS . $_plugin)) {
					//if (is_file(PLUGINS . $_plugin . '/include.php')) {
						$_plugins[] = $_plugin;
					//}
				}
			}
			
			sort($_plugins);
			closedir($handle);
		}
		
		set_var('_available_plugins', $_plugins);
		return $_plugins;
	} // get_available_plugins
	
	function get_installed_plugins() {
		if (($_plugins = get_var('_installed_plugins')) && is_array($_plugins)) return $_plugins;
		$_plugins = array();

		if (is_dir(ABSPATH . 'plugins') && ($handle = opendir(ABSPATH . 'plugins'))) {

			while (false !== ($_plugin = readdir($handle))) {
				if ((substr($_plugin, 0, 1) !== '.') && is_dir(PLUGINS . $_plugin)) {
					if (!file_exists(PLUGINS . $_plugin . '/install.sql') || is_file(PLUGINS . $_plugin . '/_installed.php')) {
						$_plugins[] = $_plugin;
					}
				}
			}
			
			sort($_plugins);
			closedir($handle);
		}
		set_var('_installed_plugins', $_plugins);
		return $_plugins;		
	} // get_installed_plugins
	
	function get_loaded_plugins() {
		$_plugins = get_var('_loaded_plugins');
		if (!is_array($_plugins))
			$_plugins = array();
		return $_plugins;
	} // get_loaded_plugins
	
	function get_page_type_plugins() {
		if (($_page_types = get_var('_page_types')) && is_array($_page_types)) return $_page_types;
		$_page_types = array();

		if (is_dir(ABSPATH . 'plugins') && ($handle = opendir(ABSPATH . 'plugins'))) {

			while (false !== ($_plugin = readdir($handle))) {
				if ((substr($_plugin, 0, 1) !== '.') && is_dir(PLUGINS . $_plugin)) {
					if (is_dir(PLUGINS . $_plugin . "/pages")) {
						$_page_types[] = $_plugin;
					}
				}
			}
			
			//sort($_page_types);
			closedir($handle);
		}
		
		set_var('_page_types', $_page_types);
		return $_page_types;
	} // get_page_type_plugins
	
	function init_plugins() {
		$_section = section();
		
		if (($_section['type'] != 'static') && is_plugin($_section['type']))
			load_plugin($_section['type']);
			
		if (!empty($_section['plugins']))
			load_plugins($_section['plugins']);			
	} // init_plugins
	
	function install_plugin($_plugin = false) {
		global $db;
		
		if (!is_string($_plugin) || empty($_plugin))
			return "No plugin supplied. Installed canceled.";
		
		if (plugin_is_installed($_plugin))
			return "Plugin is already installed.";
			
		if (file_exists(PLUGINS . $_plugin . '/_installed.php')) {
			$_plugins = get_installed_plugins();
			$_plugins[] = $_plugin;
			set_var('_installed_plugins', array_unique($_plugins));
			return "Plugin is already installed.";
		}
		
		$sql_file = PLUGINS . $_plugin . '/install.sql';
		
		if (is_file($sql_file)) {
			
			$sql = get_file($sql_file);
			
			if (!is_string($sql) || empty($sql)) {
				return "There was an error reading in the installation SQL for this plugin.";
			}
			
			$installed = $db->execute($sql, true);

			if (!$installed) {
				return "There was an error installing this plugin in the database.";
			}
						
			if (is_writable($sql_file)) {
				rename($sql_file, PLUGINS . $_plugin . "/_installed.sql");
			}
			
		}
		
		file_put_contents(PLUGINS . $_plugin . '/_installed.php', '/* Just here as an easy way to ensure plugin is installed. */');
		
		$_plugins = get_installed_plugins();
		$_plugins[] = $_plugin;
		set_var('_installed_plugins', array_unique($_plugins));
		
		return true;
	} // install_plugin
	
	function is_plugin($_plugin = false) {
		return in_array($_plugin, get_available_plugins());
	} // is_plugin
	
	function load_plugin($_plugin) {
		$_loaded_plugins = get_loaded_plugins();
					
		if (!plugin_is_loaded($_plugin)) {
			// load a single include file that handles loading all other files needed...
			//if (file_exists(PLUGINS . $_plugin . "/include.php"))
			//	include_once(PLUGINS . $_plugin . "/include.php");
			// load all the files in the plugin (that don't start with "_")...
			if (is_dir(PLUGINS . $_plugin) && ($handle = opendir(PLUGINS . $_plugin))) {

				while (false !== ($_file = readdir($handle))) {
					if ((substr($_file, -4) === '.php') && (substr($_file, 0, 1) !== '_')) {
						include_once(PLUGINS . $_plugin . "/" . $_file);
					}
				}

				closedir($handle);
			}
			
			$_loaded_plugins[] = $_plugin;
			set_var('_loaded_plugins', $_loaded_plugins);
			return true;
		}
		return false;
	} // load_plugin
	
	function load_plugins($_plugin = false) {
		if (!$_plugin)
			return false;
		if (is_string($_plugin))
			$_plugin = trim_explode(',', $_plugin);
		if (is_array($_plugin)) {
			foreach ($_plugin as $p)
				load_plugin($p);
		}
		return false;
	} // load_plugins
	
	function plugin_is_installed($_plugin = false) {
		return in_array($_plugin, get_installed_plugins());
	} // plugin_is_installed
	
	function plugin_is_loaded($_plugin = false) {
		return in_array($_plugin, get_loaded_plugins());
	} // plugin_is_loaded

	function plugin_is_page_type($_plugin = false) {
		return in_array($_plugin, get_page_type_plugins());
	} // plugin_is_page_type	

?>