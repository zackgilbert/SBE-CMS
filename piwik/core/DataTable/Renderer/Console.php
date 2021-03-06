<?php
/**
 * Piwik - Open source web analytics
 * 
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html Gpl v3 or later
 * @version $Id: Console.php 1296 2009-07-08 04:19:14Z vipsoft $
 * 
 * @package Piwik_DataTable
 */

/**
 * Simple output
 * 
 * @package Piwik_DataTable
 * @subpackage Piwik_DataTable_Renderer
 */
class Piwik_DataTable_Renderer_Console extends Piwik_DataTable_Renderer
{
	protected $prefixRows = '#';
	
	function render()
	{
		return $this->renderTable($this->table);
	}
	
	function setPrefixRow($str)
	{
		$this->prefixRows = $str;
	}
	
	protected function renderDataTableArray(Piwik_DataTable_Array $tableArray, $prefix )
	{
		$output = "Piwik_DataTable_Array<hr>";
		$prefix = $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		foreach($tableArray->getArray() as $descTable => $table)
		{
			$output .= $prefix . "<b>". $descTable. "</b><br>";
			$output .= $prefix . $this->renderTable($table, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
			$output .= "<hr>";
		}
		$output .= "Metadata<br>";
		foreach($tableArray->metadata as $id => $metadata)
		{
			$output .= "<br>";
			$output .= $prefix . " <b>$id</b> <br>";
			foreach($metadata as $name => $value)
			{
				$output .= $prefix . $prefix . "$name => $value";
			}
		}
		$output .= "<hr>";
		return $output;
	}
	
	protected function renderTable($table, $prefix = "")
	{
		if($table instanceof Piwik_DataTable_Array)
		{
			return $this->renderDataTableArray($table, $prefix);
		}
		
		if($table->getRowsCount() == 0)
		{
			return "Empty table <br>\n";
		}
		
		static $depth=0;
		$output = '';
		$i = 1;
		foreach($table->getRows() as $row)
		{
			$dataTableArrayBreak = false;
			$columns=array();
			foreach($row->getColumns() as $column => $value)
			{
				if($value instanceof Piwik_DataTable_Array )
				{
					$output .= $this->renderDataTableArray($value, $prefix);
					$dataTableArrayBreak = true;
					break;
				}
				if(is_string($value)) $value = "'$value'";
				elseif(is_array($value)) $value = var_export($value, true);
				
				$columns[] = "'$column' => $value";
			}
			if($dataTableArrayBreak === true)
			{
				continue;
			}
			$columns = implode(", ", $columns);
			
			$metadata = array();
			foreach($row->getMetadata() as $name => $value)
			{
				if(is_string($value)) $value = "'$value'";
				elseif(is_array($value)) $value = var_export($value, true);
				$metadata[] = "'$name' => $value";
			}
			$metadata = implode(", ", $metadata);
			
			$output.= str_repeat($this->prefixRows, $depth) 
						. "- $i [".$columns."] [".$metadata."] [idsubtable = " 
						. $row->getIdSubDataTable()."]<br>\n";
			
			if($row->getIdSubDataTable() !== null)
			{
				$depth++;
				try{
					$output.= $this->renderTable( 
									Piwik_DataTable_Manager::getInstance()->getTable(
												$row->getIdSubDataTable()
											),
											$prefix . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
										);
				} catch(Exception $e) {
					$output.= "-- Sub DataTable not loaded<br>\n";
				}
				$depth--;
			}
			$i++;
		}
		
		return $output;
		
	}	
}
