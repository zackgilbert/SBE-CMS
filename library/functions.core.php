<?php

	function load_config($filename) {
		require_once(CONFIG . $filename);
	} // load_config
	
	function load_include($_page, $variables = array()) {
		return load_template_file('includes/' . $_page, $variables);
	} // load_include
	
	function load_library($filename) {
		require_once(LIBRARY . $filename);
	} // load_library
	
	function load_message($_page, $variables = array()) {
		$buffer = ob_get_contents();							// get current output
		load_template_file('messages/' . $_page);
		$output = substr(ob_get_contents(),strlen($buffer));	// store difference between orig and new
		ob_end_clean();											// clear output buffer
		ob_start("ob_gzhandler");								// restart output buffer
		echo $buffer;											// print out orig output
		
		foreach ($variables as $varName => $varValue) {
			$output = str_replace("{" . $varName . "}", $varValue, $output);
		}
		
		return $output;											// return difference var
	} // load_message
	
	function load_page($_page = false, $_template = false) {
		global $db, $usr;
		foreach (get_page_vars() as $varName => $varValue)
			if (!isset($$varName)) $$varName = $varValue;
		$hasTemplate = (is_string($_template) && !empty($_template));
		$pageLocation = false;

		if (!is_string($_page) || empty($_page))
			$_page = get_page();
		
		if (get_var('page_code')) {
			echo get_var('page_code');
			return;
		}
		
		if (!empty($_page) && (substr($_page, -4) != '.php'))
			$_page .= '.php';
		
		if (get_var('plugin') && get_var('plugin-page')) {
			$pageLocation = is_plugin_file(get_var('plugin'), get_var('plugin-page'));
		} else {
			if ($p = is_template_file('pages/' . $_page)) {
				$pageLocation = $p;
			} else if ($s = is_template_file('scripts/' . $_page)) {
				$pageLocation = $s;
				$hasTemplate = false;
			}
		}
		
		if ($pageLocation || $hasTemplate) {
			if ($hasTemplate && is_string($_template) && !empty($_template)) {
				return load_template($_template, $_page);
			} else {
				include($pageLocation);
			}
		} else {
			if (!PRODUCTION && (DEBUG || ($_page == (strip_ext(NOT_FOUND_PAGE) . '.php')))) {
				echo "<p>Could not find page: (" . get_path() . '/pages/' . $_page . ")</p>";
			} else {
				load_server_page(NOT_FOUND_PAGE);
			}
		}
				
	} // load_page
		
	function load_script($_page, $variables = array()) {
		return load_template_file('scripts/' . $_page, $variables);
	} // load_script
	
	function load_server_page($_page, $plugin = false) {
		include SITES . "_SERVER_/" . strip_ext($_page) . ".php";
	} // load_server_page

	function load_template($_template = false, $_page = false) {
		global $db, $usr;
		foreach (get_page_vars() as $varName => $varValue)
			if (!isset($$varName)) $$varName = $varValue;
		
		if ($_page)
			set_var('page', $_page);
		
		if (!is_string($_template) || empty($_template))
			$_template = get_template();
	
		if (substr($_template, -4) != '.php')
			$_template .= '.php';
		
		if (is_string($_template) && is_template_file('templates/' . $_template)) {
			include(is_template_file('templates/' . $_template));
		} else {
			if ((!PRODUCTION && DEBUG) || ($_template == 'basic')) {
				echo "<p>Could not find template: (" . get_path() . '/templates/' . $_template . ")</p>";
			} else {
				// SHOULD THIS FORCE A 404?
				load_page(NOT_FOUND_PAGE, 'basic');
			}
		}
	} // load_template
	
	function load_template_file($_file, $variables = array()) {
		global $db, $usr;
		foreach (get_page_vars() as $varName => $varValue)
			if (!isset($$varName)) $$varName = $varValue;
		if (is_array($variables))
			foreach ($variables as $key => $value)
				$$key = $value;
		
		if (!get_ext($_file))
			$_file .= '.php';
		
		if ($_filepath = is_template_file($_file)) {
			include($_filepath);
		} else if (get_var('plugin') && ($_filepath = is_plugin_file(get_var('plugin'), $_file))) {
			include($_filepath);
		} else {
			//if (DEBUG || ($page == $pagenotfound)) {
				echo "<p>Could not find file: " . get_path() . '/' . $_file . "</p>";
			//} else {
				// SHOULD THIS FORCE A 404?
			//	load_include($pagenotfound);
			//}
		}
	} // load_template_file
				
	function page_uses_database() {
		if ((USE_DB || is_admin()) && (get_vars('database') || get_vars('requires_database'))) {
			return true;
		}
		return false;
	} // page_uses_database
	
	function page_uses_users() {
		if (USE_USERS || requires_authentication() || is_admin()) {
			return true;
		}
		return false;
	} // page_uses_users
	
	function requires_authentication() {
		if ((get_vars('restricted') || get_vars('authentication') || get_vars('requires_authentication'))) {
			return true;
		}
		return false;
	} // requires_authentication
	
	function get_site() {
		if (get_var('site'))
			return get_var('site');
		
		$qs = (isset($_GET['flinturl'])) ? $_GET['flinturl'] : ((isset($_GET['file'])) ? $_GET['file'] : '');
		$url = explode('/', $qs);
		
		if (is_admin()) {
			array_shift($url);
			if (isset($_REQUEST['site'])) {
				$site = $_REQUEST['site'];
				set_session_var('site', $site);
				set_var('site', $site);
				return $site;				
			} else if (get_session_var('site')) {
				$site = get_session_var('site');
				set_var('site', $site);
				return $site;
			}
		}
			
		$sites = get_sites();
		//dump($_SERVER['SERVER_NAME']);
		foreach ($sites as $siteName => $siteInfo) {
			if (in_array($_SERVER['SERVER_NAME'], $siteInfo['domains'])) {
				set_var('site', $siteName);
				return $siteName;
			}
			$server = explode(".", $_SERVER['SERVER_NAME']);
			if (count($server) > 2) {
				if (in_array($server[0], $siteInfo['subdomains'])) {
					set_var('site', $siteName);
					return $siteName;					
				}/* else if (in_array($siteName, $siteInfo['subdomains'])) {
					set_var('site', $siteName);
					return $siteName;
				}*/
			}
		}
		
		if (isset($url[0]) && !empty($url[0]) && is_dir(dirname(dirname(__FILE__)) . "/sites/" . $url[0])) {
			set_var('site', $url[0]);
			return $url[0];
		}
		
		// check for matching subdomains
		// check for matching domains
		
		set_var('site', DEFAULT_SITE);
		return DEFAULT_SITE;
	} // get_site
	
	function site_id() { return get_site_id(); } // site_id
	function get_site_id() {
		if (!get_var('site_id')) {
			$site = get_sites(get_site());
			set_var('site_id', $site['id']);
		}
		return get_var('site_id');
	} // get_site_id
	
	function get_site_themes() {
		$themes = array();
		
		if ($handle = opendir(ABSPATH . 'sites/' . get_site())) {
			while (false !== ($file = readdir($handle))) {
				if (substr($file, 0, 1) !== '.') {
					$themes[] = $file;
				}
			}
			closedir($handle);
		}
		return $themes;
		//return array(get_theme());
	} // get_site_themes
	
	function get_location($includeSite = true) {
		$location = "/";
		if (substr($_SERVER['SCRIPT_NAME'], -1) == '/') {
			$location = $_SERVER['SCRIPT_NAME'];
		} else if (dirname($_SERVER['SCRIPT_NAME']) == '/') {
			$location = dirname($_SERVER['SCRIPT_NAME']); 
		} else {
			$location = dirname($_SERVER['SCRIPT_NAME']) . '/'; 
		}
		
		// if this is the normal site or admin, then no worries...
		// **** TAKE OUT ADMIN LATER ON... it'll just break things for now. ****
		if (!$includeSite || in_array(get_site(), array(DEFAULT_SITE, 'admin'))) {
			return $location;
		}
		
		/*$qs = (isset($_GET['flinturl'])) ? $_GET['flinturl'] : ((isset($_GET['file'])) ? $_GET['file'] : '');
		$url = explode('/', $qs);
		
		if (isset($url[0]) && !empty($url[0]) && is_dir(dirname(dirname(__FILE__)) . "/sites/" . $url[0])) {
			$location = $location . $url[0] . '/';
		}*/
		
		$sites = get_sites();
		$server = explode(".", $_SERVER['SERVER_NAME']);
		array_pop($server);
		if (is_array($server) && (count($server) > 1))
			array_pop($server);
		
		foreach ($sites as $siteName => $siteInfo) {
			if (in_array($_SERVER['SERVER_NAME'], $siteInfo['domains'])) {
				return $location;
			}
			if (is_array($server) && (count($server) > 0)) {
				if (in_array(join(".", $server), $siteInfo['subdomains'])) {
					return $location;
				}
			}
		}
		if (is_dir(dirname(dirname(__FILE__))) . "/sites/" . get_site()) {
			$location = $location . get_site() . "/";
		}
		
		return $location;
	} // get_location
	
	function get_folder() {
		if (get_var('folder'))
			return get_var('folder');
		
		if (is_admin()) {
			set_var('folder', 'admin');
			return 'admin';
		}
		
		$site = get_site();
		if ($site && is_dir(dirname(dirname(__FILE__)) . "/sites/" . $site)) {
			set_var('folder', $site);
			return $site;
		}

		if (isset($url[0]) && !empty($url[0]) && is_dir(dirname(dirname(__FILE__)) . "/sites/" . $url[0])) {
			set_var('folder', $url[0]);
			return $url[0];
		}
		
		set_var('folder', DEFAULT_SITE);
		return DEFAULT_SITE;
	} // get_folder
	
	function get_path() {
		//if (get_var('folder_path'))
		//	return get_var('folder_path');
	 	$folder_path = (get_folder() == 'admin') ? ABSPATH . get_folder() : ABSPATH . "sites/" . get_folder() . "/" . get_theme();
		//set_var('folder_path', $folder_path);
		return $folder_path;
	} // get_path
	
	function get_page() {
		$page = get_var('page');
		if (!$page) $page = get_var('plugin-page');
		if (!$page) $page = get_var('include');
		if (!$page) $page = get_var('script');
		if (!$page) $page = (get_var('default-page')) ? get_var('default-page') : 'index';
		return $page;
	} // get_page
	
	function get_template() {
		$template = get_var('template');
		if (is_string($template) && !empty($template)) {
			if (substr($template, -4) != '.php')
				$template .= '.php';
			if (is_file(get_path() . '/templates/' . $template)) {
				return $template;
			}
		}
		return false;
	} // get_template
	
	function get_theme() {
		if (get_var('theme'))
			return get_var('theme');

		$site = get_sites(get_site());
		if (isset($site['theme']) && is_string($site['theme']) && is_dir(dirname(dirname(__FILE__)) . "/sites/" . get_site() . "/" . $site['theme'])) {
			set_var('theme', $site['theme']);
			return $site['theme'];
		}
		if (defined('THEME') && is_dir(dirname(dirname(__FILE__)) . "/sites/" . get_folder() . "/" . THEME)) {
			set_var('theme', THEME);
			return THEME;
		}

		set_var('theme', DEFAULT_THEME);
		return DEFAULT_THEME;
	} // get_theme
	
	function get_site_templates() {
		$templateFolder = ABSPATH . "sites/" . get_site() . "/" . get_theme() . '/templates';
		$templates = array();
		
		if ($handle = opendir($templateFolder)) {
			while (false !== ($file = readdir($handle))) {
				if (substr($file, 0, 1) != '.')
					$templates[] = $file;
			}
			closedir($handle);
		}
		
		return $templates;
	} // get_site_templates
	
	function get_page_file_location($section) {
		if (get_var('page-file-location-' . $section['id']))
			return get_var('page-file-location-' . $section['id']);
		
		$folder = ABSPATH . 'sites/' . get_site() . '/' . get_theme() . '/pages/';
		$file = $folder . get_prefix($section);
		if (substr($file, -4) != '.php')
			$file = $file . '.php';
		
		$original_file = $file;
		while (!file_exists($file) && $section['parent_id'] > 0) {
			$parent = get_sitemap_section($section['parent_id']);
			if (!empty($parent['prefix']))
				$parent_prefix = $parent['prefix'];
			elseif (!empty($parent['url']))
				$parent_prefix = $parent['url'];
			else
				$parent_prefix = '';
			$filename = str_replace($folder, "", $file);
			$file = $folder . $parent_prefix . '-' . $filename;
			$section = $parent;
		}
		
		if (!file_exists($file))
			$file = $original_file;
		
		set_var('page-file-location-' . $section['id'], $file);	
		return $file;
	} // get_page_file_location
	
	function get_table() {
		$table = get_var('table');
		if (!$table)
			$table = get_var('section');
		if (!$table)
			$table = str_replace("-","_",get_page());
		return $table;
	} // get_table
	
	function get_defaults() {
		$defaults = get_var('defaults');
		if (is_string($defaults) && !empty($defaults)) {
			return $defaults;
		}
		return 'default';
	} // get_defaults
	
	function get_prefix($levelToCheck = false) {
		$prefix = get_var('prefix');
		if (!get_var('prefix') || $levelToCheck) {
			$level = ($levelToCheck) ? $levelToCheck : get_var('level');
		
			if (!empty($level['prefix'])) {
				$prefix = (substr($level['prefix'], -4) == '.php') ? substr($level['prefix'], 0, -4) : $level['prefix'];
			} else if (empty($level['url'])) {
				$prefix = 'index';
			} else {
				$prefix = $level['url'];
			}
			if (!$levelToCheck)
				set_var('prefix', $prefix);
		}
		return $prefix;
	} // get_prefix
	
	function remember_return_page() {
		if (should_remember_return_page()) : 
			set_session_var('referral_page', $_SERVER['REQUEST_URI']);
		endif;
	} // remember_return_page
	
	function should_remember_return_page() {
		if (strpos($_SERVER['REQUEST_URI'], '/login') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/logout') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/signup') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/register') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/scripts/') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/includes/') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/email/') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/rss/') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/save/') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/delete/') !== false)
			return false;
		if (strpos($_SERVER['REQUEST_URI'], '/rss/') !== false)
			return false;
		if (get_ext($_SERVER['REQUEST_URI']))
			return false;
		return true;
	} // should_remember_return_page
	
	function is_admin() {
		$qs = (isset($_GET['flinturl'])) ? $_GET['flinturl'] : ((isset($_GET['file'])) ? $_GET['file'] : '');
		$url = explode('/', $qs);
		
		if ($url[0] == 'admin') {
			return true;
		}
		return false;
	} // is_admin
	
	function is_section($sectionToCheck = false) {
		$section = get_var('section');
		if (is_array($section) && is_string($sectionToCheck))
			return ($section['url'] == $sectionToCheck);
		return ($section == $sectionToCheck);
	} // is_section
	
	function is_restricted() {
		return get_var('restricted');
	} // is_restricted
	
	function get_restricted_to() {
		$type = false;
		$restricted = get_var('restricted');
		if (is_array($restricted)) {
			if (isset($restricted['to'])) {
				$type = $restricted['to'];
			}
		} else if (is_string($restricted)) {
			$type = $restricted;
		}
		return $type;
	} // get_restricted_to
	
	function get_restricted_login() {
		$restricted = get_var('restricted');
		$login = (isset($restricted['login'])) ? $restricted['login'] : get_folder() . "/login/";
		return $login;
	} // get_restricted_login
	
	function get_metadata($return = false, $site = false) {
		global $db, $_METADATA;
		
		if (!is_string($site))
			$site = get_site();
		
		if (!isset($_METADATA) || !is_array($_METADATA)) {
			// check for config metadata file...
			if (is_file(CONFIG . "metadata.php")) {
				// if it's there, load it and grab $metadata and use that...
				require_once(CONFIG . "metadata.php");
			} else {
			//} else if (page_uses_database()) {
				// otherwise, check for the info in the database			
				$meta = $db->get('table=>metadata', 'where=>(`deleted_at` IS NULL)');
				if (is_array($meta)) {
					$fileToWrite = "";
					foreach ($meta as $m) {
						$_METADATA[get_site_by_id($m['site_id'], 'name')][$m['name']] = $m['value'];
						$fileToWrite .= "\t" . '$_METADATA["' . get_site_by_id($m['site_id'], 'name') . '"]["' . $m['name'] . '"] = "' . $m['value'] . '";' . "\n";
					}
					save_setting_file(CONFIG . "metadata.php", $fileToWrite);				
				}
			//} else {
			//	$_METADATA = array('title' => false, 'description' => false, 'keywords' => false);
			}
		}
		
		if (isset($_METADATA[$site]) && ($site !== '__ALL__')) {
			if (is_string($return)) {
				if (isset($_METADATA[$site][$return])) {
					return $_METADATA[$site][$return];
				}
				return false;
			} else {
				return $_METADATA[$site];
			}
		} else {
			if (is_string($return)) {
				return false;
			} else {
				return $_METADATA;
			}
		}
	} // get_metadata

	function meta($name, $value = false, $site = false) {
		global $_METADATA;
		
		if (!is_string($site))
			$site = get_site();
				
		if (!isset($_METADATA) || !is_array($_METADATA))
			$_METADATA = get_metadata(false, '__ALL__');
		
		if (is_string($value)) {
			$_METADATA[$site][$name] = $_METADATA[$site][$name] . ', ' . htmlentities2(strip_tags($value));
		} else if (is_array($value)) {
			$_METADATA[$site][$name] = $_METADATA[$site][$name] . ', ' . strip_tags(join(', ', $value));
		}
		
		$value = (isset($_METADATA[$site][$name])) ? str_replace('"', '&quot;', $_METADATA[$site][$name]) : '';
		
		$level = get_var('level');
		if (isset($level[$name]) && !empty($level[$name])) {
			/*if ($name == 'keywords') {
				$value = trim(htmlentities2(strip_tags($level[$name]))) . ", " . $value;
			} else {
				$value = trim(htmlentities2(strip_tags($level[$name]))) . " " . $value;				
			}*/
			$value = trim(htmlentities2(strip_tags($level[$name])));
		}
		
		if (isset($_METADATA[$site][$name])) {
			return '<meta name="' . $name . '" content="' . $value . '" />' . "\n";
		}
		return '';
	} // meta
	
	function title($new_title = false, $prepend = true, $clear = false) {		
		$title = get_var('title');

		if (!is_array($title)) {
			$title = array(get_metadata('title'));
			set_var('title', $title);
		}

		if (is_string($new_title) && !empty($new_title)) {
			// if there is a new piece to add to the title...
			if ($clear) {
				// start over with just this title
				$title = array($new_title);
			} else {
				// build on to the previous title if there is one
				if ($prepend) {
					array_push($title, $new_title);
				} else {
					array_unshift($title, $new_title);
				}
			}
			set_var('title', $title);
		}

		return valid(join(' - ', array_reverse($title)));
	} // title

	function get_search_items() {
		return get_page_var('items');
	} // get_search_items
	
	function count_search_items() {
		return get_page_var('total_count');
	} // count_search_items
	
	function query_string($replace = false) {
		if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
			$qs = explode("&", trim(substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],"?")), '?'));
		} else {
			$qs = array();
		}	
		
		if (is_array($replace)) {
			foreach ($replace as $key => $value) {
				$found = false;
				for ($i=0; $i<count($qs); $i++) {
					if (substr($qs[$i], 0, strlen($key)) == $key) {
						$found = true;
						$qs[$i] = $key . '=' . $value;
						break;
					}
				}
				if (!$found)
					$qs[] = $key . '=' . $value;
			}
		}
		
		return $qs;
	} // query_string
	
	function sendmail($to, $from, $subject, $message) {
		require_once LIBRARY . "class.sendmail.php";
		$mail = new sendmail('Webmaster', 'noreply@' . SERVER);
		$mail->set_header('From', $from);
		$mail->bodytext($message);
		return $mail->send($to, $subject);
	} // sendmail
	
	function get_path_from_url($url) {
		$path = $url;
		
		if (substr($url, 0, strlen(LOCATION)) == LOCATION)
			$path = ABSPATH . substr($path, strlen(LOCATION));
			
		return $path;
	} // get_path_from_url
	
	function get_url_from_path($path) {
		// on default, $path is equal to $url
		$url = $path;
		
		// replace ABSPATH with LOCATION
		if (substr($path, 0, strlen(ABSPATH)) == ABSPATH)
			$url = str_replace_once(ABSPATH, get_location(false), $url);
				
		return $url;
	} // get_url_from_path
	
	function is_ajax() {
		return (isset($_GET['ajax']) || isset($_POST['ajax']));
	} // is_ajax
	
	function is_plugin_file($plugin, $filename) {
		if (is_file(PLUGINS . $plugin . "/pages/" . $filename)) {
			return PLUGINS . $plugin . "/pages/" . $filename;
		} else if (is_file(PLUGINS . $plugin . "/pages/" . $filename . ".php")) {
			return PLUGINS . $plugin . "/pages/" . $filename . ".php";
		} else if (is_file(PLUGINS . $plugin . "/" . $filename)) {
			return PLUGINS . $plugin . "/" . $filename;
		} else if (is_file(PLUGINS . $plugin . "/" . $filename . ".php")) {
			return PLUGINS . $plugin . "/" . $filename . ".php";
		}
		return false;
	} // is_plugin_file
	
	function is_template_file($fileName) {
		if (is_file(get_path() . '/' . $fileName)) {
			return get_path() . '/' . $fileName;
		} else if (is_file(get_path() . '/' . $fileName . '.php')) {
			return get_path() . '/' . $fileName . '.php';
		} else if (is_file(ABSPATH . 'uploads/' . $fileName)) {
			return ABSPATH . 'uploads/' . $fileName;
		} else if (is_file(ABSPATH . 'uploads/' . $fileName . '.php')) {
			return ABSPATH . 'uploads/' . $fileName . '.php';
		} else if (is_file(ABSPATH . $fileName)) {
			return ABSPATH . $fileName;
		}
		return false;
	} // is_template_file
	
	function search_template_file($substr_filename) {
		//$folders = array(ABSPATH . get_folder() . '/themes/' . get_theme() . '/', ABSPATH . get_folder() . '/themes/default/', ABSPATH . DEFAULT_FOLDER . '/themes/default/', ABSPATH . 'uploads/', ABSPATH);
		$folders = array(get_path() . '/', ABSPATH . 'uploads/', ABSPATH);
		foreach ($folders as $folder) {
			$full_folder = $folder . substr($substr_filename, 0, strrpos($substr_filename, '/'));
			$name = substr($substr_filename, strrpos($substr_filename, "/")+1);
			
			if (is_dir($full_folder)) {
				if ($handle = opendir($full_folder)) {
					while (false !== ($file = readdir($handle))) {
						if (substr($file, 0, strlen($name)) == $name) {
							closedir($handle);
							return $full_folder . "/" . $file;
						}
					}
					closedir($handle);
				} else {
					warning('Could not open directory (' . $folder . ') to search for template files (' . $substr_filename . ').', __FILE__, __FUNCTION__, __LINE__);
				}
			}
		}
		return false;
	} // search_template_file
	
	function get_api_key($return = false, $site = false) { return get_apikey($return, $site); } // get_api_key
	function get_apikey($return = false, $site = false) {
		global $db, $_APIKEYS;
		
		if (!is_string($site))
			$site = get_site();
		
		if (!isset($_APIKEYS) || !is_array($_APIKEYS)) {
			// check for config apikeys file...
			if (is_file(CONFIG . "apikeys.php")) {
				// if it's there, load it and grab $api_keys and use that...
				require_once CONFIG . "apikeys.php";
			} else if (page_uses_database()) {
				// otherwise, check for the info in the database			
				$apis = $db->get('table=>apikeys', 'where=>(`deleted_at` IS NULL)');
				if (is_array($apis)) {
					$_APIKEYS = array();
					$fileToWrite = "";
					foreach ($apis as $api) {
						$_APIKEYS[$api['name']] = $api['value'];
						if ($api['site_id'] > 0) {
							$fileToWrite .= "\t" . '$_APIKEYS["' . get_site_by_id($api['site_id'], 'name') . '"]["' . $api['name'] . '"] = "' . $api['value'] . '";' . "\n";
						} else {
							$fileToWrite .= "\t" . '$_APIKEYS["' . $api['name'] . '"] = "' . $api['value'] . '";' . "\n";							
						}
					}
					save_setting_file(CONFIG . "apikeys.php", $fileToWrite);
				}
			} else {
				$_APIKEYS[$site] = array();
			}
		}
		
		if (is_string($return)) {
			// if return is set
			if ($return == "__ALL__") {
				return $_APIKEYS;
			} else if ($return == "__SITE__") {
				return $_APIKEYS[$site];
			} else if (isset($_APIKEYS[$site][$return])) {
				return $_APIKEYS[$site][$return];
			} else if (isset($_APIKEYS[$return])) {
				return $_APIKEYS[$return];
			}
			return false;
		} elseif (isset($_APIKEYS[$site])) {
			return $_APIKEYS[$site];
		}
		return $_APIKEYS;
	} // get_apikey
	
	function property_url($item) {
		if (is_array($item)) {
			return (isset($item['url'])) ? strtolower($item['url']) : false;
		}
		return strtolower(url_friendly($item));
	} // property_url
	
	function property_name($item) {
		if (is_array($item)) {
			return (isset($item['name'])) ? $item['name'] : false;
		}
		return $item;		
	} // property_name

	function objectize($type, $items = array()) {
		foreach ($items as $key => $item) {
				$items[$key] = (is_object($item)) ? $item : call_user_func('get_' . singularize($type), $item);
		}
		return $items;
	} // objectize
	
	function get_site_info($return = false, $find = false, $by = 'name') {
		if (!is_string($find)) {
			$find = get_site();
			$by = 'name';
		}
		$sites = get_sites();

		$site = false;
		foreach ($sites as $s) {
			if ((string)$s[$by] === (string)$find) {
				$site = $s;
				break;
			}
		}
		
		if (is_string($return)) {			
			if (isset($site[$return])) {
				return $site[$return];
			}
			return false;
		}
		
		return $site;
	} // get_site_info
	
	function get_site_by_id($id = 1, $return = false) {
		return get_site_info($return, $id, 'id');
	} // get_site_by_id
	
	function get_sites($name = false) {
		global $db, $_SITES;
		
		if (!isset($_SITES) || !is_array($_SITES)) {
			// check for config sites file...
			if (is_file(CONFIG . "sites.php")) {
				// if it's there, load it and grab $_SITES and use that...
				require_once CONFIG . "sites.php";
			//} else if (page_uses_database()) {
			} else {
				// otherwise, check for the info in the database			
				$sites = $db->get('table=>sites', 'where=>(`deleted_at` IS NULL)');
				if (is_array($sites)) {
					$fileToWrite = "";
					foreach ($sites as $site) {
						$site['subdomains'] = trim_explode(',', $site['subdomains']);
						$site['domains'] = trim_explode(',', $site['domains']);
						$_SITES[$site['name']] = $site;
						$fileToWrite .= "\t" . '$_SITES["' . $site['name'] . '"] = array("id" => ' . $site['id'] . ', "name" => "' . $site['name'] . '", "theme" =>"' . $site['theme'] . '", "subdomains" => array(' . ((count($site['subdomains']) > 0) ? "'" . join("', '", $site['subdomains']) . "'" : '') . '), "domains" => array(' . ((count($site['domains']) > 0) ? "'" . join("', '", $site['domains']) . "'" : '') . '));' . "\n";
					}
					save_setting_file(CONFIG . "sites.php", $fileToWrite);
				}
			//} else {
			//	$_SITES = array();
			}
		}
		
		if (is_string($name)) {
			if (isset($_SITES[$name])) {
				return $_SITES[$name];
			}
			return false;
		}
		
		return $_SITES;
	} // get_sites
	
	function save_setting_file($filename, $contents = '') {
		$fileToWrite = "<?php\n\n// THIS FILE IS AUTO-CREATED BASED ON DATABASE VALUES. CHANGING THIS FILE ONLY COULD RESULT IN LOST INFORMATION.\n" . $contents . "\n?>";
		file_put_contents($filename, $fileToWrite);
	} // save_setting_file
	
	function is_current_site($site) {
		return ($site == get_site());
	} // is_current_site
	
	function referral_page() {
		if (isset($_SESSION['referral_page']))
			return $_SESSION['referral_page'];
		if (isset($_SERVER['HTTP_REFERER']))
			return $_SERVER['HTTP_REFERER'];
		return false;
		//return (isset($_SESSION['referral_page'])) ? $_SESSION['referral_page'] : $_SERVER['HTTP_REFERER'];
	} // referral_page
	
	function redirect_success($default = false) {
		
		if (!$default)
			$default = (LOCATION . 'admin/');
		
		if (isset($_POST['redirect']) && is_array($_POST['redirect']) && isset($_POST['redirect']['success']) && is_string($_POST['redirect']['success'])) {
			$redirect_success = $_POST['redirect']['success'];
		} else if (isset($_POST['redirect']) && is_string($_POST['redirect'])) {
			$redirect_success = $_POST['redirect'];
		} else if (isset($_SERVER['HTTP_REFERER'])) {
			$redirect_success = $_SERVER['HTTP_REFERER'];
		} else if (referral_page()) {
			$redirect_success = referral_page();
		} else {
			$redirect_success = $default;
		}
		
		redirect($redirect_success);
		
	} // redirect_success
	
	function redirect_failure($default = false) {

		if (!$default)
			$default = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : LOCATION . 'admin/';

		if (isset($_POST['redirect']) && is_array($_POST['redirect']) && isset($_POST['redirect']['failure']) && is_string($_POST['redirect']['failure'])) {
			$redirect_failure = $_POST['redirect']['failure'];
		} else if (isset($_POST['redirect']) && is_string($_POST['redirect'])) {
			$redirect_failure = $_POST['redirect'];
		} else if (referral_page()) {
			$redirect_failure = referral_page();
		} else {
			$redirect_failure = $default;
		}
		redirect($redirect_failure);

	} // redirect_failure

?>