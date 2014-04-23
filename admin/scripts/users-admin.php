<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/users-admin.php
	**	Creation Date: 9/9/08
	**	Description: Change the admin status of users in database
	**	Called From: admin/Flint/pages/users.php
	**
	**********************************************
	*/
	
	//{ user_id : id , add : true }
	
	if (is_ajax() && request('user_id')) {
		
		$user_id = request('user_id');
		$add = request('add');
		$remove = request('remove');
		
		$user = $db->getOne("table=>users", "where=>(`id` = " . $db->escape($user_id) . ") AND (`deleted_at` IS NULL)");
		
		if ($user && $remove) {
			
			if ($db->update('table=>users', "id=>" . $db->escape($user['id']), array('types' => ''))) {
				
				$db->delete('table=>admins', "where=>(`id` = " . $db->escape($user['id']) . ")");			
				echo 'true';
				
			} else {
				
				echo "There was an error removing this user from the staff.";
				
			}
			
		} else if ($user && $add) {
			
			if ($db->update('table=>users', "id=>" . $db->escape($user['id']), array('types' => 'admins'))) {
				
				if ($db->getOne('table=>admins', "id=>" . $db->escape($user['id']))) {
					$db->update('table=>admins', 'id=>' . $db->escape($user['id']), array('deleted_at' => NULL));
				} else {
					$db->insert('table=>admins', array('id' => $user['id']));
				}
				echo 'true';
				
			} else {
				
				echo "There was an error adding this user to the staff.";
				
			}
			
		}
		
	} else {
		
		echo 'Invalid script call.';
		
	}

?>