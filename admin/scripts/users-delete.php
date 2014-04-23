<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/users-delete.php
	**	Creation Date: 9/5/08
	**	Description: Delete users to database
	**	Called From: admin/Flint/pages/users.php
	**
	**********************************************
	*/
	
	$id = get_var('id');
	$wasSuccessful = false;
	
	$message = "There was an error deleting user from the database.";

	$deleted = $user = $db->delete('table=>users', "id=>" . $db->escape($id));
		
	if ($deleted) {
		
		$message = "User was successfully deleted.";
		$wasSuccessful = true;
		
	}
	
	
	if (is_ajax()) {
		if ($wasSuccessful) {
			echo 'true';
		} else {
			echo $message;
		}
	} else {
		if ($wasSuccessful) {
			success($message);
		} else {
			failure($message);
		}
		redirect_success();
	}

?>