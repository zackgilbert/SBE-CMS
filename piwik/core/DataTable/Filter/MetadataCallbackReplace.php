<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: MetadataCallbackReplace.php 1158 2009-05-31 11:31:14Z matt $
 * 
 * @package Piwik_DataTable
 */

/**
 * Replace a metadata value with a new value resulting 
 * from the function called with the metadata's value
 * 
 * @package Piwik_DataTable
 * @subpackage Piwik_DataTable_Filter 
 */
class Piwik_DataTable_Filter_MetadataCallbackReplace extends Piwik_DataTable_Filter_ColumnCallbackReplace
{
	public function __construct( $table, $metadataToFilter, $functionToApply, $functionParameters = null )
	{
		parent::__construct($table, $metadataToFilter, $functionToApply, $functionParameters);
	}

	protected function setElementToReplace($row, $metadataToFilter, $newValue)
	{
		$row->setMetadata($metadataToFilter, $newValue);
	}
		
	protected function getElementToReplace($row, $metadataToFilter)
	{
		return $row->getMetadata($metadataToFilter);
	}
}
