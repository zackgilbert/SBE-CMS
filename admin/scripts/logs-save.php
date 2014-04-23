<?php

	$type = get_var('type');
	$filename = get_var('file');
	$content = request('file', '');
	
	if (is_writable(LOGS . $type . '/' . $filename)) {

		if (!$handle = fopen($filename, 'w')) {
			failure("Could not open file ($type/$filename)");
			redirect_failure();
		}

	    if (fwrite($handle, $content) === FALSE) {
			failure("Could not write to file ($type/$filename).");
			redirect_failure();
	    }

		success("File was successfully saved.");
		fclose($handle);
	
		if (isset($_POST['continue'])) {
			redirect_failure();
		} else {
			redirect_success();
		}
		
	} else {
		failure("The log file ($type/$filename) could not be saved because the file is not writable.");
		redirect_failure();
	}
	
?>