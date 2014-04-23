<?php

	/*
	**********************************************
	**
	**	File: admin/default/scripts/account-save.php
	**	Creation Date: 9/23/08
	**	Description: Updates an admin user's account info
	**	Called From: admin/default/includes/account.php
	**
	**********************************************
	*/
	clear_field_values();
	clear_required_fields();
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'user';
	include(LIBRARY . 'handlePostVars.php');
	
	// not needed... info is already stored in user()
	//$current_user = $db->getOne('table=>users', "id=>" . $db->escape(user('id')));
	
	$new_user = array('id' => user('id'));
	
	// Email
	if ($user['email'] !== user('email')) {

		if (!is_valid_email($user['email'])) {
			
			$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
			$_SESSION['requiredFields'] = array('user[email]');
			failure("The email address you supplied wasn't valid. Please provide a valid one.");
			redirect_failure();
			
		} else {
			
			$u = $db->get('table=>users', "where=>(`email` = '" . $db->escape($user['email']) . "') AND (`deleted_at` IS NULL)");

			if (is_array($u) && (count($u) > 0)) {

				$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
				$_SESSION['requiredFields'] = array('user[email]');
				failure('This email is already being used. Please choose another.');
				redirect_failure();

			} else {

				$new_user['email'] = $user['email'];

			}
			
		}
		
	}
	
	//  Name
	if ($user['name'] !== user('name')) {
		
		$new_user['name'] = $user['name'];
		
	}
	
	// Password
	if (isset($user['manage_password']) && !empty($user['manage_password'])) {
		
		if ($user['manage_password'] != $user['manage_password_confirm']) {
			
			$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
			$_SESSION['requiredFields'] = array('user[manage_password]');
			failure('Your passwords do not match.');
			redirect_failure();
			
		} else {
		
			// hack to fix non-static calls in php5
			// $users['password'] = user::encryptPassword($users['password']);
			$u = new user();
			$new_user['password'] = $u->encryptPassword($user['manage_password']);
			
		}
		
	}

	
	// save to users table
	if ($db->save('table=>users', $new_user)) {
		//$user['id'] = $db->last_id;

		//$usr = new user();
		//$usr->login($users['email'], $tmp_pass);
		$usr = get_user();
		foreach ($new_user as $field => $value)
			$usr->$field = $value;
		$usr->sessionUpdate();
		
		success('Your account has been successfully updated.');	
		redirect_success();
		
	} else {
		
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error updating your account.');
		redirect_failure();
		
	}
	
?>