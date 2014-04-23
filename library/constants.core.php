<?php

/*
	Server Folder Structure Related Constants Variables
*/
	
	define('PATH', dirname($_SERVER['SCRIPT_FILENAME']) . "/");
	define('ABSPATH', dirname(dirname(__FILE__))."/");

	define('SERVER', str_replace("www.", "", $_SERVER['SERVER_NAME']));

	define('ADMIN', ABSPATH . 'admin/');
	define('LIBRARY', ABSPATH . 'library/');
	define('CONFIG', ABSPATH . 'config/');
	define('LOGS', ABSPATH . 'logs/');
	define('DOCS', ABSPATH . 'docs/');
	define('CACHE', ABSPATH . 'cache/');
	define('PLUGINS', ABSPATH . 'plugins/');
	define('SITES', ABSPATH . 'sites/');
	define('UPLOADS', ABSPATH . 'uploads/');
	
?>