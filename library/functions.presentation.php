<?php

	function javascripts($url = false) {
		if (!is_string($url)) $url = $_SERVER['REQUEST_URI'];
		$scripts = get_javascripts($url);
		
		$js = '';
		if (!PRODUCTION && DEBUG_JAVASCRIPTS) {
			$js .= '<script type="text/javascript"> var root = \'' . LOCATION . '\'; </script>' . "\n";			
			foreach ($scripts as $script) {
				$js .= include_javascript($script) . "\n";
			}
		} else {
			$loc = (strpos($url, "?") !== false) ? substr($url, 0, strpos($url, "?")) : $_SERVER['REQUEST_URI'];
			foreach ($scripts as $script) {
				if (substr($script, 0, strlen('http://')) == 'http://') {
					$js .= include_javascript($script) . "\n";
				}
			}
			$js .= '<script type="text/javascript" src="' . $loc . 'javascripts/"></script>' . "\n";
		}
		return $js;
	} // javascripts
	
	function get_javascripts($url = false) {
		if (!is_string($url)) $url = $_SERVER['REQUEST_URI'];
		$loc = (substr($url, -1*strlen('javascripts/')) == 'javascripts/') ? substr($url, 0, -1*strlen('javascripts/')) : $url;
		$loc = (strpos($loc, "?") !== false) ? substr($loc, 0, strpos($loc, "?")) : $loc;
		if ($memcached_scripts = get_memcache('scripts:' . $loc)) return $memcached_scripts;
		if (!PRODUCTION || !isset($_SESSION['javascripts'][$loc]) || isset($_GET['reset'])) {
			$scripts = array();
			$scripts = @array_merge($scripts, $GLOBALS['defaults']['javascripts']);	//load global default js
			$scripts = @array_merge($scripts, $GLOBALS['defaults'][get_defaults()]['javascripts']);	//load defaults js
			$scripts[] = @strip_ext(get_template());								//load template js
			$scripts = @array_merge($scripts, get_javascripts_by_prefix(get_template() . '-'));
			$scripts[] = @strip_ext(get_page());									//load page js
			$scripts = @array_merge($scripts, get_var('javascripts'));				//load routes js
			//?load any other additional js?
			$level = get_var('level');
			$scripts = @array_merge($scripts, trim_explode(',', $level['javascripts']));
			//$scripts = strip_ext($scripts);
			$scripts = array_unique($scripts);										// no repeats
			$scripts = array_clean($scripts, false);
			$_SESSION['javascripts'][$loc] = $scripts;
		}
		set_memcache('scripts:' . $loc, $_SESSION['javascripts'][$loc]);
		return $_SESSION['javascripts'][$loc];
	} // get_javascripts
	
	function get_javascripts_by_prefix($prefix) {
		$locations = array(get_path() . "/");
		$scripts = array();
		
		foreach ($locations as $location) {
			if ($handle = opendir($location . 'javascripts/')) {
				while (false !== ($file = readdir($handle))) {
					if ((substr($file, 0, strlen($prefix)) == $prefix) && !is_file($location . 'pages/' . strip_ext($file) . '.php')) {
						$scripts[] = $file;
					}
				}
				closedir($handle);
			}
		}
		return $scripts;
	} // get_javascripts_by_prefix
	
	function include_javascript($script) {
		if (substr($script, 0, strlen('http://')) == 'http://') {
			$js = '<script type="text/javascript" src="' . $script . '"></script>';
		} else {
			$file = get_presentation_file($script,'js');
			if ($file['found']) {
				if ((strpos($script, "tinymce") !== false) || (strpos($script, 'tiny_mce') !== false)) {
					$js = '<script type="text/javascript" src="' . $file['url'] . $file['name']/* . "." . $file['ext']*/ . '"></script>';
				} else {
					$js = '<script type="text/javascript" src="' . $file['versioned'] . '"></script>';
				}
			} else {
				$js = '<!-- Javascript File (' . $script . ') Could Not Be Found -->';
			}
		}

		return $js;
	} // include_javascript
	
	function stylesheets($url = false) {
		if (!is_string($url)) $url = $_SERVER['REQUEST_URI'];
		$styles = get_stylesheets($url);
		
		$css = '<style type="text/css">' . "\n";
		if (!PRODUCTION && DEBUG_STYLESHEETS) {
			foreach ($styles as $style) {
				$css .= "\t" . include_stylesheets($style) . "\n";
			}
		} else {
			$loc = (strpos($url, "?") !== false) ? substr($url, 0, strpos($url, "?")) : $url;
			$css .= "\t@import url(" . $loc . "stylesheets/);\n";
		}
		return $css .= "</style>\n";
	} // stylesheets
	
	function get_stylesheets($url = false) {
		if (!is_string($url)) $url = $_SERVER['REQUEST_URI'];
		$loc = (substr($url, -1*strlen('stylesheets/')) == 'stylesheets/') ? substr($url, 0, -1*strlen('stylesheets/')) : $url;
		$loc = (strpos($loc, "?") !== false) ? substr($loc, 0, strpos($loc, "?")) : $loc;
		if ($memcached_styles = get_memcache('styles:' . $loc)) return $memcached_styles;
		if (!PRODUCTION || !isset($_SESSION['stylesheets'][$loc]) || isset($_GET['reset'])) {
			$styles = array();
			$styles = @array_merge($styles, $GLOBALS['defaults']['stylesheets']);	//load global default css
			$styles = @array_merge($styles, $GLOBALS['defaults'][get_defaults()]['stylesheets']);	//load defaults css
			$styles[] = @strip_ext(get_template());									//load template css
			$styles = @array_merge($styles, get_stylesheets_by_prefix(get_template() . '-'));
			//$styles[] = @get_var('section');														
			$styles[] = @strip_ext(get_page());										//load page css
			$styles = @array_merge($styles, get_var('stylesheets'));				//load routes css
			//?load any other additional css?
			$level = get_var('level');
			$styles = @array_merge($styles, trim_explode(',', $level['stylesheets']));
			//$styles = strip_ext($styles);
			$styles = array_unique($styles);										// no repeats
			$styles = array_clean($styles, false);
			$_SESSION['stylesheets'][$loc] = $styles;
		}
		set_memcache('styles:' . $loc, $_SESSION['stylesheets'][$loc]);
		return $_SESSION['stylesheets'][$loc];
	} // get_stylesheets
	
	function get_stylesheets_by_prefix($prefix) {
		$locations = array(get_path() . "/");
		$styles = array();
		
		foreach ($locations as $location) {
			if ($handle = opendir($location . 'stylesheets/')) {
				while (false !== ($file = readdir($handle))) {
					if ((substr($file, 0, strlen($prefix)) == $prefix) && !is_file($location . 'pages/' . strip_ext($file) . '.php')) {
						$styles[] = $file;
					}
				}
				closedir($handle);
			}
		}
		return $styles;
	} // get_stylesheets_by_prefix
	
	function add_presentation($name = false, $version = true) {
		add_javascript($name, $version);
		add_stylesheet($name, $version);
	} // add_presentation
	
	function add_javascript($name = false, $version = true) {
		if ($name) {
			$javascripts = get_var('javascripts');
			if (!is_array($javascripts)) $javascripts = array();
			$javascripts[] = $name;
			set_var('javascripts', $javascripts);
		}
	} // add_javascript
	
	function add_stylesheet($name = false, $version = true) {
		if ($name && is_string($name)) {
			$stylesheets = get_var('stylesheets');
			if (!is_array($stylesheets)) $stylesheets = array();
			$stylesheets[] = $name;
			set_var('stylesheets', $stylesheets);
		}		
	} // add_stylesheet
	
	function get_presentation_file($name, $ext = false) {
		//if ($memcached_name = get_memcache('presentation:' . $name)) return $memcached_name;
		$folder = get_folder();
		$theme = get_theme();
		
		if ($ext === false)
			$ext = get_ext($name);
		
		$presentationFolder = false;
		if ($ext == 'js') {
			$presentationFolder = 'javascripts';
		} else if ($ext == 'css') {
			$presentationFolder = 'stylesheets';
		} else if (in_array($ext, array('gif', 'jpg', 'png'))) {
			$presentationFolder = 'images';
		} else {
			$presentationFolder = 'files';
		}

		$file = array("path" => false, "url" => false, "found" => false, "name" => false, "versioned" => false, "ext" => false);
				
		//$locations = array('', $folder . "/themes/" . $theme . "/" . $presentationFolder . "/", $folder . "/themes/" . DEFAULT_THEME . "/" . $presentationFolder . "/", DEFAULT_FOLDER . "/themes/" . DEFAULT_THEME . "/" . $presentationFolder . "/", $presentationFolder . "/", $folder . "/themes/" . $theme . "/", $folder . "/themes/" . DEFAULT_THEME . "/", DEFAULT_FOLDER . "/themes/" . DEFAULT_THEME . "/");
		$locations = array('', get_path() . "/" . $presentationFolder . "/", ABSPATH . $presentationFolder . "/", get_path() . "/");
		if (get_var('plugin'))
			$locations[] = PLUGINS . get_var('plugin') . '/' . $presentationFolder . '/';
		
		foreach ($locations as $location) {
			if (!get_ext($name))
				$name = $name . "." . $ext;
			
			if (!$file['found'] && is_file($location . $name)) {//strip_ext($name) . "." . $ext)) {
				if (empty($location) && (substr($name, 0, strlen("plugins/")) == 'plugins/'))
					$name = PLUGINS . substr($name, strlen("plugins/"));
				$file['path'] = dirname($location . $name) . "/";
				$file['url'] = get_url_from_path($file['path']);
				$file['name'] = basename($location . $name);//strip_ext($name);
				$file['versioned'] = get_url_from_path(autoVer($file['path'] . $file['name']));// . "." . $ext)); 
				$file['ext'] = $ext;
				$file['found'] = true;
				set_memcache('presentation:' . $name, $file);
				return $file;
			}
			
		}
		set_memcache('presentation:' . $name, $file);
		return $file;
	} // get_presentation_file
	
	function include_stylesheets($style) {
		$file = get_presentation_file($style,'css');
		if ($file['found']) {
			$css = "@import url(" . $file['versioned'] . ");";
		} else {
			$css = '/* Stylesheet File (' . $style . ') Could Not Be Found */';
		}

		return $css;
	} // include_stylesheets
	
	function compress_javascript($buffer) {
		$buffer = preg_replace("/^(\\/\\*[\\d\\D]*?\\*\\/)/is", '', $buffer);
		$buffer = preg_replace("/(\\s\\/\\*[\\d\\D]*?\\*\\/)/is", '', $buffer);
		$buffer = preg_replace("/^(\\/\\/)[^\\n]+/is", '', $buffer);
		$buffer = preg_replace("/(\\s\\/\\/)[^\\n]+/is", '', $buffer);
		$buffer = str_replace("\t", " ", $buffer); 
		$buffer = str_replace("  ", " ", $buffer);
		$buffer = preg_replace("/\\n[ ]+/", "\n", $buffer);
		$buffer = preg_replace("/\\n\\n\\n/", "\n", $buffer);
		$buffer = preg_replace("/\\n\\n/", "\n", $buffer);
		return trim($buffer);
	} // compress_javascript

	function compress_stylesheet($css){
		$css = preg_replace('!//[^\n\r]+!', '', $css);#comments
		$css = preg_replace('/[\r\n\t\s]+/s', ' ', $css);#new lines, multiple spaces/tabs/newlines
		$css = preg_replace('#/\*.*?\*/#', '', $css);#comments
		$css = preg_replace('/[\s]*([\{\},;:])[\s]*/', '\1', $css);#spaces before and after marks
		$css = preg_replace('/^\s+/', '', $css);#spaces on the begining
		if (LOCATION != '/') $css = str_replace("url(/", "url(" . LOCATION, $css);
		return $css;
	} // css_compress
	
	function add_rss($name = false) {
		if (!$name || !is_string($name)) {
			if (section('rss') == 'enable')
				$name = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? $_SERVER['REQUEST_URI'] . "rss/" : str_replace("/?", "/rss/?", $_SERVER['REQUEST_URI']);
		}
		if ($name && is_string($name)) {
			$rss = get_var('rss');
			$rss[] = $name;
			set_var('rss', $rss);
		}
	} // add_rss
	
	function rss() {
		//$links = array($_SERVER['REQUEST_URI'] . 'rss/');
		$links = get_var('rss');
		if (!$links) $links = array();
		$html = '';
		foreach ($links as $link) {
			$html = $html . '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="' . $link . '" />' . "\n";
		}
		return $html;
	} // rss

?>