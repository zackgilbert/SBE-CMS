<?php
	
	function add_photo_info($filename, $width = false, $height = false, $cropratio = false) {
		$ext = get_ext($filename);
		$file = strip_ext($filename);
		$version = false;
		if (strpos($file, '.') !== false) {
			$pieces = trim_explode('.', $file);
			$version = array_pop($pieces);
			$file = join('.', $pieces);
		}
		if (is_numeric($width))
			$file .= '_wi' . $width;
		if (is_numeric($height))
			$file .= '_he' . $height;
		if (is_string($cropratio))
			$file .= '_cr' . $cropratio;
		if ($version)
			$file .= '.' . $version;
		return $file . '.' . $ext;
	} // add_photo_info
	
	function alt() {
		$args = func_get_args();
		foreach ($args as $key => $arg)
			if (!is_string($arg) || empty($arg))
				unset($args[$key]);
		return str_replace('"', '&quot;', htmlentities2(valid(join(" - ", $args))));
	} // alt

	function create_image($filename, $save_only = true, $width = false, $height = false, $crop_ratio = false) {
		$ext = get_ext($filename);
		$file = get_presentation_file($filename, $ext);

		$mime = false;
		if ($ext == 'gif') :
			$mime = 'image/gif';
		elseif (in_array($ext, array('jpg', 'jpeg'))) :
			$mime = 'image/jpeg';
		elseif ($ext == 'png') :
			$mime = 'image/png';
		endif;

		$image = $file['path'] . $file['name'];
		
		if (!file_exists($image)) :
			return 'Error: image does not exist: ' . $image;
		elseif (substr($mime, 0, 6) != 'image/') : 
			return 'Error: requested file is not an accepted type: ' . $image;
		elseif ($file['found'] === false) :
			return 'Error: image file appears to be corrupted: ' . $filename;
		else :

			//$maxWidth		= (isset($_GET['width'])) ? (int) $_GET['width'] : 0;
			//$maxHeight		= (isset($_GET['height'])) ? (int) $_GET['height'] : 0;
			$maxWidth		= ($width) ? (int) $width : 0;
			$maxHeight		= ($height) ? (int) $height : 0;

			//if (isset($_GET['color']))
			//	$color		= preg_replace('/[^0-9a-fA-F]/', '', (string) $_GET['color']);
			//else
				$color		= FALSE;
				
			$crop_ratio = str_replace("/", ":", $crop_ratio);	// make sure you use : instead of /

			//$new_filename = strip_ext($filename);
			$new_filename = $file['path'] . strip_ext($file['name']);
			if ($width)
				$new_filename = $new_filename . "_wi" . $width;
			if ($height)
				$new_filename = $new_filename . "_he" . $height;
			if ($crop_ratio)
				$new_filename = $new_filename . "_cr" . $crop_ratio;
			$new_filename = $new_filename . "." . $ext;

			// Should we just show the image and not try to let php do it?
			$showOriginal = false;

			// no cropping to be done...
			if (!$maxWidth && !$maxHeight)
				$showOriginal = true;

			// image functions not installed...
			if (!function_exists('ImageDestroy') || !function_exists('GetImageSize'))
				$showOriginal = true;

			// make sure we can get the dimensions of the image...
			if (function_exists('GetImageSize')) :

				// Get the size and MIME type of the requested image
				$size	= GetImageSize($image);

				$width			= $size[0];
				$height			= $size[1];

				if (!$maxWidth && ($maxHeight >= $height)) :
					$showOriginal = true;
				elseif (!$maxHeight && ($maxWidth >= $width)) : 
					$showOriginal = true;
				elseif (($maxWidth >= $width) && ($maxHeight >= $height)) :
					$showOriginal = true;
				endif;

			endif;


			// If we don't have a max width or max height, OR the image is smaller than both
			// we do not want to resize it, so we simply output the original image and exit
			if ($showOriginal) :

				if (!is_file($new_filename) && is_file($image))
					copy($image, $new_filename);

				if ($save_only)
					return true;
					
				header("Content-type: $mime");
				//header('Content-Length: ' . filesize($image));
				readfile($image);

				return true;

			else : 	
				// Otherwise, we want to do some fun image replacement... 
				// INSPIRED BY: (mostly just copying large chunks of code to fit into this script)
				// Smart Image Resizer 1.2.1
				// Created by: Joe Lencioni (http://shiftingpixel.com)
				// URL: http://shiftingpixel.com/2008/03/03/smart-image-resizer/
				// Date: March 9, 2008

				$memoryToAllocate	= '100M';
				//$currentDir			= dirname(__FILE__);
				//$cacheDir			= $currentDir . '/cache/images/';

				//$mime	= $size['mime'];

				// If either a max width or max height are not specified, we default to something
				// large so the unspecified dimension isn't a constraint on our resized image.
				// If neither are specified but the color is, we aren't going to be resizing at
				// all, just coloring.

				if (!$maxWidth && $maxHeight) {
					$maxWidth	= 99999999999999;
				} elseif ($maxWidth && !$maxHeight) {
					$maxHeight	= 99999999999999;
				} elseif ($color && !$maxWidth && !$maxHeight) {
					$maxWidth	= $width;
					$maxHeight	= $height;
				}

				// Ratio cropping
				$offsetX	= 0;
				$offsetY	= 0;

				if ($crop_ratio) {
					$cropRatio		= explode(':', (string) $crop_ratio);
					if (count($cropRatio) == 2) {
						$ratioComputed		= $width / $height;
						$cropRatioComputed	= (float) $cropRatio[0] / (float) $cropRatio[1];

						// Image is too tall so we will crop the top and bottom
						if ($ratioComputed < $cropRatioComputed) { 
							$origHeight	= $height;
							$height		= $width / $cropRatioComputed;
							//$offsetY 	= 0;
							$offsetY	= ($origHeight - $height) / 2;
						// Image is too wide so we will crop off the left and right sides
						} else if ($ratioComputed > $cropRatioComputed) { 
							$origWidth	= $width;
							$width		= $height * $cropRatioComputed;
							$offsetX	= ($origWidth - $width) / 2;
						}
					}
				}

				// Setting up the ratios needed for resizing. We will compare these below to determine how to
				// resize the image (based on height or based on width)
				$xRatio		= $maxWidth / $width;
				$yRatio		= $maxHeight / $height;

				// Resize the image based on width
				if (($xRatio * $height) <= $maxHeight) { 
					$tnHeight	= ceil($xRatio * $height);
					$tnWidth	= $maxWidth;
				// Resize the image based on height
				} else {
					$tnWidth	= ceil($yRatio * $width);
				 	$tnHeight	= $maxHeight;
				}

				// We don't want to run out of memory
				ini_set('memory_limit', $memoryToAllocate);

				// Set up a blank canvas for our resized image (destination)
				$dst	= imagecreatetruecolor($tnWidth, $tnHeight);

				// Set up the appropriate image handling functions based on the original image's mime type
				switch ($size['mime']) {
					case 'image/gif':
						// We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
						// This is maybe not the ideal solution, but IE6 can suck it
						$creationFunction	= 'ImageCreateFromGif';
						$outputFunction		= 'ImagePng';
						$mime				= 'image/png'; // We need to convert GIFs to PNGs
						$doSharpen			= FALSE;
						$quality			= 9;
					break;

					case 'image/png':
						$creationFunction	= 'ImageCreateFromPng';
						$outputFunction		= 'ImagePng';
						$doSharpen			= FALSE;
						$quality			= 9;
					break;

					default:
						$creationFunction	= 'ImageCreateFromJpeg';
						$outputFunction	 	= 'ImageJpeg';
						$doSharpen			= TRUE;
						$quality			= 100;
					break;
				}

				// Read in the original image
				$src	= $creationFunction($image);

				if (in_array($size['mime'], array('image/gif', 'image/png'))) {
					if (!$color) {
						// If this is a GIF or a PNG, we need to set up transparency
						imagealphablending($dst, false);
						imagesavealpha($dst, true);
					} else {
						// Fill the background with the specified color for matting purposes
						if ($color[0] == '#')
							$color = substr($color, 1);

						$background	= FALSE;

						if (strlen($color) == 6)
							$background	= imagecolorallocate($dst, hexdec($color[0].$color[1]), hexdec($color[2].$color[3]), hexdec($color[4].$color[5]));
						else if (strlen($color) == 3)
							$background	= imagecolorallocate($dst, hexdec($color[0].$color[0]), hexdec($color[1].$color[1]), hexdec($color[2].$color[2]));
						if ($background)
							imagefill($dst, 0, 0, $background);
					}
				}

				// Resample the original image into the resized canvas we set up earlier
				ImageCopyResampled($dst, $src, 0, 0, $offsetX, $offsetY, $tnWidth, $tnHeight, $width, $height);

				if ($doSharpen && function_exists('imageconvolution')) {
					// Sharpen the image based on two things:
					//	(1) the difference between the original size and the final size
					//	(2) the final size
					$sharpness	= findSharp($width, $tnWidth);

					$sharpenMatrix	= array(
						array(-1, -2, -1),
						array(-2, $sharpness + 12, -2),
						array(-1, -2, -1)
					);
					$divisor		= $sharpness;
					$offset			= 0;
					imageconvolution($dst, $sharpenMatrix, $divisor, $offset);
				}

				//dump(ABSPATH . $new_filename);

				// Write the resized image to the server
				//$outputFunction($dst, $resized, -1);

				// $outputFunction($dst, ABSPATH . $new_filename, $quality);
				$outputFunction($dst, $new_filename, $quality);

				if (!$save_only) {
					// Send the new image to the browser
					header("Content-Type: $mime");
					header('Content-Length: ' . filesize($new_filename));
					$outputFunction($dst, null, $quality);	// this seems to be taking a while...

					// Clean up the memory
					ImageDestroy($src);
					ImageDestroy($dst);
				}
				return true;
			endif;

		endif;
		return false;
	} // create_image
	
	function findSharp($orig, $final) { // function from Ryan Rud (http://adryrun.com)
		$final	= $final * (750.0 / $orig);
		$a		= 52;
		$b		= -0.27810650887573124;
		$c		= .00047337278106508946;

		$result = $a + $b * $final + $c * $final * $final;

		return max(round($result), 0);
	} // findSharp
	
	function get_media($media_id) {
		global $db;
		return $db->getOne('table=>media', 'id=>' . $db->escape($media_id));
	} // get_media
	
	function get_media_versions($which = false) {
		$all_versions = array();

		if (is_file(CONFIG . 'mediaversions.php')) {
			include CONFIG . 'mediaversions.php';
			$all_versions = array_merge($all_versions, $_VERSIONS);
		}
		
		if (is_file(ADMIN . "config/mediaversions.php")) {
			include ADMIN . 'config/mediaversions.php';
			$all_versions = array_merge($all_versions, $_VERSIONS);
		}
		
		if (is_file(ABSPATH . "sites/" . get_site() . "/" . get_theme() . "/config/mediaversions.php")) {
			include ABSPATH . "sites/" . get_site() . "/" . get_theme() . "/config/mediaversions.php";
			$all_versions = array_merge_recursive($all_versions, $_VERSIONS);
		}
		
		if (is_string($which)) {
			if (isset($all_versions[$which])) {
				return $all_versions[$which];
			} else {
				return false;
			}
		}

		return $all_versions;
	} // get_media_versions

	function get_video_thumbnail($video_location) {
		$video_url = get_video_url($video_location);
		if (strpos($video_url, 'youtube.com') !== false) {
			$code = (strpos($video_url, "&") !== false) ? scrape($video_url, "/v/", "&") : substr($video_url, strpos($video_url, "/v/")+3);
			$thumb = "http://img.youtube.com/vi/" . $code . "/2.jpg";
			return $thumb;
			//$thumb = str_replace("www.youtube.com/v/", "youtube.com/v/", $video_url);
			//$thumb = str_replace("youtube.com/v/", "img.youtube.com/vi/", $thumb);
			//$thumb = str_replace("&hl=en", "", $thumb);
			//$thumb = str_replace("&autoplay=1", "", $thumb);
			//$thumb = str_replace("&amp;autoplay=1", "", $thumb);
			//if (substr($thumb, 0, 7) == 'http://') {
			//	return $thumb . '/2.jpg';
			//}
		}
		return upload_folder('') . 'videos-0.jpg';
	} // get_video_thumbnail
	
	function get_video_url($video_location, $autoplay = false) {
		// check if embed code was supplied or just url...
		if ((strpos($video_location, 'http://www.youtube.com/v/') !== false) && scrape($video_location, 'http://www.youtube.com/v/', '"')) {
			$video_location = 'http://www.youtube.com/v/' . scrape($video_location, 'http://www.youtube.com/v/', '"');
		} else if ((strpos($video_location, ' src="http://') !== false) && scrape($video_location, ' src="', '" ')) {
			//$startPos = (strpos($video_location, ' src="http://') + 6);
			//$tempLoc = substr($video_location, $startPos);
			//$endPos = strpos($tempLoc, '" ');
			//$video_location = substr($tempLoc, 0, $endPos);
			$video_location = scrape($video_location, ' src="', '" ');
		} else if ((strpos($video_location, ' value="http://') !== false) && scrape($video_location, 'value="', '"')) {
			$video_location = scrape($video_location, ' value="', '"');
		} else if (substr($video_location, 0, strlen('http://www.youtube.com/watch?v=')) == 'http://www.youtube.com/watch?v=') {
			$video_location = "http://www.youtube.com/v/" . substr($video_location, strlen('http://www.youtube.com/watch?v=')) . "&hl=en";		
		}
		
		if ($autoplay && strpos($video_location, 'youtube.com') !== false) {
			$video_location = $video_location . '&autoplay=1';
		}
		return $video_location;
	} // get_video_url
	
	function upload($file = false, $uploadfile = false, $max_file_size = 768000) {

		$image_saved = false;

		if (is_array($file) && is_string($uploadfile) && isset($file['tmp_name']) && !empty($file['tmp_name']) && isset($file['size']) && ($file['size'] > 10)) {
			
			// make sure there aren't any spaces or &'s in file names... it kinda messes things up.
			$uploadfile = str_replace(array(' ', '&'), array('-', ''), $uploadfile);
			
			// clean out any copies of pre-existing versions of this file
			if (strlen(strip_ext($uploadfile)) > 0) {
				$files = glob(strip_ext($uploadfile) . '_*');
				if (!is_array($files)) $files = array();
				foreach ($files as $filename)
					if (is_writable($filename))
						unlink($filename);
			}
			
			// Capture the original size of the uploaded image
			$size = getimagesize($file['tmp_name']);
			
			// only photos that are two big can be resized...
			if (in_array($size['mime'], array('image/gif', 'image/png', 'image/jpg', 'image/jpeg', 'image/pjpeg')) && ($file['size'] > $max_file_size)) {
				
				$size_ratio = $max_file_size/$file['size'];

				// now write the resized image to disk. I have assumed that you want the
				// resized, uploaded image file to reside in the ./images subdirectory.
				switch ($size['mime']) {
					case 'image/gif':
						// We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
						// This is maybe not the ideal solution, but IE6 can suck it
						$creationFunction	= 'ImageCreateFromGif';
						$outputFunction		= 'ImagePng';
						$mime				= 'image/png'; // We need to convert GIFs to PNGs
					break;

					case 'image/png':
						$creationFunction	= 'ImageCreateFromPng';
						$outputFunction		= 'ImagePng';
					break;

					default:
						$creationFunction	= 'ImageCreateFromJpeg';
						$outputFunction	 	= 'ImageJpeg';
					break;
				}

				$src = $creationFunction($file['tmp_name']);

				$new_width = $size[0]*$size_ratio;
				$new_height = $size[1]*$size_ratio;
				$tmp = imagecreatetruecolor($new_width, $new_height);
				
				// this line actually does the image resizing, copying from the original image into the $tmp image
				imagecopyresampled($tmp, $src, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
				
				// Read in the original image
				$image_saved = $outputFunction($tmp, $uploadfile, 100);

				imagedestroy($src);
				imagedestroy($tmp); // NOTE: PHP will clean up the temp file it created

			} else {

				$image_saved = move_uploaded_file($file['tmp_name'], $uploadfile);

			}

		}
		
		return $image_saved;
	} // upload
	
	function upload_folder($child_dir = '', $abspath = false) {
		$folder = rtrim((UPLOADS . $child_dir), "/");
		if (!is_dir($folder))
			mkdir($folder, 0777);
		if ($abspath) {
			return $folder . "/";
		} else {
			return str_replace_once(LOCATION, "", get_url_from_path($folder . '/'));
			//return get_url_from_path($folder . '/');
		}
	} // upload_folder
	
	function upload_media($source_file = false, $destination_file_name = false, $copies = false, $max_file_size = 768000) {

		$file_saved = false;

		if (is_array($source_file) && is_string($destination_file_name) && isset($source_file['tmp_name']) && !empty($source_file['tmp_name']) && isset($source_file['size']) && ($source_file['size'] > 10)) {
			
			// make sure there aren't any spaces and &'s in file names... it kinda messes things up.
			$destination_file_name = str_replace(array(' ', '&'), array('-', ''), $destination_file_name);
			
			// clean out any copies of pre-existing versions of this file
			if (strlen(strip_ext($destination_file_name)) > 0) {
				$files = glob(strip_ext($destination_file_name) . '_*');
				if (!is_array($files)) $files = array();
				foreach ($files as $file_name)
					if (is_writable($file_name))
						unlink($file_name);
			}
			
			// Capture the original size of the uploaded image
			$file_size = $source_file['size'];//getimagesize($source_file['tmp_name']);
			
			// only photos that are two big can be resized...
			if (in_array($file_size['mime'], array('image/gif', 'image/png', 'image/jpg', 'image/jpeg', 'image/pjpeg')) && ($source_file['size'] > $max_file_size)) {
				
				$size_ratio = $max_file_size/$source_file['size'];

				// now write the resized image to disk. I have assumed that you want the
				// resized, uploaded image file to reside in the ./images subdirectory.
				switch ($file_size['mime']) {
					case 'image/gif':
						// We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
						// This is maybe not the ideal solution, but IE6 can suck it
						$creationFunction	= 'ImageCreateFromGif';
						$outputFunction		= 'ImagePng';
						$mime				= 'image/png'; // We need to convert GIFs to PNGs
					break;

					case 'image/png':
						$creationFunction	= 'ImageCreateFromPng';
						$outputFunction		= 'ImagePng';
					break;

					default:
						$creationFunction	= 'ImageCreateFromJpeg';
						$outputFunction	 	= 'ImageJpeg';
					break;
				}

				$src = $creationFunction($source_file['tmp_name']);

				$new_width = $size[0]*$size_ratio;
				$new_height = $size[1]*$size_ratio;
				$tmp = imagecreatetruecolor($new_width, $new_height);
				
				// this line actually does the image resizing, copying from the original image into the $tmp image
				imagecopyresampled($tmp, $src, 0, 0, 0, 0, $new_width, $new_height, $file_size[0], $file_size[1]);
				
				// Read in the original image
				$file_saved = $outputFunction($tmp, $destination_file_name, 100);

				imagedestroy($src);
				imagedestroy($tmp); // NOTE: PHP will clean up the temp file it created

			} else {

				$file_saved = move_uploaded_file($source_file['tmp_name'], $destination_file_name);

			}
			
			if ($file_saved) {
				// now that original file has been uploaded, check to see if we need to upload to S3
				$destination_file_url = str_replace_once(LOCATION, "", get_url_from_path($destination_file_name));
				upload_to_s3($destination_file_name, $destination_file_url);

				if (is_string($copies))
					$copies = get_media_versions($copies);

				if (in_array($file_size['mime'], array('image/gif', 'image/png', 'image/jpg', 'image/jpeg', 'image/pjpeg')) && ($source_file['size'] > 10) && is_array($copies)) {
					// then make copies of images
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

						create_image($destination_file_name, true, $w, $h, $cr);
						upload_to_s3(add_photo_info($destination_file_name, $w, $h, $cr), add_photo_info($destination_file_url, $w, $h, $cr));

					}

				}
				return $destination_file_url;
				
			}

		}
		
		return false;
	} // upload_media

?>