<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/blogs-save.php
	**	Creation Date: 2/13/08
	**	Description: Saves blogs to database
	**	Called From: admin/Flint/pages/blogs.php
	**
	**********************************************
	*/
	clear_field_values();
	clear_required_fields();
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'blogs';
	include(LIBRARY . 'handlePostVars.php');

	// Handle article specific adjustments to values...
	$blogs['body'] = preg_replace('/(<!--)(.+)(endif]-->)/', "", $blogs['body']);
	
	// specifically: url and excerpt
	if (!isset($blogs['url'])) {
		if (!empty($blogs['title'])) {
			// snip from headline
			$blogs['url'] = url_friendly(truncate($blogs['title'], 80));
		} else {
			// snip from body
			$blogs['url'] = url_friendly(truncate(strip_tags($blogs['body']), 80));
		}
	} 
	$blogs_with_title = $db->count('table=>blogs', "where=>(`deleted_at` IS NULL) AND (`title` LIKE '" . $blogs['url'] . "%')");
	if ($blogs_with_title > 0) {
		$blogs['url'] = $blogs['url'] . '-' . 1+$blogs_with_title;
	}
	
	//$blogs['author_ids'] = join(", ", $blogs['author_ids']);
	
	if (!isset($blogs['excerpt'])) {
		// snip from body
		$blogs['excerpt'] = truncate(strip_tags($blogs['body']), 250);
	}
	
	if (empty($blogs['deleted_at'])) {
		$blogs['deleted_at'] = NULL;
	}
	
	if (empty($blogs['published_at'])) {
		$blogs['published_at'] = NULL;
	}
	
	if (isset($_POST['publish']) || isset($_POST['publish-continue'])) {
		$blogs['published_at'] = NOW;
	}
	
	// Clean up blank entries... lets db handle defaults
	foreach ($blogs as $key => $value)
		if (empty($value)) unset($blogs[$key]);

	if (!isset($blogs['categories']))
		$blogs['categories'] = array();
	$blogs['blog_categories'] = join(', ', $blogs['categories']);
	unset($blogs['categories']);
	
	if ($db->save('table=>' . $table, $blogs)) {
		if (!isset($blogs['id']))
			$blogs['id'] = $db->getInsertId();
			
		// Blog Sitemap
		$table = 'blog_sitemap';
		include(LIBRARY . 'handlePostVars.php');

		$blog_sitemap['blog_id'] = $blogs['id'];
		$bs = $db->getOne('table=>' . $table, "where=>(`blog_id` = " . $blog_sitemap['blog_id'] . ") AND (`sitemap_id` = " . $blog_sitemap['sitemap_id'] . ") AND (`deleted_at` IS NULL)");
		
		if (is_array($bs) && isset($bs['id']))
			$blog_sitemap['id'] = $bs['id'];
		
		$db->save('table=>blog_sitemap', $blog_sitemap);
		
		// Now handle extras like: categories, media, groups
		// *** THIS NEEDS TO BE BUILT OUT *** //
		
		$mediaErrors = array();
		
		$table = 'media';
		include(LIBRARY . 'handlePostVars.php');
		
		fix_files_superglobal();
		
		// banner image
		//media[banner][file]
		//media[banner][description]
		
		$file = (isset($_FILES['media']['banner']['file']) && is_array($_FILES['media']['banner']['file'])) ? $_FILES['media']['banner']['file'] : array();
		
		if (isset($file['tmp_name']) && isset($file['name']) && !empty($file['tmp_name']) && !empty($file['name'])) {
			$ftmp = $file['tmp_name'];
			$uploadfile = upload_folder('blogs', true) . $blogs['id'] . '-banner-' . str_replace(' ', '-', $file['name']);
			
			if (!($file_url = upload_media($file, $uploadfile, 'BLOG_BANNER'))) {
			//if (!upload($file, $uploadfile)) {
	
				// error uploading file
				$mediaErrors[] = array("banner" => "There was an error uploading this blog's banner image.");
	
			} else {
				
				// upload to database...
				$banner['table'] = 'blogs';
				$banner['table_id'] = $blogs['id'];
				$banner['sitemap_id'] = $blog_sitemap['sitemap_id'];
				$banner['type'] = 'banner';
				$banner['location'] = $file_url;
				//$banner['location'] = upload_to_s3($ftmp, upload_folder('blogs') . $blogs['id'] . '-banner-' . str_replace(' ', '-', $file['name']));
				$banner['description'] = $media['banner']['description'];
				
				$db->delete('table=>media', "where=>(`table` = 'blogs') AND (`table_id` = " . $blogs['id'] . ") AND (`type` = 'banner')", false);
				
				if (!$db->save('table=>media', $banner)) {
					
					$mediaErrors[] = array("banner" => "There was an error saving the blog's banner to the database.");
					
				}
				
			}
			
		}
		
		$file = (isset($_FILES['media']['thumb']['file']) && is_array($_FILES['media']['thumb']['file'])) ? $_FILES['media']['thumb']['file'] : array();
		
		if (isset($file['tmp_name']) && isset($file['name']) && !empty($file['tmp_name']) && !empty($file['name'])) {
			$ftmp = $file['tmp_name'];
			$ext = strtolower(get_ext($file['name']));
			$uploadfile = upload_folder('blogs', true) . $blogs['id'] . '-thumb.' . $ext;
			
			if (!($file_url = upload_media($file, $uploadfile, 'BLOG_THUMB'))) {
			//if (!upload($file, $uploadfile)) {
	
				// error uploading file
				$mediaErrors[] = array("thumb" => "There was an error uploading this blog's thumbnail image.");
	
			} else {
				$db->update('table=>blogs', 'id=>' . $blogs['id'], array('thumb' => ''));
			}
			
		}
				
		// sidebar items	
		$sidebarItems = (isset($_FILES['media']['sidebar']) && is_array($_FILES['media']['sidebar'])) ? $_FILES['media']['sidebar'] : array();	
		foreach ($sidebarItems as $sidebarItemKey => $sidebarItem) {
			
			$file = $sidebarItem['file'];

			if (isset($file['tmp_name']) && isset($file['name']) && !empty($file['tmp_name']) && !empty($file['name'])) {
				$ftmp = $file['tmp_name'];
				$uploadfile = upload_folder('blogs', true) . $blogs['id'] . '-' . str_replace(' ', '-', $file['name']);

				if (!($file_url = upload_media($file, $uploadfile, 'BLOG_THUMB'))) {
				//if (!upload($file, $uploadfile)) {

					// error uploading file
					$mediaErrors[] = array("sidebar" => "There was an error uploading this blog's sidebar media.");

				} else {

					// create other versions
					//create_image($uploadfile, true, 195);

					// upload to database...
					$sidebar['table'] = 'blogs';
					$sidebar['table_id'] = $blogs['id'];
					$sidebar['sitemap_id'] = $blog_sitemap['sitemap_id'];
					$sidebar['location'] = $file_url;
					//$sidebar['location'] = upload_to_s3($ftmp, upload_folder('blogs') . $blogs['id'] . '-' . str_replace(' ', '-', $file['name']));
					$sidebar['title'] = $media['sidebar'][$sidebarItemKey]['title'];
					$sidebar['description'] = $media['sidebar'][$sidebarItemKey]['description'];
					
					$ext = strtolower(get_ext($file['name']));
					$sidebar['type'] = 'file';
					if (in_array($ext, array('gif', 'jpg', 'png', 'jpeg'))) {
						$sidebar['type'] = 'photo';
					//} else if (in_array($ext, array('pdf'))) {
					//	$sidebar['type'] = 'pdf';
					} else {
						$sidebar['type'] = 'file';
					}

					if (!$db->save('table=>media', $sidebar)) {

						$mediaErrors[] = array("sidebar" => "There was an error saving this blog's sidebar item to the database.");

					}

				}

			}
			
		}
		
		$videos = (isset($media['player']['video']) && is_array($media['player']['video'])) ? $media['player']['video'] : array();
		
		foreach ($videos as $videoKey => $video) {

			if (!empty($video['location']) && !empty($video['title'])) {
			
				// get video fields and save to database
				$video['table'] = 'blogs';
				$video['table_id'] = $blogs['id'];
				$video['sitemap_id'] = $blog_sitemap['sitemap_id'];
				$video['type'] = 'video';
				
				if (!$db->save('table=>media', $video)) {

					$mediaErrors[] = array("media-video" => "There was an error saving this blog's mediaplayer video to the database.");

				}
			
			}

		}
		
		$slideshows = (isset($media['player']['slideshow']) && is_array($media['player']['slideshow'])) ? $media['player']['slideshow'] : array();
		
		foreach ($slideshows as $slideshowKey => $slideshow) {
			
			$slideshow_id = false;

			if (isset($slideshow['id']) && is_numeric($slideshow['id'])) {
				// upload photos to an already existing slideshow
				$slideshow_id = $slideshow['id'];
			} else {
				// create a new slideshow
				$slideshow['table'] = 'blogs';
				$slideshow['table_id'] = $blogs['id'];
				$slideshow['sitemap_id'] = $blog_sitemap['sitemap_id'];
				$slideshow['type'] = 'slideshow';
				
				if (!$db->save('table=>media', $slideshow)) {

					$mediaErrors[] = array("media-slideshow" => "There was an error creating this blog's mediaplayer slideshow in the database.");

				} else {
					
					$slideshow_id = $db->last_id;
					
				}
				
			}
			
			if (is_numeric($slideshow_id)) {
				
				$slideshowPhotos = (isset($media['player']['slideshow-photo']) && is_array($media['player']['slideshow-photo'])) ? $media['player']['slideshow-photo'] : array();
				$slideshowPhotoUploads = (isset($_FILES['media']['player']['slideshow-photo']) && is_array($_FILES['media']['player']['slideshow-photo'])) ? $_FILES['media']['player']['slideshow-photo'] : array();
				
				foreach ($slideshowPhotos as $photoKey => $photo) {
					
					$file = $slideshowPhotoUploads[$photoKey]['file'];
					//dump($file);

					if (!empty($photo['title']) && (isset($file['tmp_name']) && isset($file['name']) && !empty($file['tmp_name']) && !empty($file['name']))) {

						// upload slideshow photo
						$ftmp = $file['tmp_name'];
						$uploadfile = upload_folder('blogs', true) . $blogs['id'] . '-slideshow-' . str_replace(' ', '-', $file['name']);

						if (!($file_url = upload_media($file, $uploadfile, 'BLOG_SLIDESHOW'))) {
						//if (!upload($file, $uploadfile)) {

							// error uploading file
							$mediaErrors[] = array("media-slideshow-photo" => "There was an error uploading this blog's mediplayer slideshow photo file to server.");

						} else {
							
							// other versions
							//create_image($uploadfile, true, 500);
							//create_image($uploadfile, true, 200, 200);
							//create_image($uploadfile, true, 250, 200);

							// get slideshow photo fields and save to database
							$photo['table'] = 'media';
							$photo['table_id'] = $slideshow_id;
							$photo['sitemap_id'] = $blog_sitemap['sitemap_id'];
							$photo['type'] = 'slideshow-photo';
							$photo['location'] = $file_url;
							//$photo['location'] = upload_to_s3($ftmp, upload_folder('blogs') . $blogs['id'] . '-slideshow-' . str_replace(' ', '-', $file['name']));

							if (!$db->save('table=>media', $photo)) {

								$mediaErrors[] = array("media-slideshow-photo" => "There was an error saving this blog's mediaplayer slideshow photo to the database.");

							}

						}

					}
				}
				
			}

		}
		
		$audios = (isset($media['player']['audio']) && is_array($media['player']['audio'])) ? $media['player']['audio'] : array();
		$audioUploads = (isset($_FILES['media']['player']['audio']) && is_array($_FILES['media']['player']['audio'])) ? $_FILES['media']['player']['audio'] : array();
		
		foreach ($audios as $audioKey => $audio) {
			
			$file = $audioUploads[$audioKey]['file'];
			
			if (!empty($audio['title']) && (isset($file['tmp_name']) && isset($file['name']) && !empty($file['tmp_name']) && !empty($file['name']))) {
			
				// upload audio
				
			
				$ftmp = $file['tmp_name'];
				$uploadfile = upload_folder('blogs', true) . $blogs['id'] . '-audio-' . str_replace(' ', '-', $file['name']);

				if (!($file_url = upload_media($file, $uploadfile))) {
				//if (!upload($file, $uploadfile)) {
				
					// error uploading file
					$mediaErrors[] = array("media-audio" => "There was an error uploading this blog's mediplayer audio file to server.");

				} else {
					
					// get audio fields and save to database
					$audio['table'] = 'blogs';
					$audio['table_id'] = $blogs['id'];
					$audio['sitemap_id'] = $blog_sitemap['sitemap_id'];
					$audio['type'] = 'audio';
					$audio['location'] = $file_url;
					//$audio['location'] = upload_to_s3($ftmp, upload_folder('blogs') . $blogs['id'] . '-audio-' . str_replace(' ', '-', $file['name']));
				
					if (!$db->save('table=>media', $audio)) {

						$mediaErrors[] = array("media-audio" => "There was an error saving this blog's mediaplayer audio to the database.");

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

			success('Blog Post was successfully saved.');
			
			if (isset($_POST['continue']) || isset($_POST['publish-continue'])) {
				if (substr($_POST['redirect']['failure'], -5) == '/add/') {
					redirect(substr($_POST['redirect']['failure'], 0, -5) . "/" . $blogs['id'] . "/");
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
				
				failure("There were a number of errors uploading media for this blog post.");
				
			}
			
			if (isset($blogs['id']) && substr($_POST['redirect']['failure'], -5) == '/add/') {
				redirect(substr($_POST['redirect']['failure'], 0, -5) . "/" . $blogs['id'] . "/");
			} else {
				$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
				redirect_failure();
			}
			
		}
		
	} else {
		
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error saving this blog post.');
		redirect_failure();

	}

?>