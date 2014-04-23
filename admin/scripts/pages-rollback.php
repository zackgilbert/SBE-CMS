<?php

	$version_id = get_var('id');
	$version = $db->getOne('table=>versions', 'id=>' . $version_id);
	$section = get_sitemap_section($version['sitemap_id']);
	
	if (!is_numeric($version_id) || !is_array($version) || !is_array($section)) {
		failure("We were unable to find the page you were looking for. This could be due to trying to accessing this page incorrectly.");
		redirect_failure();
	}

	// version the current page	
	$filename = get_page_file_location($section);
	
	$errors = array();
	
	$currentVersion = get_file($filename);
	
	// save a version of current content into database...
	$versioned = $db->insert('table=>versions', array('sitemap_id' => $version['sitemap_id'], 'filename' => $version['filename'], 'content' => $currentVersion, 'created_by' => user('id')));
	
	if (!$versioned) {
		$errors[] = "There was an error saving the previous version of this page to the database.";
	}
	
	// rollback page to versioned content
	if (file_put_contents($filename, $version['content'])) {
		if (count($errors) < 1) {
			success("Page was successfully rollbacked.");
			redirect(LOCATION . 'admin/pages/' . $version['sitemap_id']);			
		} else if (count($errors) == 1) {
			failure($errors[0]);
			redirect_failure();
		} else {
			failure("There were multiple errors that occurred while trying to rollback this page.");
			redirect_failure();
		}
	} else {
		failure("There was an error rolling back this page to the previous version. This is typically a permissions issue. Please make sure your page files have a permissions setting of 777.");
		redirect_failure();		
	}

?>