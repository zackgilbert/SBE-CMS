<?php

	$type = get_var('type');
	$filename = get_var('file');
	
	if (is_file(LOGS . $type . '/' . $filename)) {
		
		if (unlink(LOGS . $type . '/' . $filename)) {
			
			success("The file ($type/$filename) has successfully been deleted.");
			redirect(LOCATION . 'admin/settings/tools/');
			
		} else {

			failure("We could not delete the file ($type/$filename). This is typically due to a permissions error. Try manually deleting the file.");
			redirect_failure();
			
		}
		
	} else {
		
		failure("We could not find the file you were looking for. Try manually deleting the file.");
		redirect_failure();

	}

?>