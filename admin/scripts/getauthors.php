<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/getauthors.php
	**	Creation Date: 5/8/08
	**	Description: Get all current authors from the database
	**	Called From: admin/Flint/includes/articles-edit.php & other content-edit pages...
	**
	**********************************************
	*/


	// accepts 1 possible parameter: format (option, json)
	$format = (isset($_REQUEST['format'])) ? $_REQUEST['format'] : 'option';
	
	
	if (is_ajax() && is_admin() && in_array($format, array('option', 'json'))) {
	
		$authors = $db->get("SELECT * FROM `users` WHERE (`types` = 'admins') OR (`types` = 'editors') OR (`types` = 'authors') AND (`deleted_at` IS NULL) ORDER BY `name` ASC");
		
		if ($format == 'option') {
			
			foreach($authors as $author) {
				
				echo '<option value="' . $author['id'] . '">' . valid($author['name']) . '</option>';
				
			}
			
		} else if ($format == 'json') {
			
			// need to build this out...
			
		}
		
	}

?>