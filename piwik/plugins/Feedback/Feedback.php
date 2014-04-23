<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Feedback.php 1270 2009-07-01 06:53:34Z vipsoft $
 * 
 * @package Piwik_Feedback
 */

class Piwik_Feedback extends Piwik_Plugin
{
	public function getInformation()
	{
		return array(
			'name' => 'Feedback',
			'description' => 'Send your Feedback to the Piwik Team in one click. Share your ideas and suggestions with us!',
			'author' => 'Piwik',
			'homepage' => 'http://piwik.org/',
			'version' => '0.1',
		);
	}
}
