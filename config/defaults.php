<?php
	
	/*****************************************************
	 ** Reserved Variables:
	 ** stylesheets, javascripts, plugins
	 ** template, page
	 ** requires_database || database, restricted (false || array("to" => user type, "login" => authorization page))
	 ** section, subsection, title ??
	 *****************************************************/
	

	$GLOBALS['defaults']['stylesheets'] = array('reset', 'message');
	$GLOBALS['defaults']['javascripts'] = array('http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js');

	$GLOBALS['defaults']['default'] = array();
	$GLOBALS['defaults']['default']['template'] = 'site';
	$GLOBALS['defaults']['default']['requires_database'] = false;
	$GLOBALS['defaults']['default']['restricted'] = false;
	$GLOBALS['defaults']['default']['section'] = false;
	$GLOBALS['defaults']['default']['subsection'] = false;
	$GLOBALS['defaults']['default']['title'] = false;
	$GLOBALS['defaults']['default']['stylesheets'] = array();
	$GLOBALS['defaults']['default']['javascripts'] = array();
	
	$GLOBALS['defaults']['admin'] = array();
	$GLOBALS['defaults']['admin']['folder'] = "admin";
	$GLOBALS['defaults']['admin']['template'] = 'site';
	$GLOBALS['defaults']['admin']['restricted'] = array('to' => array('admins', 'editors'), 'login' => 'admin/login');
	$GLOBALS['defaults']['admin']['section'] = false;
	$GLOBALS['defaults']['admin']['subsection'] = false;
	$GLOBALS['defaults']['admin']['stylesheets'] = array('reset', 'sheet');
	$GLOBALS['defaults']['admin']['javascripts'] = array('jquery-1.3.2.min', 'jquery-sheet');
	
	$GLOBALS['defaults']['private'] = array();
	$GLOBALS['defaults']['private']['template'] = false;
	$GLOBALS['defaults']['private']['page'] = "400";
	$GLOBALS['defaults']['private']['requires_database'] = false;
	