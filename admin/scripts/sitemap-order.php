<?php

	// accepts parent_id, and a , children_ids (a delimated string for the new order of children)
	$parent_id = $db->escape($_POST['parent_id']);
	$children_ids = trim_explode(',', $db->escape($_POST['children_ids']));
	$children_ids = array_unique($children_ids);
	
	if (!is_numeric($parent_id))
		$parent_id = 0;
	
	$noErrors = true;
	
	$order = 1;
	foreach ($children_ids as $child_id) {
		
		if ($noErrors) {
			$noErrors = $db->update('sitemap', $child_id, array('parent_id' => $parent_id, 'order' => $order));
			$order++;
		}
		
	}
	
	if ($noErrors) {
		delete_sitemap_cache();
		flush_memcache();
		echo 'true';
	} else {
		echo 'There was an error updating the order of your sitemap in the database. Changes have not been saved.';
	}
	
?>