<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: PasswordForm.php 1296 2009-07-08 04:19:14Z vipsoft $
 * 
 * @package Piwik_Login
 */

/**
 * 
 * @package Piwik_Login
 */
class Piwik_Login_PasswordForm extends Piwik_Form
{
	function __construct()
	{
		parent::__construct();
		// reset 
		$this->updateAttributes('id="loginform" name="loginform"');
	}
	
	function init()
	{
		$urlToGoAfter = Piwik_Common::getRequestVar('form_url', 'index.php', 'string');
			
		$formElements = array(
			array('text', 'form_login'),
			array('hidden', 'form_url', $urlToGoAfter),
		);
		$this->addElements( $formElements );
		
		$formRules = array(
			array('form_login', sprintf(Piwik_Translate('General_Required'), Piwik_Translate('Login_LoginOrEmail')), 'required'),		
			array('hidden', 'form_url', $urlToGoAfter),			
		);
		$this->addRules( $formRules );	
		
		$this->addElement('submit', 'submit');	
	}
}
