<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/media-upload.php
	**	Creation Date: 6/27/08
	**	Description: Uploads a gallery photo image file.
	**	Called From: admin/Flint/includes/galleryphoto-upload.php
	**
	**********************************************
	*/
	clear_field_values();
	clear_required_fields();
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'media';
	include(LIBRARY . 'handlePostVars.php');
	
	// parameters required: photo (the actual photo file being uploaded) and filename (new name of the file)
	// need to get temp image, resize to max width of 150 or max height of 150 and then crop to 150x150
	// move to images/upload/ folder...
	
	if (isset($_FILES['photo'])) {
		$file = $_FILES['photo'];
		$ftmp = $_FILES['photo']['tmp_name'];
		$filename = $media['table_id'] . '-' . str_replace(' ', '-', basename($_FILES['photo']['name']));
		//$uploaddir = chdir("../images/uploads");
		$uploadfile = upload_folder($media['table'], true) . strip_ext($filename);

		if ($_FILES['photo']['type'] == 'image/gif') {
			$fext = ".gif";
		} else if (($_FILES['photo']['type'] == 'image/jpeg') || ($_FILES['photo']['type'] == 'image/pjpeg')) {
			$fext = ".jpg";
		} else if ($_FILES['photo']['type'] == 'image/png') {
			$fext = ".png";
		} else {
			failure("We do not accept that type of file. Sorry. Please stick to images (gif, jpg or png).");
			redirect_failure();
		}

		if (!($file_url = upload_media($file, $uploadfile . $fext))) {
		//if (upload($_FILES['photo'], $uploadfile . $fext)) {
			
			$media['location'] = $file_url;
			//$media['location'] = $uploadfile . $fext;
			if ($db->insert('table=>media', $media)) {

				success("Your file has been successfully uploaded.");
				redirect_failure();
				
			} else {
			
				failure("There was an error saving your file to the database.");
				redirect_failure();
				
			}
			
		}
		
	} else {
		
		failure("There was an error uploading your file, because we could not locate the file you supplied.");
		redirect_failure();
		
	}

?>