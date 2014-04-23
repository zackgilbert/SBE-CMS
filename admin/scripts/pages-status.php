<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/pages-status.php
	**	Creation Date: 09/17/09
	**	Description: Enable/disable a sitemap section in database
	**	Called From: admin/Flint/pages/pages-delete.php
	**
	**********************************************
	*/

	$id = request('id');
	$status = $db->escape(request('status', 'enable'));
	
	if ($db->update("table=>sitemap", "id=>" . $db->escape($id), array('disabled_at' => (($status == 'enable') ? NULL : NOW)))) {
		delete_sitemap_cache();
		flush_memcache();
		echo 'true';
	} else {
		echo "Attempting to " . $status . " this page (" . $id . ") failed.";
	}

?>