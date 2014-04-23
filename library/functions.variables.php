<?php
	
	function clear_field_values() {
		unset_session_var('postFields');
	} // clear_field_values
	
	function clear_required_fields() {
		unset_session_var('requiredFields');
	} // clear_required_fields

	function get($name = false, $default = false) {
		if (isset($_GET[$name])) {
			return $_GET[$name];
		}
		return $default;
	} // get

	function get_field_value($field) {
		$fields = get_field_values();
		if (isset($fields[$field])) {
			return $fields[$field];
		} else if (strpos($field, "[") !== false) {
			$temp = trim_explode("[", $field,"]");
			$tempArray = $fields;
			for($i=0; $i<count($temp); $i++) {
				if (isset($tempArray[$temp[$i]])) {
					$tempArray = $tempArray[$temp[$i]];
				} else {
					return false;
				}
			}
			return $tempArray;
		}
		return false;
	} // get_field_value
		
	function get_field_values() {
		$fields = get_page_var('postFields');
		if (is_array($fields)) {
			return $fields;
		}
		return array();
	} // get_field_values
	
	function get_page_var($varName) {
		$page_vars = get_page_vars();
		if (isset($page_vars[$varName])) {
			return $page_vars[$varName];
		} else if (strpos($varName, "[") !== false) {
			$temp = trim_explode("[", $varName,"]");
			$tempArray = $page_vars;
			for($i=0; $i<count($temp); $i++) {
				if (isset($tempArray[$temp[$i]])) {
					$tempArray = $tempArray[$temp[$i]];
				} else {
					return false;
				}
			}
			return $tempArray;
		}
		return false;
	} // get_page_var
	
	function get_page_vars() {
		$page_vars = get_var('page_vars');
		if (is_array($page_vars))
			return $page_vars;
		return array();
	} // get_page_vars
	
	function get_required_fields() {
		$required = get_page_var('requiredFields');
		if (is_array($required)) {
			return $required;
		}
		return array();
	} // get_required_fields
	
	function get_session_var($varName) {
		if (isset($_SESSION[$varName]))
			return $_SESSION[$varName];
		return false;
	} // get_session_var
	
	function get_var($varName = false) {
		if (!isset($GLOBALS['flint'])) $GLOBALS['flint'] = array('page_vars' => array());
		$flint = $GLOBALS['flint'];
		
		if (isset($flint[$varName])) {
			return $flint[$varName];
		} else if (strpos($varName, "[") !== false) {
			$temp = trim_explode("[", $varName,"]");
			$tempArray = $flint;
			for($i=0; $i<count($temp); $i++) {
				if (isset($tempArray[$temp[$i]])) {
					$tempArray = $tempArray[$temp[$i]];
				} else {
					return false;
				}
			}
			return $tempArray;
		}

		return false;
	} // get_var
	
	function get_vars() {
		if (!isset($GLOBALS['flint'])) $GLOBALS['flint'] = array('page_vars' => array());		
		return $GLOBALS['flint'];
	} // get_vars
	
	function post($name = false, $default = false) {
		if (isset($_POST[$name])) {
			return $_POST[$name];
		}
		return $default;
	} // post
	
	function request($name = false, $default = false) {
		if (isset($_REQUEST[$name])) {
			return $_REQUEST[$name];
		}
		return $default;		
	} // request
	
	function set_page_var($varName, $varValue = false) {
		$page_vars = get_page_vars();
		if (strpos($varName, "[") !== false) {
			$temp = trim_explode("[", $varName,"]");
			$tempArray[$temp[count($temp)-1]] = $varValue;
			for($i=count($temp)-2; $i>0; $i--) {
				$newtemp = $tempArray;
				unset($tempArray);
				$tempArray = array();
				$tempArray[$temp[$i]] = $newtemp;
			}
			return $GLOBALS['flint']['page_vars'][$temp[0]] = $tempArray;
		}
		return $GLOBALS['flint']['page_vars'][$varName] = $varValue;
	} // set_page_var
	
	function set_session_var($varName, $newValue = false) {
		$_SESSION[$varName] = $newValue;
	} // set_session_var
	
	function set_var($varName, $newValue) {
		if (!isset($GLOBALS['flint'])) $GLOBALS['flint'] = array('page_vars' => array());
		$GLOBALS['flint'][$varName] = $newValue;
	} // set_var
	
	function set_vars($newVar, $overwrite = false) {
		if ($overwrite || !is_array($newVar))
			unset_vars();
		
		if (!isset($GLOBALS['flint'])) 
			$GLOBALS['flint'] = array();
		
		if (is_array($newVar))
			foreach ($newVar as $varName => $varValue)
				$GLOBALS['flint'][$varName] = $varValue;
	} // set_vars
		
	function unset_session_var($varName) {
		$_SESSION[$varName] = NULL;
		unset($_SESSION[$varName]);
	} // unset_session_var
	
	function unset_vars() {
		$GLOBALS['flint'] = NULL;
		unset($GLOBALS['flint']);
	} // unset_vars

?>