<?php

	$$table = array();	
	$temp = $$table;
	
	if (isset($_POST[$table]) && is_array($_POST[$table])) {
		foreach ($_POST[$table] as $key => $value) {		
			if (!is_array($value)) {
				// if the field is an id/int
				if(substr($key, -3) == "_id") {
					if (($value != "") && ($value != "--")) {
						if ($db->isTable(pluralize(substr($key,0,-3)))) {
							$searchItem = $db->getOne("table=>".pluralize(substr($key,0,-3)),"id=>".$db->escape($value), "return=>id");
							if ($searchItem) {
								$temp[$key] = $searchItem['id'];
							} else {
								if ($db->insert("table=>".pluralize(substr($key,0,-3)), array("fields" => array('name' => $db->escape($value))))) {
									$temp[$key] = $db->last_id;
								}
							}
						} else {
							$temp[$key] = $value;
						}
					} else {
						$temp[$key] = NULL;
					}
				// if the field is a datetime
				} else if (substr($key,-3) == "_at") {
					if ($value == "true") {
						$temp[$key] = NOW;
					} else if ($value == 'false') {
						$temp[$key] = NULL;
					} else {
						$temp[$key] = $db->escape($value);
					}
				} else {
					$temp[$key] = ($value != '--') ? convert_smart_quotes(clean($value)) : NULL;//$db->escape(convert_smart_quotes($value)) : NULL;
				}
			// is an array == HABTM
			} else {
				if ((substr($key, -3) == "_on") && (count($value) == 3) && isset($value['year']) && isset($value['month']) && isset($value['day'])) {
					$temp[$key] = $value['year'] . "-" . $value['month'] . "-" . $value['day'];
				} else {
					//$temp[$key] = join(", ", $value);
					//$temp[$key] = $value;	
					$temp[$key] = $value;
				}
			}
		}
	}
	
	$$table = $temp;

?>