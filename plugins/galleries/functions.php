<?php
	
	function page_type_galleries() {
		// BREAKDOWN OF POSSIBLE URLs:
		// / 					: gallery index page from sitemap 
		// /browse/ or /search/ : search
		// /*/					: gallery album fullview
		// /*/*/				: gallery album photo

		// ok. load galleries...
		// Things that need to be done:
		// 1. figure out if custom or default (in plugin) templates are to be used...
		// 2. which type of page is to be loaded.
		// 3. any database content...
		set_var('plugin', 'galleries');
		
		$params = get_var('params');
		$prefix = get_prefix();
		
		$page = false;
				
		// figure out what type of gallery page this is
		if (count($params) < 1) {
			$page = 'index';
		} else {
			$page = 'fullview';
			$gallery_url = $params[0];
			$gallery = get_gallery($gallery_url);
			set_var('item', $gallery);
			set_page_var('item', $gallery);
			set_page_var('gallery', $gallery);
			set_page_var($prefix, $gallery);

			// make sure we were able to find gallery
			if ($gallery->wasFound()) {
				title($gallery->title()); // add to the page's title
				
				// check if we have a specific photo to view...
				if (isset($params[1])) {
					$photo = get_gallery_photo($params[1], $gallery->id);
					set_page_var('photo', $photo);
					title(truncate($photo->title(), 50)); // add to the page's title
					$page = 'photoview';
					
					if ($photo->show_comments())
						add_stylesheet('plugins/comments/stylesheets/comments');
				}
			} else {
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
		} else if (is_template_file('galleries-' . $page)) {
			add_presentation('galleries');
			set_var('page', 'galleries-' . $page);
		} else {
			add_presentation('galleries');
			set_var('plugin-page', 'galleries-' . $page);
		}
	} // page_type_galleries
	
	function search_galleries($terms = array()) {
		global $db;
		// need to handle search based on provided info...
		// provided info could include:
		// $_POST[search] for properties
		// get_page_vars (search[$property])?
		
		$level = get_sitemap_section('galleries', 'type');
		if (!is_array($level)) return array();
		
		$criteria = array();
		
		if (isset($terms['title']) && !empty($terms['title'])) {
			$criteria[] = "(`galleries`.`name` LIKE '%" . $db->escape($terms['title']) . "%')";
		}
		
		if (isset($terms['keywords']) && !empty($terms['keywords'])) {
			$criteria[] = "(`galleries`.`name` LIKE '%" . $db->escape($terms['keywords']) . "%') OR (`galleries`.`description` LIKE '%" . $db->escape($terms['keywords']) . "%')";
		}

		if (is_admin()) {
			if (isset($terms['status']) && !empty($terms['status'])) {
				if ($terms['status'] == 'deleted') {
					$criteria[] = "`galleries`.`deleted_at` IS NOT NULL";
				} else if ($terms['status'] == 'draft') {
					$criteria[] = "`galleries`.`published_at` IS NULL";
					$criteria[] = "`galleries`.`deleted_at` IS NULL";
				} else {
					$criteria[] = "`galleries`.`published_at` IS NOT NULL";			
					$criteria[] = "`galleries`.`deleted_at` IS NULL";
				}
			} else {
				$criteria[] = "`galleries`.`deleted_at` IS NULL";
			}
		} else {
			$criteria[] = "`galleries`.`published_at` IS NOT NULL";			
			$criteria[] = "`galleries`.`deleted_at` IS NULL";
		}
		
		if (isset($terms['section']) && !empty($terms['section']) && is_numeric($terms['section'])) {
			
			$section = get_sitemap_section('galleries', 'type');
			if (!in_array($terms['section'], array($section['id'], $section['parent_id']))) {
				return array();
			}
			
		}

		$sql = "SELECT `galleries`.`id`, `galleries`.`created_at`, CONCAT('galleries') AS `t` FROM `galleries` WHERE (" . join(") AND (", $criteria) . ") ORDER BY `galleries`.`created_at` DESC";
		
		if (isset($terms['limit']) && !empty($terms['limit'])) {
			$sql = $sql . " LIMIT " . $db->escape($terms['limit']);
		}
			
		//dump($sql);
		//$content = $db->get($sql);
		if (!($content = get_memcache($sql))) {
			$content = $db->get($sql);
			set_memcache($sql, $content);
		}
		
		return $content;
	} // search_galleries

	function get_gallery($id) {
		return new gallery($id);
	} // get_gallery
		
	function get_gallery_photo($id, $album = false) {
		return new gallery_photo($id, $album);
	} // get_gallery_photo

	function get_gallery_photos($gallery_id) {
		global $db;
		
		if (is_numeric($gallery_id)) {
			if (!($photos = get_memcache('gallery_photos--' . $gallery_id))) {
				$photos = $db->get('table=>gallery_photos', "where=>(`gallery_id` = " . $db->escape($gallery_id) . ") AND (`deleted_at` IS NULL)", "order=>`order` ASC, `created_at` ASC");
				set_memcache('gallery_photos--' . $gallery_id, $photos);
			}
			$photos = objectize('gallery_photo', $photos);
		
			return $photos;
		}
		return array();
	} // get_gallery_photos
	
	function get_galleries_by_section($section_id = false, $numberToReturn = 30, $exclude_ids = array(0)) {
		global $db;
		
		if (!is_numeric($section_id)) {
			$section = get_var('level');
			$section_id = $section['id'];
		} else {
			$section = get_sitemap_section($section_id, 'id');
		}
		
		if (!is_array($exclude_ids) || (count($exclude_ids) < 1))
			$exclude_ids = array(0);
		
		if ($section['type'] == 'galleries') {
		
			$sql = "SELECT * FROM `galleries` WHERE (`sitemap_id` = " . $db->escape($section['id']) . ") AND ((`id` != " . join(") AND (`id` != ", $exclude_ids) . ")) AND (`published_at` IS NOT NULL) AND (`deleted_at` IS NULL) ORDER BY `published_at` DESC LIMIT " . $db->escape($numberToReturn);
		
		} else /*if (($section['type'] == 'index'))*/ {
			
			$sectionsToSearch = array();
			foreach ($section['subsections'] as $subsection) {
				if (($section['type'] != 'index') || empty($section['content']) || in_array($subsection['url'], trim_explode(',', $section['content'])))
					$sectionsToSearch[] = $subsection['id'];
				
			}
			
			$sqls = array();
			foreach ($sectionsToSearch as $section_id) {
				$sqls[] = "SELECT * FROM `galleries` WHERE (`sitemap_id` = " . $section_id . ") AND ((`id` != " . join(") AND (`id` != ", $exclude_ids) . ")) AND (`deleted_at` IS NULL) AND (`published_at` IS NOT NULL) ORDER BY `published_at` DESC LIMIT " . $db->escape($numberToReturn);
			}
			
			$sql = ("(" . (join(") UNION (", $sqls)) . ") LIMIT " . $db->escape($numberToReturn));
			
		//} else {
		//	$albums = array();
		}
		
		if (!($galleries = get_memcache($sql))) {
			$galleries = $db->get($sql);
			set_memcache($sql, $galleries);
		}
		
		return array_max(objectize('galleries', $galleries), $numberToReturn);		
	} // get_galleries_by_section
	
	function popular_galleries($numberToReturn = 10, $section_id = false) {
		global $db;
		
		$sql = "SELECT `galleries`.*, SUM(`gallery_photos`.`comment_count`) as `count` FROM `galleries` LEFT JOIN `gallery_photos` ON `galleries`.`id` = `gallery_photos`.`gallery_id` WHERE (`galleries`.`published_at` IS NOT NULL) AND (`galleries`.`deleted_at` IS NULL) AND (`gallery_photos`.`deleted_at` IS NULL) GROUP BY `gallery_photos`.`gallery_id` ORDER BY `count` DESC, `galleries`.`published_at` DESC LIMIT " . $db->escape($numberToReturn);
		
		if (!($galleries = get_memcache($sql))) {
			$galleries = $db->get($sql);
			set_memcache($sql, $galleries);
		}
		//$albums = $db->get($sql);
		//$albums = search_galleries(array('sort' => 'published_at DESC', 'limit' => $numberToReturn));
		return objectize('galleries', $galleries);
	} // popular_galleries
	
	function recent_galleries($numberToReturn = 10, $section_id = false) {
		if (!($galleries = get_memcache('recent_galleries--' . $numberToReturn . '--' . $section_id))) {
			$galleries = search_galleries(array('sort' => 'published_at DESC', 'limit' => $numberToReturn));
			set_memcache('recent_galleries--' . $numberToReturn . '--' . $section_id, $galleries);
		}
		return objectize('galleries', $galleries);
	} // recent_galleries
	
	function random_gallery_photos($numberToReturn = 1, $section_id = false) {
		global $db;
		if (!$section_id) $section_id = get_var('level_id');

		$sql = "SELECT `gallery_photos`.* FROM `gallery_photos` LEFT JOIN `galleries` ON `gallery_photos`.`gallery_id` = `galleries`.`id` WHERE (`galleries`.`published_at` IS NOT NULL) AND (`galleries`.`deleted_at` IS NULL) AND (`gallery_photos`.`deleted_at` IS NULL) AND (`galleries`.`sitemap_id` = " . $db->escape($section_id) . ") ORDER BY RAND() LIMIT " . $db->escape($numberToReturn);
		
		if (!($photos = get_memcache($sql))) {
			$photos = $db->get($sql);
			set_memcache($sql, $photos);
		}
		return objectize('gallery_photos', $photos);
	} // random_gallery_photos

?>