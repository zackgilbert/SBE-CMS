<?php

	require_once dirname(__FILE__) . "/library/session.php";

	require_once dirname(__FILE__) . "/config/debug.php";
	require_once dirname(__FILE__) . "/library/functions.php";
	require_once dirname(__FILE__) . "/library/constants.core.php";
	require_once dirname(__FILE__) . "/library/functions.variables.php";

	require_once LIBRARY . "functions.core.php";
	require_once LIBRARY . "constants.time.php";
	require_once LIBRARY . "variables.time.php";
	require_once LIBRARY . "functions.time.php";
	require_once CONFIG . "settings.php";
	
	require_once LIBRARY . "outputbuffer.php";

	// Use memcache if it's available
	require_once LIBRARY . "memcache.php";

	require_once LIBRARY . "functions.presentation.php";
	require_once LIBRARY . "functions.media.php";

	define("LOCATION", get_location(!is_admin()));

	$_GET['file'] = str_replace_once(LOCATION, '', $_SERVER['REQUEST_URI']);
	
	if (($_GET['file'] != $_SERVER['REQUEST_URI']) && (($pos = strpos($_SERVER['REQUEST_URI'], $_GET['file'])) !== false)) {
		// might need to do some adjusting to the file name...
		//RewriteRule ^(.*)/(.+)\.([0-9]{10,})\.(js|css|gif|jpg|png)$ $1/$2.$4 [L,QSA]
		$full = substr($_SERVER['REQUEST_URI'], $pos);
		$full = preg_replace('/\.([0-9]{10,})\./', '.', $full);
		if ($full != $_GET['file']) {
			$_GET['file'] = $full;
		}
	}
	
	// experimental way of handling images...
	if (isset($_GET['file']) && strpos($_GET['file'], '/images/') !== false) {
		//$file = trim_explode('/images/', $_GET['file']);
		//$_GET['file'] = current($file) . '/images/' . end($file);
		$urls = trim_explode('/', $_GET['file']);
		$pre = $post = array();
		$imagesFound = false;
		foreach ($urls as $url) {
			if (!$imagesFound) {
				if ($url == 'images') {
					$imagesFound = true;
				} else {
					$pre[] = $url;
				}
			} else {
				$post[] = $url;
			}
		}
		foreach ($pre as $p) {
			if (is_dir(ABSPATH . 'sites/' . $p)) {
				set_var('site', $p);
			} else if (is_dir(ABSPATH . 'sites/' . get_folder() . '/' . $p)) {
				set_var('theme', $p);
			}
		}
		$filename = 'images/' . join($post);//end($urls);
		$path = "sites/" . get_folder() . "/" . get_theme() . "/";//substr($_GET['file'], 0, strpos($_GET['file'], $filename));
		$_GET['file'] = $path . $filename;		
	} else if (strpos($_GET['file'], 'uploads/') !== false) {
		$_GET['file'] = substr($_GET['file'], strpos($_GET['file'], 'uploads/'));
	}
	
	if (isset($_GET['file']) && ((strpos($_GET['file'], '/files/') !== false))) {
		$_GET['file'] = substr($_GET['file'], strpos($_GET['file'], "/files/"));
	}

	if (isset($_GET['file']) && !is_file($_GET['file'])) {
		$_GET['file'] = str_replace("sites/main/default/", "sites/" . get_folder() . "/" . get_theme() . "/", $_GET['file']);
	}

	if (isset($_GET['file']) && substr($_GET['file'], 0, strlen("sites/")) != "sites/") {
		$_GET['file'] = "sites/" . get_folder() . "/" . get_theme() . "/" . $_GET['file'];
	}

	// GET TO THE MEAT OF THINGS
	if (isset($_GET['file']) && is_file($_GET['file']) && (!isset($_GET['width']) && !isset($_GET['height']) && !isset($_GET['cropratio']))) :
		
		$ext = get_ext($_GET['file']);
		
		if ($ext == 'js') :
			$mime = 'application/x-javascript';
		elseif ($ext == 'css') :
			$mime = 'text/css';
		elseif ($ext == 'gif') :
			$mime = 'image/gif';
		elseif (in_array($ext, array('jpg', 'jpeg'))) :
			$mime = 'image/jpeg';
		elseif ($ext == 'png') :
			$mime = 'image/png';
		else :
			$mime = mime_content_type($_GET['file']);	
		endif;

		header("Content-type: " . $mime);		
		
		if (true){//PRODUCTION) {
			$expires = 60*60*24*7; // 1 week
			header("Pragma: public");
			header("Cache-Control: maxage=".$expires);
			header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
		}
		
		readfile($_GET['file']);
			
	elseif (isset($_GET['file'])) :		
		// DYNAMIC LINK TO A JS|CSS|IMAGE
		$fileName = $originalFile = $_GET['file'];
		$type = (isset($_GET['type'])) ? $_GET['type'] : 'uploads';
		
		// Once we've figured out where the file is located, now lets handle it according to its type
		
		if (in_array($type, array('javascripts', 'stylesheets'))) :
			// Stylesheets and Javascripts
			$file = get_presentation_file($fileName);
			
			// Get mime type...
			$mime = false;
			if ($type == 'javascripts') :
				$mime = 'application/x-javascript';
			elseif ($type == 'stylesheets') :
				$mime = 'text/css';
			else :
				// If it gets this far, usually that's a bad sign... leave commented to just use text/plain
				//$mime = mime_content_type($file['path'] . $file['name'] . '.' . $file['ext']);
			endif;
			
			if ($file['found']) :
				if ($mime) header("Content-type: " . $mime);
				include(($file['path'] . $file['name'] . '.' . $file['ext']));
			endif;

		elseif (in_array($type, array('images', 'uploads'))) :
			// Images
			// A little tougher... need to read image...
			//require_once LIBRARY . "class.img.php";			
			if ($type == 'uploads')
				$fileName = str_replace("sites/" . get_folder() . "/" . get_theme() . "/", '', $fileName);
				
			if (strpos($_SERVER['REQUEST_URI'], '/plugins/') !== false) {
				$plugin = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '/plugins/')+1);
				$plugin = substr($plugin, 0, strpos($plugin, '/', strlen('plugins/')));
				$fileName = str_replace("sites/" . get_folder() . "/" . get_theme() . "/", $plugin . "/", $fileName);
			}
			
			$ext = get_ext($fileName);
			$fileName = strip_ext($fileName);
			
			$w = false;
			$h = false;
			$c = false;
			
			// explode by _, then check for w**, h** c** then put the filename back together
			$parts = trim_explode('_', $fileName);
			for ($i=count($parts)-1; $i>=0; $i--) {
				if (substr($parts[$i], 0, 2) == 'cr') {
					$c = substr($parts[$i], 2);
					unset($parts[$i]);
				} else if (substr($parts[$i], 0, 2) == 'he') {
					$h = substr($parts[$i], 2);					
					unset($parts[$i]);
				} else if (substr($parts[$i], 0, 2) == 'wi') {
					$w = substr($parts[$i], 2);
					unset($parts[$i]);
				} else {
					break;
				}
			}
			
			$fileName = join("_", $parts) . '.' . $ext;
			
			$errors = create_image($fileName, false, $w, $h, $c);
			
			// if create_image returns errors
			if (is_string($errors))
				echo $errors;
			
		endif;
	
	elseif (isset($_GET['flinturl']) && isset($_GET['type'])) :
		// LOADER FOR ALL JS|CSS FILES FOR GIVEN ROUTE
		$q = $_GET['flinturl'];
		$type = $_GET['type'];

		// Include all variables and functions allowing for routes to work
		require_once CONFIG . "defaults.php";
		require_once CONFIG . "routes.php";
		require_once LIBRARY . "functions.routes.php";

		// Get all the variables as assigned from the routes and assign them to the global Flint variable
		set_vars(get_route_vars($q));
				
		// Get all the files of requested type to load in this one file...
		if ($type == 'javascripts') :

			$files = get_javascripts();
			$ext = 'js';
			header("Content-type: application/x-javascript");

		elseif ($type == 'stylesheets') :
		
			$files = get_stylesheets();
			$ext = 'css';
			header("Content-type: text/css");

		endif;
		
		foreach ($files as $orig_file) :
			
			clearstatcache();
			$file = get_presentation_file($orig_file, $ext);

			if ($file['found']) :
				#if the file doesn't exist or the time stamp time of both compressed and uncompressed files are not near
				$cache_file = CACHE . "{$type}/" . str_replace("/", '__', $file['path'] . $file['name'] . '.' . $file['ext']);

				if (!file_exists($cache_file) || ((filemtime($file['path'] . $file['name'] . '.' . $file['ext']) - filemtime($cache_file)) > 3)) {
					$code = file_get_contents($file['path'] . $file['name'] . '.' . $file['ext']); 					# get code
					
					if ($type == 'javascripts') { 								# if a JS file
						
						if (strpos($code, "/** DON'T PACK **/") !== false) {
							$packed = $code;
						} else if ((strpos($code, "/** PACKER **/") !== false) && is_file(LIBRARY . "class.JavascriptPacker.php")) {
							require_once LIBRARY . "class.JavascriptPacker.php"; 	# include Packer class
							$script = str_replace(';;;', '//', $code); 			# the ;;; comment feature in the packer beta
							$packer = new JavaScriptPacker($script, 62, 1, 0); 		# using Dean Edwards ’s Packer, check documentations on the class file for the best compression level
							$packed = $packer->pack(); 								# JS code compressed
						} else {
							$packed = compress_javascript($code);
						}
					} else { 													# if CSS file
						$packed = compress_stylesheet($code);

						while (strpos($packed, '@import url(') !== false) :
							$import = scrape($packed, '@import url(', ');');
							$pre_packed = substr($packed, 0, strpos($packed, '@import url('));
							$post_packed = substr($packed, strpos($packed, $import . ');') + strlen($import . ');'));
							//$compressed_contents = preg_replace("/@import url\((.+)\);/", '', $compressed_contents);
							$import = str_replace("'", '', str_replace('"', '', $import));

							$import_file = get_presentation_file($import, $ext);
							//include_once($file['path'] . $file['name'] . '.' . $file['ext']);
							$file_contents = file_get_contents($import_file['path'] . $import_file['name'] . '.' . $import_file['ext']);

							$packed = $pre_packed . compress_stylesheet($file_contents) . "\n" . $post_packed;
							unset($pre_packed);
							unset($post_packed);
						endwhile;

					} 
					file_put_contents($cache_file, trim($packed));		# inserting compressed code into files
					//touch($file); # change the time stamp time for original scripts
					#$GLOBALS['debuger'][] = "$js generated, diff was $diff"; 	# Global array to let you see results
				}
				$compressed_contents = file_get_contents($cache_file) . "\n";

				if (!PRODUCTION)
					echo "\n/* " . $file['path'] . $file['name'] . "." . $file['ext'] . " */\n";
				echo $compressed_contents;
				
			else :
			
				echo "\n/* Could not load file: " . $orig_file . " */\n";
			
			endif;

		endforeach;

	endif;

	stop_outputBuffer();

?>