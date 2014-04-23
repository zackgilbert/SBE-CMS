<?php

	function args() {
		$orig = func_get_args();
		$orig = ((count($orig) == 1) && is_array($orig[0])) ? $orig[0] : $orig;
		$new = array();
		foreach($orig as $arg) {
			if (!is_string($arg) || (strpos($arg, "=>") === false)) {
				if (is_array($arg) && (count($arg) == 1)) {
					$argKeys = array_keys($arg);
					$new[$argKeys[0]] = $arg[$argKeys[0]];
				} else if (!empty($arg)) {
					$new[] = $arg;
				}
			} else {
				$delPos = strpos($arg, "=>");
				$preDel = trim(substr($arg, 0, $delPos), " '\"");
				$postDel = trim(substr($arg, $delPos+2), " '\"");
				$new[$preDel] = (substr($postDel, 0, strlen('array (')) == 'array (') ? args(trim_explode(",", substr($postDel, strlen('array ('), -1))) : $postDel;
			}
		}
		return $new;
	} // /args
	
	function array_clean($array = array(), $strict = true) {
		foreach ($array as $key => $value) {
			if (is_null($value) || (!$strict && (empty($value) || !$value)))
				unset($array[$key]);
		}
		return $array;
	} // array_clean
	
	if (!function_exists('array_combine')) {
		function array_combine($arr1, $arr2) {
			$out = array();

			$arr1 = array_values($arr1);
			$arr2 = array_values($arr2);

			foreach($arr1 as $key1 => $value1) {
				$out[(string)$value1] = $arr2[$key1];
			}

			return $out;
		}
	} // array_combine
	
	// take from: http://us2.php.net/manual/en/function.array-count-values.php#73389
	function array_icount_values($array) {
	   $ret_array = array();
	   foreach($array as $value) {
	       foreach($ret_array as $key2 => $value2) {
	           if(strtolower($key2) == strtolower($value)) {
	               $ret_array[$key2]++;
	               continue 2;
	           }
	       }
	       $ret_array[$value] = 1;
	   }
	   return $ret_array;
	} // /array_icount_values
	
	/// *** DEPRECATED!!!
	function array_flatten($array, $field = false) {
		return flatten($array, $field);
	} // /array_flatten
	
	function array_end_key(&$array) {
		end($array);
		return key($array);
	} // array_end_key
	
	function array_max($array, $max = 0, $offset = 0) {
		if (is_array($array) && is_numeric($max) && ($max > 0)) {
			$new_array = array();
			$count = 0;
			foreach ($array as $key => $value) {
				if ($count >= $offset) {
					$new_array[$key] = $value;
					if (count($new_array) >= $max)
						return $new_array;
				}
				$count++;
			}
			return $new_array;
		}
		return $array;
	} // /array_max
	
	function array_remove($array = array(), $value = false) {
		$pos = array_search($value, $array);
		if ($pos !== false)
			array_splice($array, $pos, 1);
		return $array;
	} // array_remove
	
	function array_search_recursive($needle, $haystack, $path = array()) {
		foreach($haystack as $id => $val) {
			$path2 = $path;
			$path2[] = $id;
		
			if($val === $needle)
				return $path2;
			else if(is_array($val))
				if($ret = array_search_recursive($needle, $val, $path2))
					return $ret;
		}
		return false;
	} // array_search_recursive
	
	function array_set_current(&$array, $key) {
		reset($array);
		while ((key($array) != $key)) {
			if (!next($array)) {
				break;
			}
		}
	} // array_set_current
	
	function sort2($array = array(), $fieldToSort = 'created_at', $direction = SORT_DESC) {
		$sorted = array();
		foreach ($array as $key => $row) {
			if (is_array($row)) {
		    	$sorted[$key] = $row[$fieldToSort];
			} else if (is_object($row)) {
				$sorted[$key] = $row->$fieldToSort;
			}
		}
		array_multisort($sorted, $direction, $array);
		
		return $array;
	} // array_sort
	
	function array_to_link($array) {
		if (!is_array($array)) $array = array($array);
		$string = '<a href="' . $array[0] . '">';
		$string .= (isset($array[1])) ? $array[1] : $array[0];
		return $string . "</a>";
	} // /array_to_link
	
	function array_to_string($array, $delimiter = ", ") {
		$newArray = array();
		foreach ($array as $ar)
			$newArray[] = array_to_link($ar);
		return join($delimiter, $newArray);
	} // /array_to_string

	function array_to_paths($array = array(), $prefix = '') {
		$str			= '';
		$fresh_prefix	= $prefix;
		foreach($array as $key => $value) {
			$fresh_prefix .= "/{$key}";
			if (is_array($value)) {
				$str .= array_to_paths($value, $fresh_prefix);
				$fresh_prefix = $prefix;
			} else {
				$str .= "{$prefix}/{$key} = {$value}\n";
			}
		}
		return $str;
	} // /array_to_paths
	
	// inspired by: http://particletree.com/notebook/automatically-version-your-css-and-javascript-files/
	function autoVer($path){
		$pathInfo = pathinfo($path);
		$ver = '.'.filemtime($path).'.';
		return $pathInfo['dirname'] . '/' . strip_ext($pathInfo['basename']) . $ver . get_ext($pathInfo['basename']);
	} // autoVer
	
	function capitalize($string, $all_words = false) {
		return ($all_words) ? ucwords($string) : ucfirst($string);
	} // /capitalize
		
	function clean($string) {
		if (!is_string($string)) return $string;
		return (get_magic_quotes_gpc()) ? stripslashes($string) : $string;
	} // /clean
	
	function clean_url($string) {
		return str_replace("-", "_", $string);
	} // /clean_url
		
	function convert_smart_quotes($string) { 
		$search = array(chr(145), chr(146), "’", chr(147), chr(148), '“', '”', chr(151), 'é'); 
		$replace = array("'", "'", "'", '"', '"', '"', '"', '-', '&eacute;');
		return str_replace($search, $replace, $string); 
	} // /convert_smart_quotes
	
	function dump($obj) {
		echo "<pre>";
		var_dump($obj);
		echo "</pre>";
	} // /dump
	
	function externalize($html) {
		$html = preg_replace('#(="/)#is', '="http://' . $_SERVER['SERVER_NAME'] . '/', $html);
		return $html;
	} // /externalize
	
	if (!function_exists('file_put_contents') && !defined('FILE_APPEND')) {
		define('FILE_APPEND', 1);
		function file_put_contents($n, $d, $flag = false) {
		    $mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
		    $f = @fopen($n, $mode);
		    if ($f === false) {
		        return 0;
		    } else {
		        if (is_array($d)) $d = implode($d);
		        $bytes_written = fwrite($f, $d);
		        fclose($f);
		        return $bytes_written;
		    }
		} // /file_put_contents
	} // /if file_put_contents exists
	
	function fix_files_superglobal() {
		$flat_files		= array_to_paths($_FILES);
		$fixed_files	= preg_replace('#^(/[^/]+)(/name|/type|/tmp_name|/error|/size)([^\s]*)( = [^\n]*)#m', '\1\3\2\4', $flat_files);	
		$_FILES 		= paths_to_array($fixed_files);
	} // /fix_files_superglobal
	
	function flatten($item, $field = false) {
		if (!$field && current($item))
			$field = (is_array(current($item))) ? current(array_keys(current($item))) : 'id';
		foreach ($item as $k => $v) {
			if (is_array($item[$k]) && isset($item[$k][$field])) {
				$item[$k] = $item[$k][$field];
			} else if (is_object($item[$k]) && isset($item[$k]->$field)) {
				$item[$k] = $item[$k]->$field;
			}
		}
		return $item;		
	} // flatten
	
	function format_links($string) {
		$string = preg_replace('#(^|\s)(([a-zA-Z0-9._%+-]+)@(([.-]?[a-zA-Z0-9])*))#is', '\\1<a href="mailto:\\2">\\2</a>', $string);
		$string = preg_replace('#(^|\s)(http:\/\/)?([^\s"\'<>,;\(\)]+)([\.,])(com|edu|net|gov|org|tv|co\.uk|info|biz|us|fm|ca)([/][^\s\(\)<>,"\';]+[^\.\s\(\)<>,"\';])?#sm', '\\1<a href="http://\\3.\\5\\6">\\3.\\5\\6</a>', $string);
		return $string;
	} // format_links
	
	function format_phone($phone) {
		$phone = ereg_replace("[^0-9A-Za-z]",'',$phone);
		if (strlen($phone) == 7) {
			$prefix = substr($phone,0,3);
			$number = substr($phone,3,4);
			$phone = $prefix."-".$number;
		}elseif (strlen($phone) == 10) {
			$area = substr($phone,0,3);
			$prefix = substr($phone,3,3);
			$number = substr($phone,6,4);
			$phone = "(".$area.") ".$prefix."-".$number;
		}
		return($phone);
	} // format_phone
	
	function format_text($content) {
		$content = preg_replace("/<!--(.*)-->/", "", $content);
		$content = preg_replace("/&lt;!--(.*)--&gt;/", "", $content);
		$content = str_replace('<a href="www.', '<a href="http://www.', $content);
		$content = preg_replace("/<div([^>]*)>(.*)<\/div>/", "$2", $content);
		//$content = str_replace('\r\n', "\r\n\r\n", $content);
		$content = str_replace("\r\n", "<br/>", $content);
		$content = str_replace("<br>", "<br/>", $content);
		$content = str_replace("<br/><br/>", "</p><p>", $content);
		$content = str_replace("<br/></", "</", $content);
		$content = str_replace("<hr>", "<hr/>", $content);
		$content = str_replace("<ul", "</p><ul", $content);
		$content = str_replace("</ul>", "</ul><p>", $content);
		$content = str_replace("<ol", "</p><ol", $content);
		$content = str_replace("</ol>", "</ol><p>", $content);
		$content = str_replace("<br/><li", "<li", $content);
		$content = str_replace("<blockquote", "</p><blockquote", $content);
		$content = str_replace("</blockquote>", "</blockquote><p>", $content);
		$content = str_replace("<pre", "</p><pre", $content);
		$content = str_replace("</pre>", "</pre><p>", $content);
		$content = str_replace("<p><h", "<h", $content);
		$content = str_replace("/h1>", "/h1><p>", $content);
		$content = str_replace("/h2>", "/h2><p>", $content);
		$content = str_replace("/h3>", "/h3><p>", $content);
		$content = str_replace("/h4>", "/h4><p>", $content);
		$content = str_replace("/h5>", "/h5><p>", $content);
		$content = str_replace("<span><span>", "", $content);
		$content = str_replace("</span></span>", "", $content);
		$content = str_replace("<br/><p>", "<p>", $content);
		$content = str_replace("</p><br/>", "</p>", $content);

		$content = preg_replace("/<[^\/>]*><\/[^>]*>/", '', $content);
		$content = preg_replace("/<[^\/>]*>([\s]?)*<\/[^>]*>/", '', $content);
		$content = preg_replace("/<[^\/>]*>&nbsp;<\/[^>]*>/", '', $content);
		$content = preg_replace("/<span style=\"(.*)\">(.*)(<\/span>)/", "$2", $content);

		$content = str_replace("<p><br/>", "<p>", $content);

		if (substr($content, 0, strlen("<br/>")) == "<br/>")
			$content = substr($content, strlen("<br/>"));

		//$content = str_replace('<div id="lipsum"><p>', '', $content);
		//$content = str_replace('</p></div>', '', $content);
		$content = str_replace('<!--[if !mso]>  <mce:style>', '', $content);
		
		if (substr($content, 0, 2) != '<p')
			$content = "<p>" . $content;
		if (substr($content, -4) != '</p>')
			$content = $content . "</p>";
		
		$content = format_links($content);
		return valid($content);
	} // format_text
	
	function get_ext($filename) {
		// if no ., then no extension
		$filename = strtolower($filename);
		if (strpos($filename, '.') === false) return false;
		// remove anything # or after
		if (strpos($filename, "#") !== false)
			$filename = substr($filename, 0, strpos($filename, '#'));
		// remove anything ? or after
		if (strpos($filename, "?") !== false)
			$filename = substr($filename, 0, strpos($filename, '?'));
		// if ends in /, no extension
		if (substr($filename,-1) == '/') return false;
		$parts = explode('.', $filename);
		$ext = $parts[count($parts)-1];
		if (in_array($ext, array('html','htm','shtml','php','css','js','asp','gif','jpg','jpeg','png','tiff','pdf','txt', 'ico')))
			return $ext;
		return false;
	} // get_ext
	
	function get_file($filename) {
		if (function_exists('file_get_contents')) {
			// try file_get_contents
			return file_get_contents($filename);
		} else if (function_exists('curl_init')) {
			// try curl
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $filename);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
			//curl_setopt($ch, CURLOPT_TIMEOUT, 8);
			return curl_exec($ch);
		} else if ($data = @readfile($filename)) {
			return $data;
		} else if ($fp = fopen($filename,'r')) {
			//try fopen
			$data = '';
			while(!feof($fp)){
				$data = $data . fread($fp, 1024);
			}
			fclose($fp);
			return $data;
		}
		return false;
	} // get_file
	
	// taken from http://us2.php.net/manual/en/function.htmlentities.php#34449
	function htmlentities2($myHTML) {
	   $translation_table=get_html_translation_table (HTML_ENTITIES,ENT_QUOTES);
	   $translation_table[chr(38)] = '&';
	   return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&amp;" , strtr($myHTML, $translation_table));
	} // htmlentities2
	
	function idize($arrOrig, $field = false) {
		$arrNew = array();
		foreach ($arrOrig as $arr) {
			$value = ($field && isset($arr[$field])) ? $arr[$field] : $arr;
			if (isset($arr['id'])) {
				$arrNew[$arr['id']] = $value;
			} else {
				$arrNew[] = $value;
			}
		}
		return $arrNew;
	} // idize
	
	function is_valid_email($email) {
		// First, we check that there's one @ symbol, and that the lengths are right
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	} // is_valid_email
	
	// http://us3.php.net/manual/en/function.json-encode.php#82904
	if (!function_exists('json_encode')) {
		function json_encode($a=false) {
			if (is_null($a)) return 'null';
			if ($a === false) return 'false';
			if ($a === true) return 'true';
			if (is_scalar($a)) {
				if (is_float($a)) {
					// Always use "." for floats.
					return floatval(str_replace(",", ".", strval($a)));
				}

				if (is_string($a)) {
					static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
					return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
				} else
					return $a;
			}
			$isList = true;
			for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
				if (key($a) !== $i) {
					$isList = false;
					break;
				}
			}
			$result = array();
			if ($isList) {
				foreach ($a as $v) $result[] = json_encode($v);
				return '[' . join(',', $result) . ']';
			} else {
				foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
				return '{' . join(',', $result) . '}';
			}
		} 
	} // json_encode
	
	if (!function_exists('mime_content_type')) {
		function mime_content_type($filename) {
			if (function_exists('finfo_open')) {
				$finfo    = finfo_open(FILEINFO_MIME);
				$mimetype = finfo_file($finfo, $filename);
				finfo_close($finfo);
				return $mimetype;
			} else {
				return trim(exec('file -bi ' . escapeshellarg($filename)));
			}
	    }
	} // mime_content_type
	
	function paths_to_array($str) {
		$array = array();
		$lines = explode("\n", trim($str));

		if (!empty($lines[0])) {
			foreach($lines as $line) {
				list($path, $value) = explode(' = ', $line);
				$steps = explode('/', $path);
				array_shift($steps);
				$insertion =& $array;
				foreach($steps as $step) {
					if (!isset($insertion[$step])) {
						$insertion[$step] = array();
					}
					$insertion =& $insertion[$step];
				}
				$insertion = $value;
			}
		}
		return $array;
	} // /paths_to_array
	
	function pluralization($num = 0, $words) {
		if (!is_numeric($num)) $num = 0;
		return $num . " " . (($num == 1) ? singularize($words) : pluralize($words));  
	} // pluralization
	
	function pluralize($word) {
		if (($word != "") && (!in_array(strtolower($word), array('music', 'blog')))) {
			$word = (substr($word, -1) == "s") ? $word : $word . "s";
			$word = ((substr($word, -2) == "ys") && (!in_array(substr($word, -3, 1),array('a','e','i','o','u')))) ? substr($word, 0, -2) . 'ies' : $word;
		}
		return $word;
	} // pluralize
		
	function redirect($url) {
		global $db;
 		
		if ((substr($url,-1) != '/') && (strpos($url, "#") === false) && (strpos($url, "?") === false) && !get_ext($url))
			$url .= "/";
		
		$loc = get_location();
		
		if ((substr($url, 0, 7) != 'http://') && (substr($url, 0, strlen($loc)) !== 
		$loc)) {
			$url = $loc . $url;
		}

		if (isset($db)) $db->close(); // this might be already taken care of with register_shutdown_function
		header("Location: " . str_replace("\'", "'", $url));
		exit;
	} // /redirect
	
	function scrape($content, $startCode, $endCode = false) {
		if (strpos($content, $startCode) !== false) {
			$start = strpos($content, $startCode)+strlen($startCode);
			if (is_string($endCode)) {
				$end = strpos(substr($content, $start), $endCode);
				$content = substr($content, $start, $end);			
			} else {
				$content = substr($content, $start);
			}
		}
		return $content;
	} // strape
		
	function singularize($word) {
		if (($word != "") && (!in_array(strtolower($word), array('news')))) {
			$word = (substr($word, -1) == "s") ? substr($word,0,-1) : $word;
			$word = ((substr($word, -2) == "ie") && (strtolower($word) != 'movie')) ? substr($word,0,-2) . "y" : $word;
		}
		return $word;
	} // singularize
	
	function str_replace_once($search, $replace, $subject) {
		$str_new = $subject;
 		if (substr_count($subject, $search) > 0) {
			$str_pre = substr($subject, 0, strpos($subject, $search));
			$str_post = substr($subject, strpos($subject, $search)+strlen($search));
			//$str_core = substr(substr($subject, strlen($str_pre)), 0, strlen($str_post));
			$str_new = $str_pre . $replace . $str_post;
			unset($str_pre);
			unset($replace);
			unset($str_post);
		}
		return $str_new;
	} // str_replace_once
	
	function strip_ext($filename) {
		if (is_array($filename)) {
			foreach ($filename as $key => $file) {
				$filename[$key] = strip_ext($file);
			}
		} else {
			if (strrpos($filename, ".") !== false) {
				$ext = get_ext($filename);
				if (in_array($ext, array('html','htm','shtml','php','css','js','asp','gif','jpg','jpeg','png','tiff','pdf','txt'))) {
					return substr($filename, 0, strrpos($filename, "."));
				}
			}
		}
		return $filename;
	} // strip_extension
	
	function tagstolower($html) {
		//return preg_replace("/(<[^>]+>)/ies", "strtolower('$1')", $html);
		return preg_replace_callback("/(<[^>]+>)/i", "tagtolower", $html);
	} // tagstolower
	
	function tagtolower($tag) {
		$pieces = explode(' ', $tag[0]);
		foreach ($pieces as $key => $piece) {
			if (substr($piece, 0, 1) == '<') {
				$pieces[$key] = strtolower($piece);			
			} else if (substr($piece, -1) == '>') {
				$ps = explode('"', $piece);
				$ps[count($ps)-1] = strtolower($ps[count($ps)-1]);
				$pieces[$key] = join('"', $ps);
			} else if (strpos($piece, '="') !== false) {
				$ps = explode('="', $piece);
				$ps[0] = strtolower($ps[0]);
				$pieces[$key] = join('="', $ps);
			}
		}
		return join(" ", $pieces);
		/*if (preg_match("/<([^>]+)(\s\w+)=([^>]+)>/i", $Matches[1], $NewMatch)) {
			return "<" . strtolower($NewMatch[1]) . strtolower($NewMatch[2]) . "=" . $NewMatch[3] . ">";
		} else {
			return strtolower($Matches[1]);
		}*/
	} // tagtolower
	
	function titlize($string = '') {
		return ucwords(valid(str_replace('-', ' ', $string)));
	} // titlize

	function trim_explode($separator, $string, $trim = NULL) {
		if (empty($string)) return array();
		$items = explode($separator, $string);
		if (!isset($trim) || !is_string($trim)) $trim = " \t\n";
		for($i=0; $i<count($items); $i++) {
			$items[$i] = trim($items[$i], $trim);
		}
		return $items;
	} // /trim_explode
	
	function truncate($string, $length, $words = false) {
		if ($words) {
			$words = trim_explode(' ', $string);
			if (count($words) > $length) {
				$wordsToReturn = array();
				for ($i=0; $i<$length; $i++) 
					$wordsToReturn[] = $words[$i];
				return join(' ', $wordsToReturn);
			}
		} else if (strlen($string) > $length) {
			$short_string = substr($string, 0, $length-3);
			$string = substr($short_string, 0, strrpos($short_string, " ")) . "...";

			if ((strrpos($string, "<") !== false) && (strrpos(substr($string,strrpos($string,"<")), ">") === false)) 	
				$string = substr($string, 0, strrpos($string,"<")) . "...";
		}
		return $string;			
	} // /truncate
	
	// inspired by http://us.php.net/manual/en/function.file-exists.php#79118
	function url_exists($url) {
		if (function_exists('curl_init')) {
			// Version 4.x supported
			$handle   = curl_init($url);
			if (false === $handle)
				return false;
			curl_setopt($handle, CURLOPT_HEADER, false);
			curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
			curl_setopt($handle, CURLOPT_NOBODY, true);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
			$connectable = curl_exec($handle);
			curl_close($handle);   
			return $connectable;
		} else if (@readfile($url)) {
			return true;
		} else if ($fp = fopen($url, 'r')) {
			return $fp;
		}
		return false;
	} // url_exists

	function urlFriendly($string){ return url_friendly($string); } // alias
	function url_friendly($string) {
		$string = str_replace(array("/", " ", "&amp;"), "-", $string);
		$url = preg_replace("/[^a-zA-z0-9-\/ ]/i", '', html_entity_decode($string));
		while (strpos($url, '--') !== false)
			$url = preg_replace("/--/i", "-", $url);
		return trim(urlencode($url), "\n\t _-");
	} // /url_friendly
	
	function url_friendly_old($string) {
		return urlencode(str_replace(array("/", "&amp;", "&", "+", '.', "?", "'", '"'), " ", clean($string)));
	} // url_friendly_old
	
	// based on: http://us.php.net/manual/en/function.get-defined-functions.php#84366
	function user_func_exists($function_name = false) {
		if (!is_string($function_name)) return false;
		if (!get_var('user_functions')) {
		    $func = get_defined_functions();
		    $user_func = array_flip($func['user']);
		    unset($func);
			set_var('user_functions', $user_func);
		}
		$user_func = get_var('user_functions');
	    return (isset($user_func[$function_name]));
	} // user_func_exists
	
	function valid($string) {
		//return preg_replace("/&amp;(#[0-9]+|[a-z]+);/i", "&$1;", htmlspecialchars($string));
		$string = str_replace("&", "&amp;", $string);
		return preg_replace("/&amp;(#[0-9]+|[a-z]+);/i", "&$1;", $string);
	} // /valid
	
?>