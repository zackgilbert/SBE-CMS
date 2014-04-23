<?php
	
	define("MINIMUM_PHP_VERSION", '4.3.0');
	define("PREFERRED_PHP_VERSION", '5.1.0');
	
	$loaded_extensions = get_loaded_extensions();
	$required_extensions = array('mysql');
	$preferred_extensions = array('mhash','curl', 'memcache', 'gd');

	function is_apache() {
		return ((strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) || (strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false));
	} // is_apache
	
	function loaded_extensions() {
		return get_loaded_extensions();
	} // loaded_extensions
	
	function required_extensions() {
		global $required_extensions;
		return $required_extensions;
	} // required_extensions
	
	function preferred_extensions() {
		global $preferred_extensions;
		return $preferred_extensions;
	} // preferred_extensions

	function server_meets_minimum_requirements() {
		global $flint_errors;
		if (!is_array($flint_errors)) $flint_errors = array();
		//global $is_apache;
		if (is_apache()/*$is_apache*/ && version_compare(PHP_VERSION, MINIMUM_PHP_VERSION, ">") && !get_server_errors()) {
			return true;
		}
		return false;
	} // server_meets_minimum_requirements

	function server_meets_preferred_requirements() {
		if (server_meets_minimum_requirements() && version_compare(PHP_VERSION, PREFERRED_PHP_VERSION, ">=") && !get_server_warnings()) {
			return true;
		}
		return false;
	} // server_meets_preferred_requirements
	
	function get_server_errors() {
		global $flint_errors;
		if (!is_array($flint_errors)) $flint_errors = array();
		
		foreach (required_extensions() as $ext) {
			if (!in_array($ext, loaded_extensions())) {
				$flint_errors[] = "Extension (" . $ext . ") was not found on this server.";
			}
		}
		
		if (count($flint_errors) > 0) {
			return $flint_errors;
		}
		return false;
	} // get_server_errors
	
	function get_server_warnings() {
		global $flint_errors;
		if (!is_array($flint_errors)) $flint_errors = array();
		
		foreach (preferred_extensions() as $ext) {
			if (!in_array($ext, loaded_extensions())) {
				$flint_errors[] = "Extension (" . $ext . ") was not found on this server.";
			}
		}
		
		if (count($flint_errors) > 0) {
			return $flint_errors;
		}
		return false;
	} // get_server_warnings
	
?>