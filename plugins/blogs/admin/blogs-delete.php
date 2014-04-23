<?php

	$id = request('id');
	
	if (is_numeric($id)) {
		if ($db->delete('table=>blogs', 'id=>' . $db->escape($id))) {
			$db->delete('table=>blog_sitemap', "where=>(`blog_id` = " . $db->escape($id) . ")");
			success("Your blog was successfully deleted.");
		} else {
			failure("Sorry, but there was a problem deleting this blog.");
		}
	} else {
		failure("Invalid script call. Sorry.");
	}

	redirect_failure();
	
?>