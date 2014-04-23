<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/file-delete.php
	**	Creation Date: 4/26/08
	**	Description: Delete a file from server
	**	Called From: admin/Flint/includes/articles-edit.php + many others
	**
	**********************************************
	*/
	
	// get info about what is to be deleted... (file location/name)
	if (is_ajax() && is_logged_in() && is_admin() && request('file')) {
		
		$filename = $db->escape(request('file'));
	
		//$file = get_presentation_file($filename);
		$file = search_template_file(strip_ext($filename) . ".");
		
		if (is_string($file) && !empty($file)) {
			
			if (unlink($file)) {
				
				$other_files = glob(strip_ext($file) . '_*');
				if (is_array($other_files)) {
					foreach ($other_files as $f)
				   		unlink($f);
				}
				//foreach (glob(strip_ext($file)."_*") as $f)
				//	unlink($f);
				
				$thumb = request('thumb');
				if (is_string($thumb) && (strpos($thumb, ":") !== false)) {
					list($table, $id) = explode(":", $thumb);
					$db->update('table=>' . $db->escape($table), 'id=>' . $db->escape($id), array('thumb' => ''));
				}
				
				echo 'true';
				
			} else {
				
				warning("Attempting to remove file(" . $file . ") failed. Could not find file.", __FILE__, __FUNCTION__, __LINE__);
				
				echo "There was an error removing the file from the server.";
				
			}			
			
		} else {
			
			echo "There was an error. We could not find the file on the server.";			
			
		}

	} else {
		
		echo "Invalid script call. Required information was missing.";
		
	}

?>