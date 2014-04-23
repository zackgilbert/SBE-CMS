<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/galleries-save.php
	**	Creation Date: 6/29/08
	**	Description: Saves galleries to database and uploads gallery photos to server
	**	Called From: admin/Flint/includes/galleries-edit.php
	**
	**********************************************
	*/
	clear_field_values();
	clear_required_fields();
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'galleries';
	include(LIBRARY . 'handlePostVars.php');


	// Handle group specific adjustments to values...
	// specifically: url and excerpt
	if (!isset($galleries['url'])) {
		if (!empty($galleries['name'])) {
			$galleries['url'] = url_friendly(truncate($galleries['name'], 100));
		}
	} 
	
	if (isset($_POST['publish']) || isset($_POST['publish-continue'])) {
		$galleries['published_at'] = NOW;
	}
	
	// Clean up blank entries... lets db handle defaults
	foreach ($galleries as $key => $value)
		if (empty($value)) unset($galleries[$key]);
	
	//dump($galleries);
	//die();
	
	if ($db->save('table=>galleries', $galleries)) {
		if (!isset($galleries['id']))
			$galleries['id'] = $db->last_id;

		if (isset($_FILES['photo']) && is_array($_FILES['photo']) && isset($_FILES['photo']['tmp_name']) && !empty($_FILES['photo']['tmp_name'])) {
			$photo = $_FILES['photo'];
			$ftmp = $photo['tmp_name'];
			$uploadfile = upload_folder('galleries', true) . $galleries['id'];

			if ($photo['type'] == 'image/gif') {
				$fext = ".gif";
			} else if (($photo['type'] == 'image/jpeg') || ($photo['type'] == 'image/pjpeg')) {
				$fext = ".jpg";
			} else if ($photo['type'] == 'image/png') {
				$fext = ".png";
			} else {
				failure("The gallery's info was successfully saved, but we could not upload the photo you supplied. We do not accept that type of file for your gallery photos. Please stick to images (gif, jpg or png).");
				redirect_failure();
			}

			//upload($photo, $uploadfile . $fext);
			//create_image($uploadfile, true, 100, 100, '1:1');
			upload_media($photo, $uploadfile . $fext, 'GALLERY_THUMB');
			$db->update('table=>galleries', 'id=>' . $galleries['id'], array('thumb' => ''));
			
		}
		
		$upload_errors = false;
		
		if (isset($_POST['photos']) && isset($_FILES['photos']) && is_array($_FILES['photos']['name']) && (count($_FILES['photos']['name']) > 0)) {
			fix_files_superglobal();
			
			$album_id = (isset($_POST['photos']['album_id'])) ? $_POST['photos']['album_id'] : $galleries['id'];
			$sitemap_id = $_POST['photos']['sitemap_id'];
			
			foreach ($_FILES['photos'] as $key => $photo) {
				
				// if a file was actually selected and uploaded,
				if (!$upload_errors && !empty($photo['tmp_name']) && in_array(strtolower(get_ext($photo['name'])), array('gif', 'jpg', 'jpeg', 'png'))) {
					
					$ftmp = $photo['tmp_name'];
					$uploadfile = upload_folder('galleries', true) . $album_id . '-' . str_replace(' ', '-', strip_ext($photo['name'])) . '.' . get_ext($photo['name']);
					
					// move the tmp file to server					
					if (($file_url = upload_media($photo, $uploadfile, 'GALLERY_PHOTO_THUMB'))) {
					//if (upload($photo, $uploadfile)) {
						// get fields ready for db.
						$gallery_photo = array();
						$gallery_photo['gallery_id'] = $album_id;

						if ($db->insert('table=>gallery_photos', $gallery_photo)) {
							$gallery_photo_id = $db->last_id;
							
							// create other versions
							//create_image($uploadfile, true, 60, 60, '1:1');
							//create_image($uploadfile, true, 150, 150);
							//create_image($uploadfile, true, 608);
							
							$media = array();
							$media['table'] = 'gallery_photos';
							$media['table_id'] = $gallery_photo_id;
							$media['sitemap_id'] = $sitemap_id;
							$media['type'] = 'photo';
							$media['title'] = $_POST['photos'][$key]['title'];
							$media['description'] = $_POST['photos'][$key]['description'];
							$media['location'] = $file_url;
							//$media['location'] = upload_to_s3($ftmp, upload_folder('galleries') . $album_id . '-' . str_replace(' ', '-', strip_ext($photo['name'])) . '.' . get_ext($photo['name']));
							
							if (!$db->insert('table=>media', $media)) {
								
								$upload_errors = true;
								
							} else if (isset($_POST['photos']['default']) && ($_POST['photos']['default'] == $key)) {
							
								$copy_file = upload_folder('galleries', true) . $album_id . '.' . get_ext($photo['name']);
								copy($uploadfile, $copy_file);
								$db->update('table=>galleries', 'id=>' . $galleries['id'], array('thumb' => ''));
								
								if ($copies = get_media_versions('GALLERY_THUMB')) {
									foreach ($copies as $copy) {
										$w = false;
										$h = false;
										$cr = false;

										if (is_array($copy)) {
											if (isset($copy[0])) $w = $copy[0];
											if (isset($copy[1])) $h = $copy[1];
											if (isset($copy[2])) $cr = $copy[2];
										} else {
											foreach (trim_explode("_", $copy) as $c) {
												if (substr($c, 0, 2) == 'wi') $w = substr($c, 2);
												if (substr($c, 0, 2) == 'he') $h = substr($c, 2);
												if (substr($c, 0, 2) == 'cr') $cr = substr($c, 2);
											}
										}

										create_image($copy_file, true, $w, $h, $cr);
										upload_to_s3(add_photo_info($copy_file, $w, $h, $cr), add_photo_info($copy_file, $w, $h, $cr));
										
									}
								}
								//upload_media($photo, $copy, 'GALLERY_THUMB');
								
							}
							
						} else {
							
							$upload_errors = true;
							
						}

					} else {
						
						// typically because folder doesn't exist or permissions on folder are not correct (0777)
						$upload_errors = true;

					}
					
				} // if
				
			} // foreach

		}
		
		/*if ($upload_errors) {
			
			failure('Your gallery was successfully saved, but there were errors uploading some of the photos.');
			redirect_failure();
			
		} else {
		
			success('Gallery Album was successfully saved.');

			if (isset($_POST['continue'])) {
				redirect_failure();
			} else {
				redirect_success();
			}
			
		}*/
		if (!$upload_errors) {
			
			flush_memcache();

			success('Your gallery was successfully saved.');
			
			if (isset($_POST['continue']) || isset($_POST['publish-continue'])) {
				if (substr($_POST['redirect']['failure'], -5) == '/add/') {
					redirect(substr($_POST['redirect']['failure'], 0, -5) . "/" . $galleries['id'] . "/");
				} else {
					redirect_failure();
				}
			} else {
				
				redirect_success();
	
			}
			
		} else {
		
			failure("There were some errors uploading photos for this gallery.");
			
			if (isset($galleries['id']) && substr($_POST['redirect']['failure'], -5) == '/add/') {
				redirect(substr($_POST['redirect']['failure'], 0, -5) . "/" . $galleries['id'] . "/");
			} else {
				$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
				redirect_failure();
			}
			
		}
				
	} else {
		
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error saving your gallery to the database.');
		redirect_failure();

	}

?>