<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: function.sparkline.php 1296 2009-07-08 04:19:14Z vipsoft $
 * 
 * @package SmartyPlugins
 */

/**
 * @param string $url
 * @return string IMG HTML tag 
 */
function smarty_function_sparkline($params, &$smarty = false)
{
	$src = $params['src'];
	$width = Piwik_Visualization_Sparkline::getWidth();
	$height = Piwik_Visualization_Sparkline::getHeight();
	return "<img class=\"sparkline\" alt=\"\" src=\"$src\" width=\"$width\" height=\"$height\" />";
}
