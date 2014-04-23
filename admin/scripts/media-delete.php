<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/media-delete.php
	**	Creation Date: 4/26/08
	**	Description: Delete a media item from database and server
	**	Called From: admin/Flint/includes/articles-edit.php + many others
	**
	**********************************************
	*/
	
	// get info about what is to be deleted... (id of media record and type of media) 
	// must be ajax call, must be logged in and admin to use, must have valid info supplied...
	// if all is good, then go and grab the record from the database
	// get the location and find file... 
	// then delete it from database and delete it from server
	
	if (is_ajax() && is_logged_in() && is_admin() && isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
		
		$id = $db->escape($_REQUEST['id']);
		
		// grab media item's record from database
		$media = $db->getOne('table=>media', 'where=>(`id` = ' . $id . ')');
		
		// if media item was found in database
		if ($media) {
			
			$deleted = false;

			// delete from database
			if (!is_string($media['deleted_at']) || empty($media['deleted_at'])) {
				
				$deleted = $db->delete('table=>media', 'id=>' . $media['id']);
				$db->update('table=>' . $media['table'], 'id=>' . $media['id'], array('thumb' => ''));
				
			} else {
				
				$deleted = true;
				
			}
			
			if ($deleted) {
			
				if (!empty($media['location']) && (strpos($media['location'], 'http://') === false)) {
				
					// delete file from server
					$file = get_presentation_file($media['location']);
					if ($file['found']) {

						$filename = search_template_file($file['path'] . $file['name'] . ".");

						if (is_string($filename) && !empty($filename)) {

							if (unlink($filename)) {

								$other_files = glob(strip_ext($filename) . '_*');
								if (is_array($other_files)) {
									foreach ($other_files as $f)
								   		unlink($f);
								}

							} else {
								
								unlink($file['path'] . $file['name'] . '.' . $file['ext']);
								
							}
							
						} else {
							
							unlink($file['path'] . $file['name'] . '.' . $file['ext']);
							
						}
						
					} else {
						
						warning("Attempting to remove file(" . $media['location'] . ") failed. Could not find file.", __FILE__, __FUNCTION__, __LINE__);
						
					}

				}
				
				echo 'true';

			} else {
			
				echo "There was an error deleting the media item from ";
				
			}
			
		} else {
			
			echo "Invalid ID provided. Could not find media item in database.";
			
		}
		
	} else {
		
		echo "Invalid script call. Missing required information";

	}

?>