<?php

	$params = get_var('params');
	$params = (is_string($params)) ? trim_explode('/', $params) : array();
		
	/*if ((count($params) < 2)) {
		include is_plugin_file($section['type'], 'admin/galleries');
	} else if (count($params) === 2) {
		$gallery = get_gallery(($params[1] == 'add') ? false : $params[1]);
		include is_plugin_file($section['type'], 'admin/galleries-edit');			
	} else if (count($params) === 3) {
		$gallery = get_gallery(($params[1] == 'add') ? false : $params[1]);
		$photo = get_gallery_photo($params[2], $gallery);
		include is_plugin_file($section['type'], 'admin/galleries-photos-edit');
	}*/
	if ((count($params) < 2) || (isset($params[1]) && ($params[1] == 'list'))) {
		//$blogs = objectize('blogs', search_blogs(array('section' => get_var('id'), 'sort' => '`created_at` DESC', 'limit' => '0,20')));
		require_once LIBRARY . "functions.pagination.php";
		
		// figure out what content we need... 
		// this is easy for just index because we dont need to search...
		$_GET['section'] = get_var('id');
		if (!isset($GET['sort']))
			$_GET['sort'] = '`created_at` DESC';
		$items = search_galleries($_GET);
		
		set_page_var('total_count', count($items));
		$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());
		//set_page_var('items', objectize('gallery', $items));
		$galleries = $items = objectize('gallery', $items);
		set_page_var('items', $galleries);
		
		include is_plugin_file($section['type'], ((isset($params[1]) && ($params[1] == 'list')) ? 'admin/galleries-list' : 'admin/galleries-browse'));
		
	} else if (count($params) === 2) {
		$gallery = get_gallery(($params[1] == 'add') ? false : $params[1]);
		include is_plugin_file($section['type'], 'admin/galleries-edit');			
	}
	
?>