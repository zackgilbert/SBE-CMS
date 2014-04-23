<?php

	/*
	function events() {
		global $event;
		
		if (!get_session_var('events_url'))
			set_session_var('events_url', get_sitemap_section_url(get_var('level_id')));

		$params = get_var('params');

		$prefix = get_var('prefix');

		add_stylesheet($prefix);
		add_javascript($prefix);
		
		$isEvent = true;
		
		if ((count($params) > 1) || is_numeric(current($params))) {
			$isEvent = false;
		}

		if ($isEvent) :
			
			$event_id = get_var('id');
			
			if (current($params)) {
				$event_date = array_shift($params);
				set_page_var('date', $event_date);
			}
			
			$event = new event($event_id);

			set_page_var('event', $event);
			set_session_var('event_id', get_var('id'));

			if ($event->wasFound()) :

				set_var('page', $prefix . '-fullview');

			else: 

				set_var('page', $prefix.'-404');

			endif;
			
		else :

			$year = get_var('id');
			$month = array_shift($params);
			$day = array_shift($params);
			
			set_page_var('year', $year);
			set_page_var('month', $month);
			set_page_var('day', $day);
			
			set_var('page', $prefix . '-bydate');
			
		endif;
		
		set_var('params', $params);
		
	} // events
	*/
	
	function get_user_events($user_id) {
		global $db;
		//return $db->get('table=>events_users', "where=>(`user_id` = " . $db->escape($user_id) . ") AND (`deleted_at` IS NULL)");
		$events = $db->get("SELECT `events`.*, `users_events`.`attending_on` FROM `events` LEFT JOIN (`users_events`) ON `events`.`id` = `users_events`.`table_id` WHERE (`users_events`.`table` = 'events') AND (`users_events`.`user_id` = " . $db->escape($user_id) . ") AND (`users_events`.`attending_on` >= '" . TODAY . "') AND (`users_events`.`deleted_at` IS NULL) AND (`events`.`deleted_at` IS NULL) ORDER BY `users_events`.`attending_on` ASC");
		return $events;
	} // get_user_events
	
	function get_location_events($location, $numberOfDays = 30) {
		global $db;
		$events = $db->get("SELECT * FROM `events` WHERE (`location` LIKE '" . $db->escape($location) . "') AND (`published_at` IS NOT NULL) AND (`deleted_at` IS NULL)");
		
		$eventDates = array();	
		foreach ($events as $event) {
			$event = new event($event);
			$eventDates = array_merge($eventDates, $event->asDates($numberOfDays));
		}
		//dump($eventDates);

		$date = array();
		$start_time = array();
		foreach ($eventDates as $key => $row) {
		    $date[$key]  = $row['_date'];
			//$start_time[$key] = $row['start_time'];
		}

		array_multisort($date, SORT_ASC, /*$start_time, SORT_ASC,*/ $eventDates);
		
		$events = array();
		foreach($eventDates as $eventID => $event) {
			$events[$eventID] = new event($event, $event['_date']);
		}
		
		return $events;
	} // get_location_events
	
	function get_user_past_events($user_id) {
		global $db;
		$events = $db->get("SELECT `events`.*, `users_events`.`attending_on` FROM `events` LEFT JOIN (`users_events`) ON `events`.`id` = `users_events`.`table_id` WHERE (`users_events`.`table` = 'events') AND (`users_events`.`user_id` = " . $db->escape($user_id) . ") AND (`users_events`.`attending_on` < '" . TODAY . "') AND (`users_events`.`deleted_at` IS NULL) AND (`events`.`deleted_at` IS NULL) ORDER BY `users_events`.`attending_on` ASC");
		return $events;
	} // get_user_past_events

	function get_event($id) {
		require_once(dirname(__FILE__) . '/class.event.php');
		return new event($id);
	} // get_event
	
	function get_event_categories($sitemap_id = false) {
		global $db;
		if (!is_numeric($sitemap_id)) $sitemap_id = section_id();
		$section = get_sitemap_section($sitemap_id);
		
		$event_sections = get_sitemap_sections_by_content('events', $section);
		
		$categories = false;		
		if (is_array($event_sections) && (count($event_sections) > 0))
			$categories = $db->get('table=>event_categories', "where=>(`deleted_at` IS NULL) AND ((`sitemap_id` = " . join(") OR (`sitemap_id` = ", flatten($event_sections)) . "))", "order=>name ASC");
		
		if (!is_array($categories) || (count($categories) < 1))
			$categories = $db->get('table=>event_categories', "where=>(`deleted_at` IS NULL) AND (`sitemap_id` = 0)", "order=>name ASC");
		
		return $categories;
	} // get_event_categories
	
	function get_event_category($category, $return = false, $sitemap_id = false) {
		global $db;
		if (!is_numeric($sitemap_id)) $sitemap_id = section_id();
		
		if ($category) {
			if (is_numeric($category)) {
				$category = $db->getOne('table=>event_categories', "id=>" . $db->escape($category));
			} else {
				$category = $db->getOne('table=>event_categories', "where=>(`deleted_at` IS NULL) AND ((`name` LIKE '" . $db->escape($category) . "') OR (`url` LIKE '" . $db->escape($category) . "')) AND (`sitemap_id` = " . $sitemap_id . ")");
			}
			
			if ($return) {
				if (isset($category[$return])) {
					return $category[$return];
				} else {
					return false;
				}
			} else {
				return $category;
			}
		}
		return get_event_categories();
	} // get_event_category
	
	function link_to_event($id, $date = false) {
		$event = get_event($id);
		return '<a href="' . $event->link($date) . '">' . $event->name() . '</a>';
	} // link_to_event
	
	function events_activity_text($activity) {
		return 'added ' . link_to_event($activity['table_id']) . " to events they're attending";
	} // events_activity_text
	
	function page_type_events() {
		// BREAKDOWN OF POSSIBLE URLs:
		// / 					: event index page from sitemap 
		// /browse/ or /search/ : search
		// /*/					: event fullview

		// ok. load events...
		// Things that need to be done:
		// 1. figure out if custom or default (in plugin) templates are to be used...
		// 2. which type of page is to be loaded.
		// 3. any database content...
		set_var('plugin', 'events');
		
		$params = get_var('params');
		$prefix = get_prefix();
		
		$page = false;
				
		set_page_var('content_type', $prefix);

		add_presentation('autosuggest');
		add_presentation('jquery.datepicker');
		
		set_var('calendar', TODAY);
				
		if (count($params) < 1) {
			// index page...
			set_var('calendar_year', year(get_var('calendar')));
			set_var('calendar_month', pad(month(get_var('calendar'))));
			set_var('calendar_day', pad(day(get_var('calendar'))));
			set_var('calendar_timestamp', timestamp(get_var('calendar_year'), get_var('calendar_month'), get_var('calendar_day')));
			
			//add_rss();
			$page = 'index';
			
		/*elseif (get_var('is_rss') && (section('rss') == 'enable') && !isset($params[0])) :
				
			$items = search_events(array('limit' => 10));
			
			set_page_var('total_count', count($items));
			$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());
						
			set_page_var('items', array_max($items, 10));
			
			event_rss();*/
		} else {
			
			require_once LIBRARY . 'functions.pagination.php';
			
			if (isset($params[0]) && in_array($params[0], array('search', 'browse'))) {
				if (isset($_GET['date']) && (strpos($_GET['date'], "-") == 4)) {
					$dateParts = trim_explode('-', $_GET['date']);
					set_var('calendar_year', $dateParts[0]);
					set_var('calendar_month', $dateParts[1]);
					if (!isset($dateParts[2])) {
						$dateParts[2] = '01';
						set_var('calendar_day', false);
					} else {
						set_var('calendar_day', $dateParts[2]);
					}
					set_var('calendar', join("-", $dateParts));
				} else {
					set_var('calendar', TODAY);
					set_var('calendar_year', year(get_var('calendar')));
					set_var('calendar_month', pad(month(get_var('calendar'))));
					set_var('calendar_day', pad(day(get_var('calendar'))));
				}
				//set_var('calendar_day', pad(day(get_var('calendar'))));
				set_var('calendar_timestamp', timestamp(get_var('calendar_year'), get_var('calendar_month'), get_var('calendar_day')));

				if (!isset($_GET['date']))
					$_GET['date'] = '30-days';

				// search event in this section
				$items = search_events($_GET);
				set_page_var('total_count', count($items));
				$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());

				set_page_var('items', $items);
				$page = 'browse';
			} elseif (count($params) > 1) {
				$search = array();

				if ($params[0] == 'types') {

					set_var('calendar', TODAY);
					set_var('calendar_year', year(get_var('calendar')));
					set_var('calendar_month', pad(month(get_var('calendar'))));
					set_var('calendar_day', pad(day(get_var('calendar'))));
					set_var('calendar_timestamp', timestamp(get_var('calendar_year'), get_var('calendar_month'), get_var('calendar_day')));

					$search['categories'][] = $_GET['categories'][] = get_event_category($params[1], 'id');

					if (!isset($_GET['date'])) {
						$search['date'] = $_GET['date'] = '30-days';
					}

				} elseif (count($params) == 2) {

					$search['start'] = $params[0] . '-' . pad($params[1]) . '-01';
					$search['end'] = $params[0] . '-' . pad($params[1]) . '-' . daysInMonth($params[1], $params[0]);

					set_var('calendar', $params[0] . '-' . pad($params[1]) . '-01');
					set_var('calendar_year', $params[0]);
					set_var('calendar_month', pad($params[1]));
					//set_var('calendar_day', pad(day(get_var('calendar'))));
					set_var('calendar_day', false);
					set_var('calendar_timestamp', timestamp(get_var('calendar_year'), get_var('calendar_month'), '01'));
					$_GET['date'] = $params[0] . '-' . pad($params[1]);

				} elseif (count($params) == 3) {

					$search['start'] = $params[0] . '-' . pad($params[1]) . '-' . pad($params[2]);
					$search['end'] = $params[0] . '-' . pad($params[1]) . '-' . pad($params[2]);

					set_var('calendar', $search['start']);
					set_var('calendar_year', year(get_var('calendar')));
					set_var('calendar_month', pad(month(get_var('calendar'))));
					set_var('calendar_day', pad(day(get_var('calendar'))));
					set_var('calendar_timestamp', timestamp(get_var('calendar_year'), get_var('calendar_month'), get_var('calendar_day')));
					$_GET['date'] = $params[0] . '-' . pad($params[1]) . '-' . pad($params[2]);

				}

				// search event in this section
				$items = search_events($search);

				set_page_var('total_count', count($items));
				$items = array_max($items, pagination_browse_limit(), (pagination_current_page()-1)*pagination_browse_limit());

				set_page_var('items', $items);
				set_page_var('events', $items);

				//add_presentation($prefix . '-resultview');

				$page = 'browse';
			} else {
				// specific event page
				// 1st param is event's url
				$event_url = $params[0];
				$event = get_event($event_url);
				set_var('item', $event);
				set_page_var('item', $event);
				set_page_var('event', $event);
				set_page_var(singularize($prefix), $event);

				if (!$event->wasFound()) {

					//set_var('page', $prefix . '-404');
					$page = '404';
				//} elseif (get_var('is_rss') && (section('rss') == 'enable')) {

				//	event_comment_rss();

				} else {
					// otherwise: fullview
					title($event->title());
					//add_rss();
					add_stylesheet('plugins/comments/stylesheets/comments');
					//set_var('page', $prefix . '-fullview');
					$page = 'fullview';
					
				}
			}				
		}
		
		// figure out what templates to use...
		// - custom templates based on $prefix
		// - custom templates based on plugin name
		// - default templates from plugin
		if (is_template_file($prefix . '-' . $page)) {
			add_presentation($prefix);
			set_var('page', $prefix . '-' . $page);
		} else if (is_template_file('events-' . $page)) {
			add_presentation('events');
			set_var('page', 'events-' . $page);
		} else {
			add_presentation('events');
			set_var('plugin-page', 'events-' . $page);
		}
	} // page_type_events
	
	function admin_events($id = false) {
		add_presentation('autocomplete');
		
		$item = call_user_func('get_event', $id);
		set_page_var('item', $item);
		set_page_var('event', $item);
		if (isset($item->type))
			set_page_var('type', singularize($item->type));
			
		return $item;
	} // admin_events
	
	function search_events($terms = array()) {
		global $db;
		// need to handle search based on provided info...
		// provided info could include:
		// $_POST[search] for properties
		// get_page_vars (search[$property])?
		
		$criteria = array();
		
		if (isset($terms['keywords']) && !empty($terms['keywords'])) {
			//if ($terms['exact-match']) {
				$criteria[] = "(`events`.`name` LIKE '%" . $db->escape($terms['keywords']) . "%') OR (`events`.`description` LIKE '%" . $db->escape($terms['keywords']) . "%')";
			//} else {
				// this needs to be reworked for better, more forgiving search
			//	$criteria[] = "(`artists`.`name` LIKE '%" . $terms['keywords'] . "%') OR (`artists`.`biography` LIKE '%" . $terms['keywords'] . "%')";
			//}
		}
		
		if (is_admin()) {
			if (isset($terms['status']) && !empty($terms['status'])) {
				if ($terms['status'] == 'deleted') {
					$criteria[] = "`events`.`deleted_at` IS NOT NULL";
				} else if ($terms['status'] == 'draft') {
					$criteria[] = "`events`.`published_at` IS NULL";
					$criteria[] = "`events`.`deleted_at` IS NULL";
				} else if ($terms['status'] == 'public') {
					$criteria[] = "`events`.`published_at` IS NOT NULL";
					$criteria[] = "`events`.`deleted_at` IS NULL";
				}
			} else {
				$criteria[] = "`events`.`deleted_at` IS NULL";				
			}	
		} else {
			$criteria[] = "`events`.`published_at` IS NOT NULL";
			$criteria[] = "`events`.`deleted_at` IS NULL";
		}
		
		
		/*if (isset($terms['section']) && !empty($terms['section']) && is_numeric($terms['section'])) {
			
			$section = get_sitemap_section('events', 'content');
			$parent = get_sitemap_section($section['parent_id']);
			if (!in_array($terms['section'], array($section['id'], $section['parent_id'], $parent['parent_id']))) {
				return array();
			}
			
		}*/
		
		/*if (!isset($terms['section']) || empty($terms['section']) || !is_numeric($terms['section'])) {
			$root = get_sitemap_root();
			$terms['section'] = $root['id'];
		}*/
		if (isset($terms['section'])) {
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

			$criteria[] = "(`events`.`sitemap_id` = " . join(") OR (`events`.`sitemap_id` = ", $subsections) . ")";			
		}

		if (isset($terms['categories']) && is_array($terms['categories']) && !empty($terms['categories'][0])) {
			$catCriteria = array();
			foreach ($terms['categories'] as $category) {
				if (!empty($category))
					$catCriteria[] = "`events`.`event_category_id` = " . $db->escape($category);
			}
			if (count($catCriteria) > 0)
				$criteria[] = "(" . join(") OR (", $catCriteria) . ")";
		}
		
		if (isset($terms['location']) && !empty($terms['location']) && is_numeric($terms['location'])) {
			$criteria[] = "`events`.`location` = '" . $db->escape($terms['location']) . "'";
		}
		
		$numberOfDays = 30;
		
		if ((isset($terms['start']) && !empty($terms['start']) && isset($terms['end']) && !empty($terms['end'])) || (isset($terms['date']) && !empty($terms['date']))) {
			
			if (isset($terms['start']) && isset($terms['end'])) {
				$start_date = substr($terms['start'], 0, 10);
				$end_date = substr($terms['end'], 0, 10);
				$numberOfDays = daysApart($end_date, $start_date);
			} else if (isset($terms['date']) && (strpos($terms['date'], '-') == 4)) {
				$dateParts = trim_explode('-', $terms['date']);
				
				if (count($dateParts) == 3) {
					$start_date = substr($terms['date'], 0, 10);
					$end_date = substr($terms['date'], 0, 10);
				} else if (count($dateParts) == 2) {
					$start_date = $terms['date'] . '-01';
					$end_date = $terms['date'] . '-' . pad(daysInMonth($dateParts[1], $dateParts[0]));					
				}
				$numberOfDays = daysApart($end_date, $start_date);
				
			} else if (isset($terms['date']) && !empty($terms['date'])) {

				$range = $terms['date'];

				if ($range == 'today') {
					$start_date = TODAY;
					$end_date = TODAY;
					$numberOfDays = 1;
				} elseif ($range == 'tomorrow') {
					$start_date = substr(dayAfter(), 0, 10);
					$end_date = substr(dayAfter(), 0, 10);
					$numberOfDays = 2;
				} else {
					$start_date = TODAY;
					$end_date = format_date(timestampFromFilter($range), 'Y-m-d');
					$numberOfDays = daysAway($end_date);
				}
			}

			$repeat = "
			(
				(`repeats_every` IS NULL) 
				AND 
				(
					(`start_date` BETWEEN '" . $start_date . "' AND '" . $end_date . "') 
					OR 
					(
						(`end_date` IS NOT NULL) 
						AND 
						(
							(`end_date` BETWEEN '" . $start_date . "' AND '" . $end_date . "')
							OR
							(
								(`start_date` <= '" . $start_date . "') 
								AND 
								(`end_date` >= '" . $start_date . "')
							) 
							OR
							(
								(`start_date` <= '" . $end_date . "') 
								AND 
								(`end_date` >= '" . $end_date . "')
							) 
						)
					)
				)
			) 
			OR 
			(
				(`repeats_every` IS NOT NULL) 
				AND 
				(
					(
						(`ends_on` IS NULL) 
						OR 
						(`ends_on` >= '" . $start_date . "')
					)
				)
			)";
			
			$criteria[] = $repeat;
			
		} else {
			
			$start_date = TODAY;
			$end_date = format_date(timestampFromFilter('30-days'), 'Y-m-d');
			$numberOfDays = 30;

			$repeat = "
			(
				(`repeats_every` IS NULL) 
				AND 
				(
					(`start_date` >= '" . $start_date . "') 
					OR
					(
						(`end_date` IS NOT NULL)
						AND
						(`end_date` >= '" . $start_date . "')
					)
				)
			) 
			OR 
			(
				(`repeats_every` IS NOT NULL) 
				AND 
				(
						(`ends_on` IS NULL) 
						OR 
						(`ends_on` >= '" . $start_date . "')
				)
			)";
			
			$criteria[] = $repeat;
			
		}

		$sql = "SELECT `events`.*, CONCAT('events') AS `t` FROM `events` WHERE (" . join(") AND (", $criteria) . ") ORDER BY `events`.`start_date` ASC";
		$events = $db->get($sql);
		
		//dump($sql);
		//dump("Found: " . count($events));
		
		if (!is_admin()) {
			
			$eventDates = array();
			foreach ($events as $event) {
				$event = new event($event);
				$eventDates = array_merge($eventDates, $event->asDates($numberOfDays, $start_date));
			}

			$date = array();
			foreach ($eventDates as $key => $row) {
			    $date[$key]  = $row['_date'];
			}

			array_multisort($date, SORT_ASC, $eventDates);

			$events = array();
			foreach($eventDates as $eventID => $event) {
				$events[$eventID] = new event($event, $event['_date']);
			}
			
		}
		
		if (isset($terms['limit'])) {
			$events = array_max($events, $terms['limit']);
		}
		
		return $events;
	} // search_events
	
	function upcoming_events($numberToReturn = 5) {
		$events = search_events(array('limit' => $numberToReturn, 'date' => '14-days'));
		return $events;
	} // upcoming_events
	
	function event_comment_rss() {
		$item = get_page_var('item');
		// rss of a fullview item, means rss is of comments...
		$comments = array_max(array_reverse($item->comments()), 10);

		title($item->title());
		$rss['title'] = title('Comments');
		$rss['link'] = 'http://' . $_SERVER['SERVER_NAME'] . $item->link() . "#comments";
		$rss['description'] = "Comments for '" . $item->title() . "'";
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
	} // event_comment_rss
	
	
	function event_rss() {
		//$items = get_page_var('items');
		$items = array_max(get_page_var('items'), 10);
		
		$rss['title'] = title();
		$rss['link'] = 'http://' . $_SERVER['SERVER_NAME'] . section_link();
		$rss['description'] = "The latest events from Rochester City Newspaper";
		$rss['hasComments'] = true;

		// get items
		$rss['items'] = array();

		foreach ($items as $item) :
			$itemRSS = array();
			$itemRSS['title'] = format_date($item->_date, 'Y-m-d') . " - " . $item->title();
			$itemRSS['link'] = 'http://' . $_SERVER['SERVER_NAME'] . $item->link();
			$itemRSS['creator'] = 'Rochester City Newspaper';
			$itemRSS['pubDate'] = $item->published_at;
			$itemRSS['content'] = $item->body();
			$rss['items'][] = $itemRSS;
		endforeach;
			
		set_page_var('rss', $rss);

		site_rss();
	} // event_rss
	
	function calendar($which = false) {
		if (!$which)
			return get_var('calendar');
		else
			return get_var('calendar_' . $which);
	} // calendar
	
	function recent_events($numberToReturn = 5) {
		global $db;
		
		$root = get_sitemap_root();
		$subsections = array($root['id']);
		$sections = $root['subsections'];
		while (count($sections) > 0) {
			$tempsections = array();
			foreach ($sections as $section) {
				$subsections[] = $section['id'];
				$tempsections = array_merge($tempsections,$section['subsections']);
			}
			$sections = $tempsections;
		}

		$sql = "SELECT * FROM `events` WHERE (`deleted_at` IS NULL) AND (`published_at` IS NOT NULL) AND (`events`.`sitemap_id` = " . join(") OR (`events`.`sitemap_id` = ", $subsections) . ") ORDER BY `published_at` DESC LIMIT " . $db->escape($numberToReturn);
		$events = $db->get($sql);
		
		return objectize('event', $events);
	} // recent_events
	
	function events_browse_title() {
		global $db;
		
		$cats = get('categories');
		
		$title = 'Upcoming ';
		if (is_array($cats) && isset($cats[0]) && !empty($cats[0])) {
			$title .= valid(join(" & ", flatten($db->get('table=>event_categories', "where=>(`id` = " . join(") OR (`id` = ", $cats) . ")", 'return=>name'), 'name'))) . ' ';
		}
		$title .= 'Events';
		if (($location = get('location')) && is_numeric($location)) {
			$title .= ' at ' . valid($db->getProperty('table=>directories', 'id=>' . $db->escape($location), 'property=>name'));
		}
		
		if ($date = get('date')) {
			$title .= ' for '; 
			if (strpos($date, "-") == 4) {
				$title .= format_date(get_var('calendar'), ((get_var('calendar_day')) ? 'F jS, Y' : 'F, Y'));
			} else if (strpos($date, 'days') !== false) {
				$title .= 'the Next ' . capitalize(str_replace("-", " ", $date), true);
			} else {
				$title .= capitalize($date);
			}
		}
		
		return $title;
	} // events_browse_title
	
	function get_event_venue($event_venue_id) {
		global $db;
		
		if (!is_numeric($event_venue_id))
			return false;
			
		$venue = $db->getOne(sprintf("SELECT * FROM `event_venues` WHERE (`id` = %s) AND (`deleted_at` IS NULL)", $event_venue_id));
		
		return $venue;
	} // get_event_venue
	
	function get_event_locations($section = false) {
		global $db;
		
		if (!is_numeric($section))
			$section = get_var('id');
		/*load_plugin('directories');
		if ((!$locations = get_directories_by_type('venues')) || (count($locations) < 1)) {
			$locations = get_directories_by_type();
		}*/
		$locations = $db->get(sprintf("SELECT * FROM `event_venues` WHERE ((`sitemap_id` = %s) OR (`sitemap_id` = 0)) AND (`deleted_at` IS NULL)", $section));
		return $locations;
	} // get_event_locations

?>