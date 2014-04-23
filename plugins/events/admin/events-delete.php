<?php

	$id = request('id');
	
	if (is_numeric($id)) {
		if ($db->delete('table=>events', 'id=>' . $db->escape($id))) {
			success("Your event was successfully deleted.");
		} else {
			failure("Sorry, but there was a problem deleting this event.");
		}
	} else {
		failure("Invalid script call. Sorry.");
	}

	redirect_failure();
	
?>