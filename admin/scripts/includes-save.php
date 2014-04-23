<?php

	$file = post('file');
	$content = stripslashes(post('contents'));
	
	if (file_put_contents(ABSPATH . 'sites/' . $file, $content)) {
		success("Sidebar widget has successfully been updated.");
	} else {
		failure("There was an error saving the sidebar widget.");
	}

	redirect(LOCATION . 'admin/');

?>