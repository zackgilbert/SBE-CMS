<?php

	$params = get_var('params');
	$params = (is_string($params)) ? trim_explode('/', $params) : array();
		
	if ((count($params) < 2) || (isset($params[1]) && ($params[1] == 'list'))) {
		require_once LIBRARY . "functions.pagination.php";
		
		// figure out what content we need... 
		// this is easy for just index because we dont need to search...
		$_GET['section'] = get_var('id');
		if (!isset($GET['sort']))
			$_GET['sort'] = '`created_at` DESC';
		$_GET['start'] = '2010-01-01';
		$_GET['end'] = format_date(timestampFromFilter('180-days'), 'Y-m-d');
		$items = search_events($_GET);
		
		set_page_var('total_count', count($items));
		$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());
		$events = $items = objectize('event', $items);
		set_page_var('items', $events);
		
		include is_plugin_file($section['type'], ((isset($params[1]) && ($params[1] == 'list')) ? 'admin/events-list' : 'admin/events-browse'));
		
	} else if (count($params) === 2) {			
		$event = get_event(($params[1] == 'add') ? false : $params[1]);
		include is_plugin_file($section['type'], 'admin/events-edit');			
	}
	
?>