<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/comments-approve.php
	**	Creation Date: 9/30/08
	**	Description: Approve a comment.
	**	Called From: admin/Flint/includes/comments-resultview.php
	**
	**********************************************
	*/
	
	$comment_id = request('id');
	$approved = false;
	
	if (is_admin() && is_logged_in() && is_numeric($comment_id)) {
		
		$approved = $db->update('table=>comments', 'id=>' . $db->escape($comment_id), array('approved_at' => NOW));
		
		storage("kill_me");
		
		if (is_ajax()) {
			
			if ($approved) {
				echo 'true';
			} else {
				echo "There was an error approving this comment.";
			}
			
		} else {
			
			if ($approved) {
				success("Comment has successfully been approved.");
			} else {
				failure("There was an error approving this comment.");
			}
			redirect_failure();
			
		}
		
	} else {

		echo "Invalid script call.";

	}

?>