<?php

	/*
	**********************************************
	**
	**	File: plugins/bands/admin/scripts/bands-save.php
	**	Creation Date: 9/29/09
	**	Description: Saves bands to database
	**	Called From: plugins/bands/admin/pages/bands-edit.php
	**
	**********************************************
	*/
	clear_field_values();
	clear_required_fields();
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'bands';
	include(LIBRARY . 'handlePostVars.php');

	// specifically: url and excerpt
	if (!isset($bands['url'])) {
		if (!empty($bands['name'])) {
			$bands['url'] = url_friendly($bands['name']);
		}
	} 
	
	if (empty($bands['deleted_at'])) {
		$bands['deleted_at'] = NULL;
	}
	
	if (empty($bands['published_at'])) {
		$bands['published_at'] = NULL;
	}
	
	if (isset($_POST['publish']) || isset($_POST['publish-continue'])) {
		$bands['published_at'] = NOW;
	}
	
	// Clean up blank entries... lets db handle defaults
	foreach ($bands as $key => $value)
		if (empty($value)) unset($bands[$key]);
	
	if ($db->save('table=>' . $table, $bands)) {
		if (!isset($bands['id']))
			$bands['id'] = $db->last_id;
			
		$mediaErrors = array();
		
		$table = 'media';
		include(LIBRARY . 'handlePostVars.php');
		
		fix_files_superglobal();
		
		if (isset($_FILES['photo']) && is_array($_FILES['photo']) && isset($_FILES['photo']['tmp_name']) && !empty($_FILES['photo']['tmp_name'])) {
			$photo = $_FILES['photo'];
			$ftmp = $photo['tmp_name'];
			$uploadfile = upload_folder('bands', true) . $bands['id'];

			if ($photo['type'] == 'image/gif') {
				$fext = ".gif";
			} else if (($photo['type'] == 'image/jpeg') || ($photo['type'] == 'image/pjpeg')) {
				$fext = ".jpg";
			} else if ($photo['type'] == 'image/png') {
				$fext = ".png";
			} else {
				$mediaErrors[] = array('media-thumb' => "There was an error uploading this band's logo/thumbnail file. We only accept images with the file types: gif, jpg or png");	
			}

			//upload($photo, $uploadfile . $fext);
			//create_image($uploadfile, true, 100, 100, '1:1');
			if (isset($fext) && is_string($fext)) {
				upload_media($photo, $uploadfile . $fext, 'BAND_THUMB');
				$db->update('table=>bands', 'id=>' . $bands['id'], array('thumb' => ''));
			}
			
		}
		
		$photos = (isset($media['photo']) && is_array($media['photo'])) ? $media['photo'] : array();
		$photoUploads = (isset($_FILES['media']['photo']) && is_array($_FILES['media']['photo'])) ? $_FILES['media']['photo'] : array();
		
		foreach ($photos as $photoKey => $photo) {
			
			$file = $photoUploads[$photoKey]['file'];
			
			if (!empty($photo['title']) && (isset($file['tmp_name']) && isset($file['name']) && !empty($file['tmp_name']) && !empty($file['name']))) {
			
				// upload photo
				$ftmp = $file['tmp_name'];
				$uploadfile = upload_folder('bands', true) . $bands['id'] . '-photo-' . str_replace(' ', '-', $file['name']);
				
				if ($file['type'] == 'image/gif') {
					$fext = ".gif";
				} else if (($file['type'] == 'image/jpeg') || ($file['type'] == 'image/pjpeg')) {
					$fext = ".jpg";
				} else if ($file['type'] == 'image/png') {
					$fext = ".png";
				}

				if (!isset($fext) || !is_string($fext)) {
					$mediaErrors[] = array('media-photo' => "There was an error uploading this band's photo file. We only accept images with the file types: gif, jpg or png");
				} else if (!($file_url = upload_media($file, $uploadfile))) {
				//if (!upload($file, $uploadfile)) {
				
					// error uploading file
					$mediaErrors[] = array("media-photo" => "There was an error uploading this band's photo file to server.");

				} else {
					
					// get photo fields and save to database
					$photo['table'] = 'bands';
					$photo['table_id'] = $bands['id'];
					$photo['sitemap_id'] = $bands['sitemap_id'];
					$photo['type'] = 'photo';
					$photo['location'] = $file_url;

					if (!$db->save('table=>media', $photo)) {

						$mediaErrors[] = array("media-photo" => "There was an error saving this band's photo to the database.");

					}
			
				}
				
			} else {
				// ERRORS!!!
				if ($file['error'] === "1") {
					
					$mediaErrors[] = array('media-photo' => "The photo file you are trying to upload was too big. Try a smaller file (under 5mb).");
					
				} else if (isset($file['name']) && !empty($file['name'])) {
					
					$mediaErrors[] = array('media-photo' => "There was an error uploading the photo file " . $file['name']);
					
				}
				
			}

		}
		
		$videos = (isset($media['video']) && is_array($media['video'])) ? $media['video'] : array();
		
		foreach ($videos as $videoKey => $video) {

			if (!empty($video['location']) && !empty($video['title'])) {
			
				// get video fields and save to database
				$video['table'] = 'bands';
				$video['table_id'] = $bands['id'];
				$video['sitemap_id'] = $bands['sitemap_id'];
				$video['type'] = 'video';
				
				if (!$db->save('table=>media', $video)) {

					$mediaErrors[] = array("media-video" => "There was an error saving this band's video to the database.");

				}
			
			}

		}
		
		$audios = (isset($media['audio']) && is_array($media['audio'])) ? $media['audio'] : array();
		$audioUploads = (isset($_FILES['media']['audio']) && is_array($_FILES['media']['audio'])) ? $_FILES['media']['audio'] : array();
		
		foreach ($audios as $audioKey => $audio) {
			
			$file = $audioUploads[$audioKey]['file'];
			
			if (!empty($audio['title']) && (isset($file['tmp_name']) && isset($file['name']) && !empty($file['tmp_name']) && !empty($file['name']))) {
			
				// upload audio
				$ftmp = $file['tmp_name'];
				$uploadfile = upload_folder('bands', true) . $bands['id'] . '-audio-' . str_replace(' ', '-', $file['name']);

				if (!($file_url = upload_media($file, $uploadfile))) {
				//if (!upload($file, $uploadfile)) {
				
					// error uploading file
					$mediaErrors[] = array("media-audio" => "There was an error uploading this band's audio file to server.");

				} else {
					
					// get audio fields and save to database
					$audio['table'] = 'bands';
					$audio['table_id'] = $bands['id'];
					$audio['sitemap_id'] = $bands['sitemap_id'];
					$audio['type'] = 'audio';
					$audio['location'] = $file_url;

					if (!$db->save('table=>media', $audio)) {

						$mediaErrors[] = array("media-audio" => "There was an error saving this band's audio to the database.");

					}
			
				}
				
			} else {
				// ERRORS!!!
				if ($file['error'] === "1") {
					
					$mediaErrors[] = array('media-audio' => "The audio file you are trying to upload was too big. Try a smaller file.");
					
				} else if (isset($file['name']) && !empty($file['name'])) {
					
					$mediaErrors[] = array('media-audio' => "There was an error uploading the audio file " . $file['name']);
					
				}
				
			}

		}
		
		//echo "Media Errors: ";
		//dump($mediaErrors);

		if (count($mediaErrors) == 0) {
			
			flush_memcache();

			success('Band was successfully saved.');
			
			if (isset($_POST['continue']) || isset($_POST['publish-continue'])) {
				if (substr($_POST['redirect']['failure'], -5) == '/add/') {
					redirect(substr($_POST['redirect']['failure'], 0, -5) . "/" . $bands['id'] . "/");
				} else {
					redirect_failure();
				}
			} else {
				
				redirect_success();
	
			}
			
		} else {
			
			if (count($mediaErrors) == 1) {
				
				failure(current($mediaErrors[0]));
				
			} else {
				
				failure("There were a number of errors uploading media for this band.");
				
			}
			
			if (isset($bands['id']) && substr($_POST['redirect']['failure'], -5) == '/add/') {
				redirect(substr($_POST['redirect']['failure'], 0, -5) . "/" . $bands['id'] . "/");
			} else {
				$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
				redirect_failure();
			}
			
		}
		
	} else {
		
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error saving this band.');
		redirect_failure();

	}

?>