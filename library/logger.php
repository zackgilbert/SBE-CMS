<?php

	// we'll do our own error handling.
	//error_reporting(0);

	// this function will handle all our errors.
	function customHandler ($errno, $errstr, $errfile, $errline, $errcontext) {
		global $log;
		
		if (!is_object($log))
			$log = new logger(ABSPATH . "logs/errors", DEBUG);
		
		$errortype = array (
		               E_ERROR				=> 'Error',
		               E_WARNING			=> 'Warning',
		               E_PARSE				=> 'Parsing Error',
		               E_NOTICE				=> 'Notice',
		               E_CORE_ERROR			=> 'Core Error',
		               E_CORE_WARNING		=> 'Core Warning',
		               E_COMPILE_ERROR		=> 'Compile Error',
		               E_COMPILE_WARNING	=> 'Compile Warning',
		               E_USER_ERROR			=> 'User Error',
		               E_USER_WARNING		=> 'User Warning',
		               E_USER_NOTICE		=> 'User Notice'
		               );
		// log the error through our logger class.
		if (isset($errortype[$errno])) {
			$log->log("<b>$errortype[$errno]:</b> [$errno] $errstr in<br />
$errfile on line $errline", $errcontext);
		} else {
			$log->log("<b>Unknown Error:</b> [$errno] $errstr in<br />
$errfile on line $errline", $errcontext);
		}
		return true;
	} // customHandler

	class logger {

		var $ROOT		=	"";
		var $WIN32		=	false;
		var $DEBUG		=	false;

		function logger ( $logroot, $debug = false) {
			if ($debug) $this->setDebug($debug);
			if(empty($logroot)) {
				if($this->DEBUG) {
					error_log("[ErrorLog]: need logroot to initialize",0);
				}
				return;
			}

			$this->setRoot($logroot);

			return;
		} // logger constructer

		function log ($l, $errcontext = '') {
			if (PRODUCTION) return;	// don't log when in production mode... slows things down.
			
			$log = "
<p>
" . NOW . " - PHP " . PHP_VERSION . " (" . PHP_OS . ")<br/>
" . $l . "<br/>\n";

			if (isset($_SERVER['REQUEST_URI'])) $log .= "CURRENT PAGE: " . $_SERVER['REQUEST_URI'] . "<br/>\n";
			if (isset($_SERVER['HTTP_USER_AGENT'])) $log .= "USER AGENT: " . $_SERVER['HTTP_USER_AGENT'] . "<br/>\n";
			if (isset($_SERVER['HTTP_REFERER'])) $log .= "REFERER: " . $_SERVER['HTTP_REFERER'] . "<br/>\n";
			if (isset($_GET) && is_array($_GET) && (count($_GET) > 1)) $log .= "GET: " . var_export($_GET,true) . "<br/>\n";
			if (isset($_POST) && is_array($_POST) && (count($_POST) > 0)) $log .= "POST: " . var_export($_POST,true) . "<br/>\n";
			$log .= "
</p>";
			
			error_log(strip_tags(stripslashes($log)), 3, $this->ROOT . TODAY . ".log");
			chmod($this->ROOT . TODAY . ".log", 0666);
			
			if ($this->DEBUG) {
				echo $log . "
<br/>";
			}

			return;
		} // log
		
		function setDebug($debug) {
			$this->DEBUG = $debug;
		} // setDebug

		function setRoot ($root) {
			if(!$this->WIN32) {
				if(!ereg("\/$",$root)) {
					$root = "$root"."/";
				}
				if(is_dir($root)) {
					$this->ROOT = $root;
				} else {
					$this->ROOT = "";

					if($this->DEBUG) {
						error_log("Specified ROOT dir [$root] is not a directory", 0);
					}
				}
			} else {
				// WIN32 box - no test
				if(!ereg("\\$",$root)) {
					$root = "$root"."\\";
				}

				$this->ROOT = $root;
			}

		} // setRoot

	} // logger
	
	global $log;
	$log = new logger(ABSPATH . "logs/errors", DEBUG);			// create a new instance of the logger object
	set_error_handler("customHandler");					// set error handler to my custom one.
	
	function error($message, $file, $function, $line) {
		global $log;
		$GLOBALS['flint']['errors'][] = array("time" => NOW, "message" => $message, "file" => $file, "function" => $function, "line" => $line);
		$error = "There was a Flint program error that occurred: " . $message . "\nFile: " . $file . "\nFunction: " . $function . "\nLine: " . $line;
		$log->log($error);
		return false;
	} // error
	
	function warning($message, $file, $function, $line) {
		global $log;
		$GLOBALS['flint']['warnings'][] = array("time" => NOW, "message" => $message, "file" => $file, "function" => $function, "line" => $line);
		$error = "There was a Flint program warning that occurred: " . $message . "\nFile: " . $file . "\nFunction: " . $function . "\nLine: " . $line;
		$log->log($error);
		return false;
	} // warning
	
?>