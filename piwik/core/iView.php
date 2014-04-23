<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: iView.php 1270 2009-07-01 06:53:34Z vipsoft $
 * 
 * @package Piwik
 */

/**
 * @package Piwik_Visualization
 */
interface Piwik_iView
{
	/**
	 * Outputs the data.
	 * @return mixed (image, array, html...)
	 */
	function render();
}
