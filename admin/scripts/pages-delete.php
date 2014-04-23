<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/pages-delete.php
	**	Creation Date: 09/17/09
	**	Description: Delete a sitemap section from database
	**	Called From: admin/Flint/pages/pages-delete.php
	**
	**********************************************
	*/

	$id = $db->escape(request('id'));
	
	if (delete_sitemap_section($id)) {
		echo 'true';
	} else {
		echo "Attempting to delete the page (" . $id . ") failed.";
	}

?>