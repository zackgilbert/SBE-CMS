<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Widgetize.php 1270 2009-07-01 06:53:34Z vipsoft $
 * 
 * @package Piwik_Widgetize
 */

class Piwik_Widgetize extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'Widgetize your data!',
			'description' => 'The plugin makes it very easy to export any Piwik Widget in your Blog, Website or on Igoogle and Netvibes!',
			'author' => 'Piwik',
			'homepage' => 'http://piwik.org/',
			'version' => '0.1',
		);
	}
}
