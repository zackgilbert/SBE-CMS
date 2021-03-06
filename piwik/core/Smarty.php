<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Smarty.php 1296 2009-07-08 04:19:14Z vipsoft $
 * 
 * @package Piwik
 */

require_once PIWIK_INCLUDE_PATH . '/libs/Smarty/Smarty.class.php';

class Piwik_Smarty extends Smarty 
{
	function trigger_error($error_msg, $error_type = E_USER_WARNING)
	{
		throw new SmartyException($error_msg);
	}
}

class SmartyException extends Exception {}
