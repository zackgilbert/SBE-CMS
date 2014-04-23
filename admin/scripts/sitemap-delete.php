<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/sitemap-delete.php
	**	Creation Date: ??/??/08
	**	Description: Delete a sitemap section from database
	**	Called From: admin/Flint/pages/sitemap.php
	**
	**********************************************
	*/

	$id = $db->escape(get_var('id'));
	
	if (delete_sitemap_section($id)) {
		delete_sitemap_cache();
		flush_memcache();
		echo 'true';
	} else {
		echo "Attempting to delete the sitemap section (" . $id . ") failed.";
	}

?>