<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: CorePluginsAdmin.php 1270 2009-07-01 06:53:34Z vipsoft $
 * 
 * @package Piwik_CorePluginsAdmin
 */

class Piwik_CorePluginsAdmin extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'Plugins Management',
			'description' => 'Plugins Administration Interface.',
			'author' => 'Piwik',
			'homepage' => 'http://piwik.org/',
			'version' => '0.1',
		);
	}
	
	function getListHooksRegistered()
	{
		return array('AdminMenu.add' => 'addMenu');
	}
	
	function addMenu()
	{
		Piwik_AddAdminMenu(Piwik_Translate('CorePluginsAdmin_MenuPlugins'), array('module' => 'CorePluginsAdmin', 'action' => 'index'));		
	}
}


