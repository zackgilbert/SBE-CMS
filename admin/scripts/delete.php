<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/delete.php
	**	Creation Date: 4/15/08
	**	Description: Delete an item from database
	**	Called From: admin/Flint/pages/manage.php
	**
	**********************************************
	*/

	// get $table and $id, as supplied to url
	$table = singularize(get_var('table'));
	$id = get_var('id');
	$wasSuccessful = false;
	
	$message = 'There was an error trying to delete this ' . $table . '.';
	
	require_plugin(get_var('table'));
	
	if (is_numeric($id) && function_exists('get_' . $table)) :
		$item = call_user_func('get_' . $table, $id);
		
		if (method_exists($item, 'wasFound')) :
		
			if ($item->wasFound()) :
				if ($item->delete()) :
					$wasSuccessful = true;
					$message = capitalize($table) . ' was successfully deleted.';
				else :
					$message = 'There was an error deleting the ' . $table . ' from the database.';
				endif;
			else :
				$message = "Couldn't locate $table.";
			endif;
		
		elseif (function_exists('delete_' . $table)) :

			if (call_user_func('delete_' . $table, $id)) :
				$wasSuccessful = true;
				$message = capitalize($table) . ' was successfully deleted.';				
			else :
				$message = 'There was an error deleting the ' . $table . ' from the database.';
			endif;
			
		else:
		
			$message = "Couldn't figure out how to delete " . $table . ".";
		
		endif;
		
	elseif ($db->isTable(get_var('table'))) :
		
		if ($db->update("table=>" . $db->escape(get_var('table')), "id=>" . $db->escape($id), array('deleted_at' => NOW))) :
			$wasSuccessful = true;
			$message = capitalize(str_replace("_", " ", $table)) . ' was successfully deleted.';				
		else :
			$message = 'There was an error deleting the ' . str_replace("_", " ", $table) . ' from the database.';
		endif;
		
	else :
	
		$message = 'Invalid information supplied. Can not delete ' . $table . '.';

	endif;
		
	if (is_ajax()) :
		if ($wasSuccessful) :
			echo 'true';
		else:
			echo $message;
		endif;
	else:
		if ($wasSuccessful) :
			success($message);
		else:
			failure($message);
		endif;
		redirect_success();
	endif;

?>