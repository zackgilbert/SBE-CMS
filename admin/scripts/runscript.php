<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/runscript.php
	**	Creation Date: 9/1/08
	**	Description: Random script used to run admin maintence scripts.
	**
	**********************************************
	*/
	
	/***** DO NOT LEAVE EXECUTABLE ONCE FINISHED!!! *****/

	if (request('command') == 'maintenance') {
		
		$regions = $db->get('table=>winery_regions');
		foreach ($regions as $key => $region) {
			//unset($regions[$key]);
			$regions[$region['id']] = $region;
		}
		
		dump($regions);
		
		$region_ids = $db->get('table=>directory_type_wineries', 'return=>id,regions');
		
		foreach ($region_ids as $region_id) {
		
			if (isset($region_id['regions']) && is_numeric($region_id['regions']) && isset($regions[$region_id['regions']])) {
				
				$db->update('table=>directory_type_wineries', 'id=>' . $region_id['id'], array('regions' => $regions[$region_id['regions']]['name']));
				dump($region_id['id']);
				
			}
			
		}
		
	} else {
		
		echo "Invalid script call...";
		
	}

?>