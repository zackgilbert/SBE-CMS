<?php

	$params = get_var('params');
	$params = (is_string($params)) ? trim_explode('/', $params) : array();
		
	if ((count($params) < 2) || (isset($params[1]) && ($params[1] == 'list'))) {
		//$blogs = objectize('blogs', search_blogs(array('section' => get_var('id'), 'sort' => '`created_at` DESC', 'limit' => '0,20')));
		require_once LIBRARY . "functions.pagination.php";
		
		// figure out what content we need... 
		// this is easy for just index because we dont need to search...
		$_GET['section'] = get_var('id');
		if (!isset($GET['sort']))
			$_GET['sort'] = '`created_at` DESC';
		$items = search_blogs($_GET);
		
		set_page_var('total_count', count($items));
		$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());
		//set_page_var('items', objectize('blog', $items));
		$blogs = $items = objectize('blog', $items);
		set_page_var('items', $blogs);
		
		include is_plugin_file($section['type'], ((isset($params[1]) && ($params[1] == 'list')) ? 'admin/blogs-list' : 'admin/blogs-browse'));
		
	} else if (count($params) === 2) {
		$blog = get_blog(($params[1] == 'add') ? false : $params[1]);
		include is_plugin_file($section['type'], 'admin/blogs-edit');			
	}
	
?>