<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/galleries-deletephoto.php
	**	Creation Date: 6/30/08
	**	Description: Deletes a photo from a gallery album
	**	Called From: admin/Flint/includes/galleries-edit.php
	**
	**********************************************
	*/
	
	if (is_ajax() && request('photo_id')) {
		
		$photo_id = request('photo_id');
		
		if ($db->delete('table=>gallery_photos', 'id=>' . $db->escape($photo_id))) {
			
			$media = $db->getOne('table=>media', "where=>(`table` = 'gallery_photos') AND (`table_id` = " . $db->escape($photo_id) . ") AND (`type` = 'photo')");
			
			$photo_path = get_path_from_url($media['location']);
			unset($photo_path);
			$db->delete('table=>media', 'id=>' . $media['id']);
			
			echo 'true';
			
		} else {
			
			echo "There was an error trying to delete the photo from the database.";
			
		}
		
	} else {
		
		echo "Invalid script call. Missing requires information.";
		
	}

?>