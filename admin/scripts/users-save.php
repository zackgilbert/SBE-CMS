<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/users-save.php
	**	Creation Date: 9/9/08
	**	Description: Save user info to database
	**	Called From: admin/Flint/pages/users.php
	**
	**********************************************
	*/
	
	clear_field_values();
	clear_required_fields();
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'users';
	include(LIBRARY . 'handlePostVars.php');
	
	// password
	if (isset($users['password']) && !empty($users['password'])) {
		
		if ($users['password'] == $users['password_confirm']) {
			$pass = $users['password'];

			unset($users['password']);
			unset($users['password_confirm']);

			$users['password'] = encrypt_password($pass);			
		} else {
			
			$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
			failure("Please make sure your passwords match.");
			redirect_failure();
			
		}
		
	} else {
		unset($users['password']);
		unset($users['password_confirm']);
	}
	
	if (!isset($users['url']) || (strlen($users['url']) < 1))
		$users['url'] = url_friendly(strtolower($users['name']));
	
	if ($db->save('table=>users', $users)) {
		if (!isset($users['id']))
			$users['id'] = $db->last_id;
		
		$_POST['redirect'] = str_replace('/add', '/' . $users['id'], $_POST['redirect']);
	
		success("User's information has successfully been saved.");
		redirect_success();
	
	} else {
		
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error saving this user.');
		redirect_failure();

	}

?>