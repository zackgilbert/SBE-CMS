<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: ExampleAPI.php 1270 2009-07-01 06:53:34Z vipsoft $
 * 
 * @package Piwik_ExampleAPI
 */

class Piwik_ExampleAPI extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'Example API',
			'description' => 'Example Plugin: How to create an API for your plugin, to export your data in multiple formats without any special coding? Visit the <a href="index.php?module=API&action=listAllAPI#ExampleAPI">ExampleAPI example methods</a>.',
			'author' => 'Piwik',
			'homepage' => 'http://piwik.org/',
			'version' => '0.1',
		);
	}
}
