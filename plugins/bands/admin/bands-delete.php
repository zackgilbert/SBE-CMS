<?php

	$id = request('id');
	
	if (is_numeric($id)) {
		if ($db->delete('table=>bands', 'id=>' . $db->escape($id))) {
			success("Your band was successfully deleted.");
		} else {
			failure("Sorry, but there was a problem deleting this band.");
		}
	} else {
		failure("Invalid script call. Sorry.");
	}

	redirect_failure();
	
?>