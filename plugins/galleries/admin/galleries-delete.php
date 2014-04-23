<?php

	$id = request('id');
	
	if (is_numeric($id)) {
		if ($db->delete('table=>galleries', 'id=>' . $db->escape($id))) {
			success("Your gallery was successfully deleted.");
		} else {
			failure("Sorry, but there was a problem deleting this gallery.");
		}
	} else {
		failure("Invalid script call. Sorry.");
	}

	redirect_failure();
	
?>