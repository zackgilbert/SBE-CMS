<?php

	function get_route_vars($q = false) {
		global $urlParts,$route;

		$currentURL = ($q) ? $q : $_GET['flinturl'];
		
		if ($memcached_url = get_memcache('route_vars:' . $currentURL)) {
			return $memcached_url;
		}
		
		$urlParts = explode("/", trim($currentURL, "/"));
		$routeVariations = get_route_variations($urlParts);
		
		$route = get_current_route($routeVariations);
		
		// Need to get the variables for the current route.
		$routeVariables = get_route_variables($urlParts, $route);
		// Redirect to new page if needed -- NOT SURE IF THIS IS THE RIGHT PLACE FOR THIS
		if (isset($routeVariables['redirect'])) {
			$r = ((substr($routeVariables['redirect'], 0, 1) === '/') ? '' : get_location()) . $routeVariables['redirect'];
			redirect($r);
			//redirect(LOCATION . str_replace("_", "-", $routeVariables['redirect']));	
		}
		
		$defaults = (isset($routeVariables['defaults'])) ? $routeVariables['defaults'] : 'default';
		$vars = $routeDefaults = get_route_defaults($urlParts, $defaults);
		set_vars(array('origs' => $routeDefaults));

		foreach ($routeVariables as $varName => $varValue) {
			if (isset($vars[$varName]) && (in_array($varName, array('stylesheets', 'javascripts', 'plugins')) || is_array($varValue))) {
				// merge arrays (defaults first)
				$vars[$varName] = array_merge($vars[$varName], $varValue);
			} else {
				// just set/overwrite variable
				$vars[$varName] = $varValue;
			}
		}
		
		set_memcache('route_vars:' . $currentURL, $vars);
		return $vars;		
	} // get_route_vars
	
	function get_current_route($routeVariations) {
		foreach (get_routes() as $routeKey => $routeValue) {
			if (in_array($routeKey, $routeVariations))
				return $routeKey;
		}
	} // get_current_route
	
	function get_route_variations($urlParts = array()) {
		return array_unique(get_route_variations_recursive(array(join("/", $urlParts)), $urlParts, 0));
	} // get_route_variations
	
	function get_route_variations_recursive($routeVariations, $currentRoute, $startAt = 0) {
		$routeVariations[] = join("/", $currentRoute);
		for ($i=$startAt+1; $i<count($currentRoute); $i++) {
			$routeVariations = get_route_variations_recursive($routeVariations, $currentRoute, $i);
		}

		if (isset($currentRoute[$startAt-1])) {
			$tempRoute = $currentRoute;
			$tempRoute[$startAt-1] = "**";
			$routeVariations[] = join("/", $tempRoute);
		}
		
		$currentRoute[$startAt] = "*";
		$routeVariations[] = join("/", $currentRoute);
		for ($i=$startAt+1; $i<count($currentRoute); $i++) {
			$routeVariations = get_route_variations_recursive($routeVariations, $currentRoute, $i);
		}
		
		$currentRoute[$startAt] = "**";
		$catchAll = join("/", $currentRoute);
		if (strpos($catchAll, "**/") === false) {
			$routeVariations[] = $catchAll;
		}
		
		while ((substr($catchAll, -5) == "**/**")) {
			$routeVariations[] = $catchAll = str_replace("**/**", "**", $catchAll);
		}
		
		while ((substr($catchAll, -4) == "*/**")) {
			$routeVariations[] = $catchAll = str_replace("*/**", "**", $catchAll);
		}
		
		return $routeVariations;
	} // get_route_variation
	
	function get_route_defaults($urlParts, $routeDefaults = 'default') {
		if (!is_string($routeDefaults)) $routeDefaults = 'default';
		
		// Load defaults, if there are any... SHOULD DEFAULTS NOT BE GLOBALS?
		if (is_string($routeDefaults) && !empty($routeDefaults) && isset($GLOBALS['defaults'][$routeDefaults])) {
			return set_dynamic_variables($urlParts, $GLOBALS['defaults'][$routeDefaults]);
		}
		
		return array();
	} // get_route_defaults
	
	function get_route_variables($urlParts, $route) {
		$routeVariables = $origRouteVariables = get_routes($route);
		$routeVariables = set_dynamic_variables($urlParts, $routeVariables);
		
		return $routeVariables;
	} // get_route_variables
	
	function get_routes($route = false) {
		global $routes;
		
		if (is_string($route) && isset($routes[$route]))
			return $routes[$route];
		
		return $routes;
	} // get_routes
	
	function set_dynamic_variables($urlParts, $routeVariables) {
		global $route;
		
		$routeParts = explode("/", $route);
		
		foreach ($routeVariables as $varName => $varValue) {
			// if there are any special dynamic values in the variable value...
			while (is_string($varValue) && strpos($varValue, "{") !== false) {
				// find out about string positions to get the dynamic value...
				$replaceStart = strpos($varValue, "{");
				$replaceEnd = strpos($varValue, "}", $replaceStart+1);
				
				// get the position in the route array...
				$routeNum = substr($varValue, $replaceStart+1, $replaceEnd-($replaceStart+1));
				
				// get any text that comes before the dynamic value
				$preRoute = substr($varValue, 0, $replaceStart);
				// get any text that comes after the dynamic value
				$postRoute = substr($varValue, $replaceEnd+1);
				
				if ($routeNum == 'all') {
					$varValue = $preRoute . join('/', $urlParts) . $postRoute;					
				} else {
					if (!isset($urlParts[$routeNum-1])) $log->log('Error in routes (/config/routes.php): ' . var_export($route, true) . "<br />Route Index Not Found In Value: $routeNum");
					
					$intraRoute = $urlParts[$routeNum-1];
					if ($routeParts[$routeNum-1] == '**') {
						for ($i=$routeNum; $i<count($urlParts); $i++) {
							$intraRoute .= "/" . $urlParts[$i];
						}
					}
					
					// replace the current dynamic value in the variable value with the new value.
					$varValue = $preRoute . $intraRoute . $postRoute;					
				}
			}
			
			$routeVariables[$varName] = $varValue;
		}
		
		return $routeVariables;
	} // set_dynamic_variables
	
?>