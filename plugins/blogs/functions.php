<?php

	function get_blog($blog_id = false, $blog_date = false) {
		return new blog($blog_id, $blog_date);
	} // get_blog

	function get_blogs_by_section($section_id, $numberToReturn = 30, $exclude_ids = array()) {
		global $db;
		
		if (!is_numeric($section_id)) {
			$section = get_var('level');
			$section_id = $section['id'];
		} else {
			$section = get_sitemap_section($section_id, 'id');
		}
		
		if (!is_array($exclude_ids) || (count($exclude_ids) < 1))
			$exclude_ids = array(0);
		
		if ($section['parent_id'] === '0') {

			$sql = "SELECT `blogs`.*, `blog_sitemap`.`sitemap_id` FROM `blogs` INNER JOIN `blog_sitemap` ON `blogs`.`id` = `blog_sitemap`.`blog_id` WHERE ((`blogs`.`id` != " . join(") AND (`blogs`.`id` != ", $exclude_ids) . ")) AND (`blogs`.`deleted_at` IS NULL) AND (`blogs`.`published_at` IS NOT NULL) ORDER BY `blogs`.`published_at` DESC LIMIT " . $db->escape($numberToReturn); 
			
		} elseif (($section['type'] == 'content') && ($section['content'] == 'blogs')) {
		
			$sql = "SELECT `blogs`.*, `blog_sitemap`.`sitemap_id` FROM `blogs` INNER JOIN `blog_sitemap` ON `blogs`.`id` = `blog_sitemap`.`blog_id` WHERE (`blog_sitemap`.`sitemap_id` = " . $db->escape($section_id) . ") AND ((`blogs`.`id` != " . join(") AND (`blogs`.`id` != ", $exclude_ids) . ")) AND (`blogs`.`deleted_at` IS NULL) AND (`blogs`.`published_at` IS NOT NULL) ORDER BY `blogs`.`published_at` DESC LIMIT " . $db->escape($numberToReturn); 
		
		} else /*if (($section['type'] == 'index'))*/ {
			
			$sectionsToSearch = array();
			foreach ($section['subsections'] as $subsection) {
				if (($section['type'] != 'index') || empty($section['content']) || in_array($subsection['url'], trim_explode(',', $section['content'])))
					$sectionsToSearch[] = $subsection['id'];
				
			}
			
			$sqls = array();
			foreach ($sectionsToSearch as $section_id) {
				$sqls[] = "SELECT `blogs`.*, `blog_sitemap`.`sitemap_id` FROM `blogs` INNER JOIN `blog_sitemap` ON `blogs`.`id` = `blog_sitemap`.`blog_id` WHERE (`blog_sitemap`.`sitemap_id` = " . $db->escape($section_id) . ") AND ((`blogs`.`id` != " . join(") AND (`blogs`.`id` != ", $exclude_ids) . ")) AND (`blogs`.`deleted_at` IS NULL) AND (`blogs`.`published_at` IS NOT NULL) ORDER BY `blogs`.`published_at` DESC LIMIT " . $db->escape($numberToReturn);
			}
			
			$sql = ("(" . (join(") UNION (", $sqls)) . ") LIMIT " . $db->escape($numberToReturn));
			
			//$blogs = $db->get($sql);
			
		}
		
		if (!($blogs = get_memcache($sql))) {
			$blogs = $db->get($sql);
			set_memcache($sql, $blogs);
		}
		
		return array_max(objectize('blog', $blogs), $numberToReturn);		
	} // get_blogs_by_section
	
	function recent_blogs($numberToReturn = 10, $section_id = false) {
		$section_id = section_id($section_id);
		if (!($blogs = get_memcache('recent_blogs--' . $numberToReturn . '--' . $section_id))) {
			$blogs = get_blogs_by_section($section_id, $numberToReturn);
			set_memcache('recent_blogs--' . $numberToReturn . '--' . $section_id, $blogs);
		}
		return $blogs;
		//return get_blogs_by_section($section_id, $numberToReturn);
	} // recent_blog
	
	function blog_has_categories($section = false) {
		$cats = blog_categories($section);
		return (is_array($cats) && (count($cats) > 0));
	} // blog_has_categories
	
	function blog_categories($section = false) {
		global $db;
		
		if (!$section) {
			$section = get_var('level_id');
		}
		
		$cats = get_memcache("blog_categories--" . $section);
		
		if (!$cats) {
			$cats = $db->get('table=>blog_categories', "where=>(`sitemap_id` = " . $db->escape($section) . ") AND (`deleted_at` IS NULL)", "order=>name ASC");

			set_memcache("blog_categories--" . $section, $cats);
		}
		
		return $cats;
	} // blog_categories
	
	function link_to_blog_categories($section = false) {
		if (!$section) {
			$section = get_var('level_id');
		}
		
		$cats = blog_categories($section);
		$categories = array();
		foreach ($cats as $cat) {
			//$categories[] = '<a href="' . get_sitemap_section_url($section) . 'categories/' . $cat['url'] . '/">' . $cat['name'] . ' (' . $cat['count'] . ')</a>';
			$categories[] = '<a href="' . get_sitemap_section_url($section) . 'categories/' . $cat['url'] . '/">' . $cat['name'] . '</a>';
		}
		return join(', ', $categories);
	} // link_to_blog_categories
	
	function blog_archive_dates($section = false) {
		global $db;

		$section = section($section);
		
		if (!($first_post = get_memcache('blog_archive_dates--' . $section['id']))) {
			$first_post = $db->get("SELECT `blogs`.`published_at` FROM `blogs` INNER JOIN `blog_sitemap` ON `blogs`.`id` = `blog_sitemap`.`blog_id` WHERE (`blog_sitemap`.`sitemap_id` = " . $db->escape($section['id']) . ") AND (`blogs`.`deleted_at` IS NULL) AND (`blogs`.`published_at` IS NOT NULL) ORDER BY `blogs`.`published_at` ASC LIMIT 1");
			set_memcache('blog_archive_dates--' . $section['id'], $first_post);
		}
		$start_date = $first_post[0]['published_at'];
		
		$years = array();
		$currentYear = '0000';

		foreach (dateRangeToArray(NOW, format_date($start_date, 'Y-m-01')) as $month) {
			$years[year($month)][month($month)] = '<a href="' . get_sitemap_section_url($section['id']) . year($month) . '/' . pad(month($month)) . '/">' . format_date($month, "F") . '</a>';
		}
		return $years;
	} // blog_archive_dates
	
	function search_blogs($terms = array()) {
		global $db;
		// accepts: keywords (search headline, subheadline, body), exact match of keywords, section, type, author, date range, status
		// supply array of search terms (keywords, exact-match[true|false], section[id], author[id], date[?], status[?])
		
		$criteria = array();

		if (isset($terms['keywords']) && !empty($terms['keywords'])) {
			//if ($terms['exact-match']) {
				$criteria[] = "(`blogs`.`body` LIKE '%" . $db->escape($terms['keywords']) . "%') OR (`blogs`.`title` LIKE '%" . $db->escape($terms['keywords']) . "%')";
			//} else {
				// this needs to be reworked for better, more forgiving search
			//	$criteria[] = "(`blogs`.`body` LIKE '%" . $db->escape($terms['keywords']) . "%') OR (`blogs`.`title` LIKE '%" . $db->escape($terms['keywords']) . "%')";
			//}
		}

		if (isset($terms['author']) && !empty($terms['author']) && is_numeric($terms['author'])) {
			//$criteria[] = "(`blogs`.`author_ids` = '" . $db->escape($terms['author']) . "') OR (`blogs`.`author_ids` LIKE '" . $db->escape($terms['author']) . ",%') OR (`blogs`.`author_ids` LIKE '%, " . $db->escape($terms['author']) . "') OR (`blogs`.`author_ids` LIKE '%, " . $db->escape($terms['author']) . ",%')";
			$criteria[] = "(`blogs`.`user_id` = '" . $db->escape($terms['author']) . "')";
		}
		
		if (isset($terms['category']) && !empty($terms['category']) && is_string($terms['category'])) {
			$criteria[] = "(`blogs`.`blog_categories` = '" . $db->escape($terms['category']) . "') OR (`blogs`.`blog_categories` LIKE '" . $db->escape($terms['category']) . ",%') OR (`blogs`.`blog_categories` LIKE '%, " . $db->escape($terms['category']) . "') OR (`blogs`.`blog_categories` LIKE '%, " . $db->escape($terms['category']) . ",%')";
		}
		
		if (isset($terms['date']) && !empty($terms['date']) && !empty($terms['date'])) {
			$criteria[] = "((`blogs`.`created_at` LIKE '" . $db->escape($terms['date']) . "%') OR (`blogs`.`published_at` LIKE '" . $db->escape($terms['date']) . "%'))";
		}

		/*if (isset($terms['status']) && !empty($terms['status'])) {
			if ($terms['status'] == 'deleted') {
				$criteria[] = "`blogs`.`deleted_at` IS NOT NULL";
			} else if ($terms['status'] == 'published') {
				$criteria[] = "`blogs`.`published_at` IS NOT NULL";
				$criteria[] = "`blogs`.`deleted_at` IS NULL";				
			} else {
				$criteria[] = "`blogs`.`status` = '" . $db->escape($terms['status']) . "'";
				$criteria[] = "`blogs`.`deleted_at` IS NULL";
			}
		} else {
			$criteria[] = "`blogs`.`deleted_at` IS NULL";
		}*/
		if (is_admin()) {
			if (isset($terms['status']) && !empty($terms['status'])) {
				if ($terms['status'] == 'deleted') {
					$criteria[] = "`blogs`.`deleted_at` IS NOT NULL";
				} else if ($terms['status'] == 'draft') {
					$criteria[] = "`blogs`.`published_at` IS NULL";
					$criteria[] = "`blogs`.`deleted_at` IS NULL";
				} else {
					$criteria[] = "`blogs`.`published_at` IS NOT NULL";			
					$criteria[] = "`blogs`.`deleted_at` IS NULL";
				}
			} else {
				$criteria[] = "`blogs`.`deleted_at` IS NULL";
			}
		} else {
			$criteria[] = "`blogs`.`published_at` IS NOT NULL";			
			$criteria[] = "`blogs`.`deleted_at` IS NULL";
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

		$criteria[] = "(`blog_sitemap`.`sitemap_id` = " . join(") OR (`blog_sitemap`.`sitemap_id` = ", $subsections) . ")";
		
		$sql = "SELECT `blogs`.*, CONCAT('blogs') AS `t` FROM `blogs` INNER JOIN `blog_sitemap` ON `blogs`.`id` = `blog_sitemap`.`blog_id` WHERE (" . join(") AND (", $criteria) . ")";

		//$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page']-1 : 0;
		//$limit = 20;
		//$sql = $sql . " ORDER BY `blogs`.`created_at` DESC LIMIT " . ($page*$limit) . ", " . $limit;
		
		if (isset($terms['sort']) && !empty($terms['sort'])) {
			$sql = $sql . " ORDER BY `blogs`." . $db->escape($terms['sort']);
		} else {
			$sql = $sql . " ORDER BY `blogs`.`published_at` DESC";
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
	} // search_blogs
	
	function page_type_blogs() {
		// BREAKDOWN OF POSSIBLE URLs:
		// / 					: blog index page from sitemap 
		// /browse/ or /search/ : search
		// /*/					: blog article fullview
		// what about categories/authors/tags/etc?
		// what about stuff like print version or rss?

		// ok. load blogs...
		// Things that need to be done:
		// 1. figure out if custom or default (in plugin) templates are to be used...
		// 2. which type of page is to be loaded.
		// 3. any database content...
		set_var('plugin', 'blogs');
		
		$params = get_var('params');
		$prefix = get_prefix();
		
		$page = false;
				
		// figure out what type of blog page this is
		if ((count($params) < 1) || in_array($params[0], array('search', 'browse'))) {
			require_once LIBRARY . "functions.pagination.php";
			$page = 'index';
			
			// figure out what content we need... 
			// this is easy for just index because we dont need to search...
			$_GET['section'] = get_var('level_id');
			$items = search_blogs($_GET);
			
			set_page_var('total_count', count($items));
			$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());
			set_page_var('items', objectize('blog', $items));
		} else if ((count($params) > 1) && (count($params) < 3)) {
			require_once LIBRARY . "functions.pagination.php";
			$page = 'index';
			
			if (isset($params[0]) && ($params[0] == 'categories')) : 
				$cats = blog_categories();
				$_GET['category'] = $params[1];
				set_var('category', capitalize($params[1]));
				foreach ($cats as $cat) :
					if ($cat['url'] == $params[1]) :
						set_var('category', $cat['name']);
					endif;
				endforeach;
				$_GET['sort'] = 'published_at ASC';
			elseif (count($params) == 2) :
				set_var('date', $params[0] . '-' . pad($params[1]) . '-01');
				$_GET['date'] = $params[0] . '-' . pad($params[1]);
				$_GET['sort'] = 'published_at ASC';
			endif;
		
			$_GET['section'] = get_var('level_id');
			$items = search_blogs($_GET);
			set_page_var('total_count', count($items));
			$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());
			set_page_var('items', objectize('blog', $items));
		} else {
			$page = 'fullview';
			$blog_url = $params[2];
			$blog = get_blog($blog_url);
			set_var('item', $blog);
			set_page_var('item', $blog);
			set_page_var('blog', $blog);
			set_page_var($prefix, $blog);

			if ($blog->show_comments())
				add_stylesheet('plugins/comments/stylesheets/comments');

			// make sure we were able to find blog
			if ($blog->wasFound()) {
				title($blog->title()); // add to the page's title
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
		} else if (is_template_file('blogs-' . $page)) {
			add_presentation('blogs');
			set_var('page', 'blogs-' . $page);
		} else {
			add_presentation('blogs');
			set_var('plugin-page', 'blogs-' . $page);
		}
	} // page_type_blogs
	
	//function site_blogs() {
		// BREAKDOWN OF POSSIBLE URLs:
		// / 					: blogs index page from sitemap 
		// /browse/ or /search/ : search
		// /*/					: blogs fullview
	/*	
		$params = get_var('params');
		$prefix = get_prefix();
		
		set_page_var('content_type', $prefix);
		
		add_presentation($prefix);

		// this should be taken out once we want the index to actually load...
		//if (!isset($params[0]))
		//	$params[0] = 'search';
		
		if (!isset($params[0]) || (count($params) == 2)) :
		
			if (isset($params[0]) && ($params[0] == 'categories')) : 
				$cats = blog_categories();
				$_GET['category'] = $params[1];
				set_var('category', capitalize($params[1]));
				foreach ($cats as $cat) :
					if ($cat['url'] == $params[1]) :
						set_var('category', $cat['name']);
					endif;
				endforeach;
				$_GET['sort'] = 'published_at ASC';
			elseif (count($params) == 2) :
				set_var('date', $params[0] . '-' . pad($params[1]) . '-01');
				$_GET['date'] = $params[0] . '-' . pad($params[1]);
				$_GET['sort'] = 'published_at ASC';
			endif;
		
			$_GET['section'] = get_var('level_id');
			$items = search_blogs($_GET);
			set_page_var('total_count', count($items));
			$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());
			set_page_var('items', objectize('blog', $items));
		
			if (get_var('is_rss') && (section('rss') == 'enable')) :
			
				blog_rss();
			
			else : 
			
				// index page...
				add_rss();
				set_var('page', $prefix . '-index');
				
			endif;
			
		else :
			// specific blog page
			if (count($params) == 1) {
				// 1st param is blog's url
				$item_url = $params[0];
				$item_date = false;
			} else {
				// theres a year and month attached
				$item_url = array_pop($params);
				$item_date = join('-', $params);				
			}
			//$item_url = $params[0];
			$item = get_blog($item_url, $item_date);
			set_var('item', $item);
			set_page_var('item', $item);
			set_page_var(singularize($prefix), $item);
			
			// things to check for $params[1]:
			// email / print / rss
			
			
			if (!$item->wasFound()) :
				 
				// not found
				set_var('page', $prefix . '-404');
			
			elseif (get_var('is_email')) :

				set_var('template', false);
				set_var('page', 'email');

			elseif (get_var('is_print')) :

				set_var('template', 'print');
				if ($item->wasFound()) :
					if (is_template_file('pages/' . $prefix . '-print.php')) :
						set_var('page', $prefix . '-print');
					else :
						set_var('page', $prefix . '-fullview');
					endif;
				else :
					set_var('page', 'print-error');
				endif;

			elseif (get_var('is_rss') && (section('rss') == 'enable')) :
				
				blog_comment_rss();
			
			else : 
							
				// otherwise: fullview	
				title($item->title());
				meta('description', $item->title() . ', ' . $item->body());
				add_presentation('comments');
				add_rss();			
				set_var('page', $prefix . '-fullview');

			endif;
			
		endif;
	} // site_blogs*/
	
	function blog_rss() {
		$items = get_page_var('items');
		
		$rss['title'] = title();
		$rss['link'] = 'http://' . $_SERVER['SERVER_NAME'] . section_link();
		$rss['description'] = "Blog posts from " . section('name');
		$rss['hasComments'] = true;

		// get items
		$rss['items'] = array();

		foreach ($items as $item) :
			$itemRSS = array();
			$itemRSS['title'] = $item->title();
			$itemRSS['link'] = 'http://' . $_SERVER['SERVER_NAME'] . $item->link();
			$itemRSS['creator'] = strip_tags(link_to_authors($item->author_ids));
			$itemRSS['pubDate'] = $item->published_at;
			$itemRSS['content'] = $item->body();
			$rss['items'][] = $itemRSS;
		endforeach;
			
		set_page_var('rss', $rss);

		site_rss();
	} // blog_rss
	
	function blog_comment_rss() {
		$item = get_page_var('item');
		// rss of a fullview item, means rss is of comments...
		$comments = array_max(array_reverse($item->comments()), 10);

		title($item->title());
		$rss['title'] = title('Comments');
		$rss['link'] = 'http://' . $_SERVER['SERVER_NAME'] . $item->link() . "#comments";
		$rss['description'] = "Comments for the blog post, '" . $item->title() . "'";
		$rss['comments'] = false;

		// get items
		$rss['items'] = array();

		foreach ($comments as $comment) :
			$commentRSS = array();
			$commentRSS['title'] = $comment->name . " on `" . $item->title() . "`";
			$commentRSS['link'] = 'http://' . $_SERVER['SERVER_NAME'] . $item->link() . '#comment-' . $comment->id;
			$commentRSS['creator'] = $comment->name;
			$commentRSS['pubDate'] = $comment->created_at;
			$commentRSS['content'] = $comment->comment;
			$rss['items'][] = $commentRSS;
		endforeach;
			
		set_page_var('rss', $rss);

		site_rss();
		
	} // blog_rss
	
	function get_blog_authors() {
		global $db;
		
		$authors = $db->get("SELECT * FROM `users` WHERE (`types` = 'admins') OR (`types` = 'editors') OR (`types` = 'authors') AND (`deleted_at` IS NULL) ORDER BY `name` ASC");
		
		return $authors;
	}  // get_blog_authors
	
	function blog_authors() {
		$author_ids = blog_setting('author_ids');
		$authors = trim_explode(",", $author_ids);
		$authors = objectize('author', $authors);
		return $authors;
	} // blog_authors
	
	function blog_setting($name = false, $sitemap_id = false) {
		global $db;	
			
		if (!is_numeric($sitemap_id)) $sitemap_id = section('id');
		
		$setting = get_memcache("blog_setting--" . $sitemap_id . "--" . $name);
		
		if (!$setting) {
			$value = $db->getProperty("table=>blog_settings", "where=>(`sitemap_id` = " . $db->escape($sitemap_id) . ") AND (`name` = '" . $db->escape($name) . "') AND (`deleted_at` IS NULL)", 'property=>value');
			set_memcache('blog_settings--' . $sitemap_id . '--' . $name, $value);
		}
		
		return $value;
	} // blog_setting
	
?>