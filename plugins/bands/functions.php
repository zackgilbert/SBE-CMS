<?php

	function get_all_bands($sitemap_id = false) {
		global $db;
		
		if (is_array($sitemap_id)) $sitemap_id = $sitemap_id['id'];
		if (!is_numeric($sitemap_id)) $sitemap_id = get_var('level_id');
		
		$bands = $db->get(sprintf("SELECT * FROM `bands` WHERE (`sitemap_id` = %d) AND (`deleted_at` IS NULL) ORDER BY `created_at` ASC", $db->escape($sitemap_id)));
		if (is_array($bands) && (count($bands) > 0)) {
			$bands = objectize('bands', $bands);
		}
		return $bands;
	} // get_all_bands

	function get_band($band) {
		return new band($band);
	} // get_band

	function get_bands($sitemap_id = false) {
		global $db;
		
		if (is_array($sitemap_id)) $sitemap_id = $sitemap_id['id'];
		if (!is_numeric($sitemap_id)) $sitemap_id = get_var('level_id');

		//$bands = $db->get(sprintf("SELECT * FROM `bands` WHERE (`sitemap_id` = %d) AND (`published_at` IS NOT NULL) AND (`deleted_at` IS NULL) ORDER BY `published_at` ASC", $db->escape($sitemap_id)));
		$bands = $db->get(sprintf("SELECT * FROM `bands` WHERE (`published_at` IS NOT NULL) AND (`deleted_at` IS NULL) ORDER BY `published_at` ASC"));
		
		if (is_array($bands) && (count($bands) > 0)) {
			$bands = objectize('bands', $bands);
		}
		return $bands;
	} // get_bands
	
	function search_bands($terms = array()) {
		global $db;
		
		$criteria = array();

		if (isset($terms['keywords']) && !empty($terms['keywords'])) {
			$criteria[] = "(`bands`.`name` LIKE '%" . $db->escape($terms['keywords']) . "%') OR (`bands`.`biography` LIKE '%" . $db->escape($terms['keywords']) . "%') OR (`bands`.`tagline` LIKE '%" . $db->escape($terms['keywords']) . "%')";
		}
		
		if (isset($terms['date']) && !empty($terms['date']) && !empty($terms['date'])) {
			$criteria[] = "((`bands`.`created_at` LIKE '" . $db->escape($terms['date']) . "%') OR (`bands`.`published_at` LIKE '" . $db->escape($terms['date']) . "%'))";
		}

		
		if (is_admin()) {
			if (isset($terms['status']) && !empty($terms['status'])) {
				if ($terms['status'] == 'deleted') {
					$criteria[] = "`bands`.`deleted_at` IS NOT NULL";
				} else if ($terms['status'] == 'draft') {
					$criteria[] = "`bands`.`published_at` IS NULL";
					$criteria[] = "`bands`.`deleted_at` IS NULL";
				} else {
					$criteria[] = "`bands`.`published_at` IS NOT NULL";			
					$criteria[] = "`bands`.`deleted_at` IS NULL";
				}
			} else {
				$criteria[] = "`bands`.`deleted_at` IS NULL";
			}
		} else {
			$criteria[] = "`bands`.`published_at` IS NOT NULL";			
			$criteria[] = "`bands`.`deleted_at` IS NULL";
		}
		
		if (!isset($terms['section']) || empty($terms['section']) || !is_numeric($terms['section'])) {
			$root = get_sitemap_root();
			$terms['section'] = $root['id'];
		}
		$subsections = array($terms['section']);
			
		$sections = get_sitemap_subsections($terms['section']);
		while (count($sections) > 0) {
			$tempsections = array();
			foreach ($sections as $section) {
				$subsections[] = $section['id'];
				$tempsections = array_merge($tempsections,$section['subsections']);
			}
			$sections = $tempsections;
		}

		$criteria[] = "(`bands`.`sitemap_id` = " . join(") OR (`bands`.`sitemap_id` = ", $subsections) . ")";
		
		$sql = "SELECT `bands`.*, CONCAT('bands') AS `t` FROM `bands` WHERE (" . join(") AND (", $criteria) . ")";

		//$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page']-1 : 0;
		//$limit = 20;
		//$sql = $sql . " ORDER BY `blogs`.`created_at` DESC LIMIT " . ($page*$limit) . ", " . $limit;
		
		if (isset($terms['sort']) && !empty($terms['sort'])) {
			$sql = $sql . " ORDER BY `bands`." . $db->escape($terms['sort']);
		} else {
			$sql = $sql . " ORDER BY `bands`.`published_at` DESC";
		}
		
		if (isset($terms['limit'])) {
			$sql = $sql . " LIMIT " . $db->escape($terms['limit']);
		}
				
		//dump($sql);
		if (!($content = get_memcache($sql))) {
			$content = $db->get($sql);	
			set_memcache($sql, $content);
		}
		
		return $content;
	} // search_bands
	
	function page_type_bands() {
		// BREAKDOWN OF POSSIBLE URLs:
		// / 					: band index page from sitemap 
		// /*/					: band fullview

		// ok. load bands...
		// Things that need to be done:
		// 1. figure out if custom or default (in plugin) templates are to be used...
		// 2. which type of page is to be loaded.
		// 3. any database content...
		set_var('plugin', 'bands');
		
		$params = get_var('params');
		$prefix = get_prefix();
		
		$page = false;

		//title(section('name'));
				
		// figure out what type of blog page this is
		if (count($params) < 1) {
			$page = 'index';
		} else {
			$page = 'fullview';
			$band_url = $params[0];
			$band = get_band($band_url);
			set_var('item', $band);
			set_page_var('item', $band);
			set_page_var('band', $band);
			set_page_var($prefix, $band);

			// make sure we were able to find band
			if ($band->wasFound()) {
				title($band->title()); // add to the page's title
				add_javascript('audioplayer');
				add_javascript('flashembed');
				add_presentation('jquery.fancybox-1.2.1');
			} else {
				title('Could Not Find');
			 	$page = '404';
			}
		}
		
		// figure out what templates to use...
		// - custom templates based on $prefix
		// - custom templates based on plugin name
		// - default templates from plugin
		if (is_template_file($prefix . '-' . $page)) {
			add_presentation($prefix);
			set_var('page', $prefix . '-' . $page);
		} else if (is_template_file('bands-' . $page)) {
			add_presentation('bands');
			set_var('page', 'bands-' . $page);
		} else {
			add_presentation('bands');
			set_var('plugin-page', 'bands-' . $page);
		}
	} // page_type_bands

?>