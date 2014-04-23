<?php

	$postFields = array();
	$requiredFields = (isset($_POST['required'])) ? trim_explode(",", $_POST['required']) : array();
	global $missingFields;
	$missingFields = array();
	
	function handleRequired($required, $array, $string = '') {
		global $missingFields;
		$fields = array();
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$fields[$k] = handleRequired($required, $v, $k.'[');
			} else {
				$fields[$k] = $v;
				$k2 = (substr($string, -1) == '[') ? $string . $k . ']' : $k ;
				if (in_array($k2, $required) && empty($v)) {
					$missingFields[] = $k2;
				}				
			}
		}
		return $fields;
	}
	
	$postFields = handleRequired($requiredFields, $_POST);
	
	if (count($missingFields) > 0) {
		$_SESSION['postFields'] = $postFields;
		$_SESSION['requiredFields'] = $missingFields;
		failure("Required information appears to have been left blank. Required fields are highlighted.");
		redirect_failure();
	}

?>