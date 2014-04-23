<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: CoreAdminHome.php 1270 2009-07-01 06:53:34Z vipsoft $
 * 
 * @package Piwik_CoreAdminHome
 */
 

class Piwik_CoreAdminHome extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'Administration area',
			'description' => 'Administration area of Piwik.',
			'author' => 'Piwik',
			'homepage' => 'http://piwik.org/',
			'version' => '0.1',
		);
	}
}

