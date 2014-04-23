<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/comments-unapprove.php
	**	Creation Date: 9/30/08
	**	Description: Unapprove a comment.
	**	Called From: admin/Flint/includes/comments-resultview.php
	**
	**********************************************
	*/
	
	$comment_id = request('id');
	$unapproved = false;
	
	if (is_admin() && is_logged_in() && is_numeric($comment_id)) {
		
		$unapproved = $db->update('table=>comments', 'id=>' . $db->escape($comment_id), array('approved_at' => NULL));

		storage("kill_me");
		
		if (is_ajax()) {
			
			if ($unapproved) {
				echo 'true';
			} else {
				echo "There was an error unapproving this comment.";
			}
			
		} else {
			
			if ($unapproved) {
				success("Comment has successfully been unapproved.");
			} else {
				failure("There was an error unapproving this comment.");
			}
			redirect_failure();
			
		}
		
	} else {

		echo "Invalid script call.";

	}

?>