<?php

	function site() {
		
		// always know what the url that accessed this page is by setting the current_url...
		set_var('current_url', get_var('params'));
		
		// set levels according to sitemap...
		set_sitemap_levels(get_var('params'));
		
		$level = get_var('level');
		
		if (!is_array($level)) {
			load_server_page('sitemap-setup');
			exit;
		}
			
		// set template (if empty, then false, if 'DEFAULT' then use DEFAULT_TEMPLATE)
		if (empty($level['template'])) :
			set_var('template', false);
		elseif ($level['template'] == 'DEFAULT') :
			set_var('template', DEFAULT_TEMPLATE);
		else :
			set_var('template', $level['template']);
		endif;
		
		if (!empty($level['url']))
			title($level['name']);		
		
		if (function_exists('page_type_' . $level['type'])) {
			return call_user_func('page_type_' . $level['type']);
		}
		
		if (plugin_is_page_type($level['type'])) {
			if (plugin_is_installed($level['type'])) {
				load_plugin($level['type']);
				if (function_exists('page_type_' . $level['type'])) {
					return call_user_func('page_type_' . $level['type']);
				}
			} else {
				load_server_page('plugins-install', $level['type']);
				exit;
			}
		} else {
			load_server_page('plugins-404', $level['type']);
			exit;
		}
	} // site
	
	function page_type_static() {
		set_var('page', get_prefix());
	} // page_type_static

	function get_logo($default = 'images/logo-seen-bizessen.jpg') {
		global $db;
		
		$logo = $db->getProperty('table=>media', "where=>(`type` = 'logo') AND (`deleted_at` IS NULL)", 'order=>created_at DESC', 'property=>location');
		return (is_string($logo)) ? $logo : $default;
	} // logo
	
	function get_stats($method, $extras = '') {
		// this token is used to authenticate your API request.
		// You can get the token on the API page inside your Piwik interface
		$token_auth = 'anonymous';
		$server = (SERVER == 'localhost') ? SERVER . ":8888" : SERVER;
		// we call the REST API and request the 100 first keywords for the last month for the idsite=1
		$url = "http://" . $server . LOCATION . "piwik/index.php";
		$url .= ("?module=API&method=" . $method);
		$url .= ("&idSite=1&" . trim($extras, "&"));
		$url .= "&format=PHP";
		$url .= "&token_auth=$token_auth";
		//http://piwik.org/demo/?module=API&method=VisitTime.getVisitInformationPerLocalTime&idSite=1&period=day&date=today&format=PHP&prettyDisplay=true&token_auth=anonymous
		$content = $fetched = get_file($url);
		
		if (is_string($content) && (strpos($content, "Piwik &rsaquo; Error") === false))
			$content = unserialize($content);
		
		// case error
		if (!is_array($content)) {
		    print("Error, content fetched = ".$fetched);
			die();
		}
		return $content;
	} // get_stats
	
	function format_time($difference = '0', $unit = 'seconds', $largest = 'years') {
		$unit = singularize($unit);
		$largest = singularize($largest);
		
		$periods = array("second", "minute", "hour", "day", "week", "month", "year");
		$short = array('sec', 'min', 'hr', 'day', 'wk', 'month', 'year');
		$lengths = array("60","60","24","7","4.35","12");
		
		foreach ($periods as $k => $period)
			if ($unit == $period)
				break;
		
		for($j = $k; ($difference >= $lengths[$j]) && ($largest != $periods[$j]); $j++) {
			$extra = $difference%$lengths[$j];
			$difference = ($difference-$extra) / $lengths[$j];
		}

		if ($difference != 1) {
			$periods[$j] .= "s";
			$short[$j] .= 's';
		}
		
		if (($j>0) && ($extra != 1)) {
			$periods[$j-1] .= "s";
			$short[$j-1] .= 's';
		}
		
		$text = $difference . " " . $periods[$j];
		if (isset($periods[$j-1]) && ($extra > 0))
			$text .= ", " . $extra . " " . $periods[$j-1];		
		return str_replace($periods, $short, $text);
	} // format_time

?>