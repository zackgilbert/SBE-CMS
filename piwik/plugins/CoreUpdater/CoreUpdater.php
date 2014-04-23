<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: CoreUpdater.php 1270 2009-07-01 06:53:34Z vipsoft $
 * 
 * @package Piwik_CorePluginsAdmin
 */

class Piwik_CoreUpdater extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'Updater',
			'description' => 'Piwik updating mechanism',
			'author' => 'Piwik',
			'homepage' => 'http://piwik.org/',
			'version' => '0.1',
		);
	}
}

