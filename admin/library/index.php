<?php
		
	require_once LIBRARY . "functions.editable.php";
		
	function get_sections($levels = false, $current_level = -1, $sections = array()) {
		//if (!$levels) $levels = get_var('levels');
		if (!$levels) {
			$levels = get_sitemap_roots();
			foreach ($levels as $level) {
				$sections = get_sections($level, $current_level+1, $sections);
			}
		} else {
			$levels['display'] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $current_level) . $levels['name'];
			$sections[] = $levels;
			foreach ($levels['subsections'] as $level) {
				$sections = get_sections($level, $current_level+1, $sections);
			}
		}
		return $sections;
	} // get_sections
	
	function admin_editor() {
		
		set_page_var('editableAreas', false);
		
		$templates = array();
		if ($handle = opendir(ABSPATH . 'sites/' . get_site() . '/' . get_theme() . '/templates')) {
		    
			while (false !== ($file = readdir($handle))) {
				if (substr($file, 0, 1) !== '.')
					$templates[] = $file;
			}

			closedir($handle);
		}
		set_page_var('templates', $templates);
		
		$stylesheets = array();
		if ($handle = opendir(ABSPATH . 'sites/' . get_site() . '/' . get_theme() . '/stylesheets')) {
		    
			while (false !== ($file = readdir($handle))) {
				if (substr($file, 0, 1) !== '.')
					$stylesheets[] = $file;
			}

			closedir($handle);
		}
		set_page_var('stylesheets', $stylesheets);
		
		$template = request('template');
		$stylesheet = request('stylesheet');
		
		if ($template || $stylesheet) {
			
			$f = ((is_string($template)) ? 'templates/' . $template : 'stylesheets/' . $stylesheet);
			$filename = ABSPATH . "sites/" . get_site() . "/" . get_theme() . "/" . $f;
	
			if ($contents = file_get_contents($filename)) {
			/*if ($handle = fopen($filename, "r")) {
			
				$contents = stream_get_contents($handle);
				
				if (post('file')) {
					
					if (!is_writable($filename)) {
						failure("Could not write to the file (" . $f .") because file was not writable. Please manually change permissions to 777.");
					} else {
						$handle2 = fopen($filename, "w");
						$contents = stripslashes(post('file'));
						if (fwrite($handle2, $contents) !== false) {
							success("File ($f) was successfully saved.");
						} else {
							failure("There was an error saving the file ($f). Please manually update file.");
						}
					}
				}

				//$contents = fread($handle, filesize($filename));
				set_page_var('filename', $f);
				set_page_var('file', $contents);
				fclose($handle);*/
				set_page_var('filename', $f);
				set_page_var('file', $contents);
				
				// Find editable content of templates
				if (is_string($template)) {
					require_once LIBRARY . 'simple_html_dom.php';

					$a = str_replace(array("&mdash;", "<?", "?>", "></textarea>"), array("&amp;mdash;", "&lt;?", "?&gt;", ">SEENREPLACEME</textarea>"), $contents);
					$html = str_get_html($a);
					$query = "//[class*=editable]";
					$editableAreas = $html->find($query);
					
					if (is_array($editableAreas) && (count($editableAreas) > 0))
						set_page_var('editableAreas', $editableAreas);
				}
			
			} else {
				failure("There was an error reading file ($f).");
			}

		}
		
	} // admin_editor
	
	function admin_comments() {
		load_plugin('comments');
		
		require_once LIBRARY . "functions.pagination.php";
		
		if (get_var('id')) {
			// individual one
			$comment = new comment(get_var('id'));
			set_page_var('item', $comment);
		} else {
			// search all
			$items = search_comments();
			set_page_var('total_count', count($items));
			$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());
			set_page_var('items', $items);
		}
	} // admin_comments
	
	function admin_pages() {
		$params = get_var('params');
	} // admin_pages
	
	function admin_pages_list() {
		$params = get_var('params');
	} // admin_pages_list
	
	function admin_pages_content() {
		//require_once LIBRARY . 'functions.editable.php';
		require_once(LIBRARY . 'simple_html_dom.php');
		
		$section = (is_numeric(get_var('id'))) ? get_sitemap_section(get_var('id')) : get_sitemap_root();
		
		if (is_plugin($section['type'])) {
			set_var('plugin', $section['type']);
			load_plugin($section['type']);
			add_presentation(ABSPATH . 'plugins/' . $section['type'] . "/admin/" . $section['type']);
		}
		
	} // admin_pages_content
	
	function admin_users() {
		global $db;
		
		$params = trim_explode('/', get_var('params'));
		
		require_once LIBRARY . 'functions.pagination.php';
		
		if (count($params) > 0) {
			
			if (is_numeric(current($params))) {
				
				$user_id = array_shift($params);
				$user = $db->getOne('table=>users', 'id=>' . $db->escape($user_id));
				
			} else {
			//} elseif (current($params) == 'add') {

				$user = array('id' => false, 'name' => false, 'types' => 'authors', 'email' => false, 'url' => false);

			}

			set_page_var('item', $user);
			set_page_var('user', $user);
			set_var('page', 'users-edit');
			
		} else {
		
			$items = search_users();
			set_page_var('total_count', count($items));
			$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());
			set_page_var('items', $items);
			
		}		 
				
	} // admin_users
	
	function admin_settings() {
		$params = get_var('params');
		if (!$params || empty($params))
			$params = 'general';
		set_page_var('settings', $params);
	} // admin_settings
	
	function admin_support() {
		include_once CONFIG . 'support.php';
		
		$params = get_var('params');
		if (!$params || empty($params))
			$params = 'ticket';
		set_page_var('support', $params);
	} // admin_support
	
	function search_users() {
		global $db;
		
		$terms = $_GET;
		
		$criteria = array();
		
		//$criteria[] = "((`users`.`types` LIKE '%admins%') OR (`users`.`types` LIKE '%editors%') OR (`users`.`types` LIKE '%contributors%') OR (`users`.`types` LIKE '%staff%'))";
		$criteria[] = "(`users`.`name` != '')";
		
		if (isset($terms['keywords']) && !empty($terms['keywords'])) {
			//if ($terms['exact-match']) {
				$criteria[] = "(`users`.`name` LIKE '%" . $db->escape($terms['keywords']) . "%') OR (`users`.`email` LIKE '%" . $db->escape($terms['keywords']) . "%')";
			//} else {
				// this needs to be reworked for better, more forgiving search
			//	$criteria[] = "(`artists`.`name` LIKE '%" . $terms['keywords'] . "%') OR (`artists`.`biography` LIKE '%" . $terms['keywords'] . "%')";
			//}
		}
		
		if (isset($terms['status']) && !empty($terms['status']) && ($terms['status'] == 'deleted')) {
			$criteria[] = "`users`.`deleted_at` IS NOT NULL";
		} else {
			$criteria[] = "`users`.`deleted_at` IS NULL";
		}
		
		$sql = "SELECT `users`.*, CONCAT('users') AS `t` FROM `users` WHERE (" . join(") AND (", $criteria) . ")";
		
		if (isset($terms['sort']) && !empty($terms['sort'])) {
			$sql = $sql . " ORDER BY `users`." . $db->escape($terms['sort']);			
		} else {
			$sql = $sql . " ORDER BY `users`.`created_at` DESC";
		}
		
		if (isset($terms['limit']) && !empty($terms['limit'])) {
			$sql = $sql . " LIMIT " . $db->escape($terms['limit']);
		}
			
		//dump($sql);
		$content = $db->get($sql);
		return $content;
	} // search_users
	
	function get_page_thumb($section, $width = false, $height = false, $cropratio = false) {
		$url = trim(str_replace(array("/", "."), "-", $_SERVER['SERVER_NAME'] . get_sitemap_section_url($section['id'])), "-");
		if (file_exists(upload_folder('screenshots', true) . $url . '.jpg')) {
			return add_photo_info(upload_folder('screenshots') . $url . ".jpg", $width, $height, $cropratio);			
		}
		
		return add_photo_info(upload_folder('screenshots') . "0.gif", $width, $height, $cropratio);
	} // get_page_thumb
	
	function get_support_tickets() {
		global $db;
		
		return $db->get('table=>tickets', "where=>(`deleted_at` IS NULL)", 'order=>created_at DESC');
	} // get_support_tickets

?>