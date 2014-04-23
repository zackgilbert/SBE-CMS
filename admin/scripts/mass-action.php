<?php

	// get variables needed for file...
	$type = request('type');
	$action = request('action');
	$ids = request('ids');
	
	// error check supplied variables...
	if (!$db->isTable($db->escape($type))) {
		echo "There was an error editing your items (type: " . $type . ").";
	} else if (!in_array('publish', 'unpublish', 'delete', 'undelete')) {
		echo "There was an error editing your items (action: " . $action . ").";
	} else if (!is_string($ids) || empty($ids)) {
		echo "There was an error editing your items. No items selected.";
	} else {
		$ids = trim_explode(',', $ids);
		
		if ($action == 'publish') {
			
			if ($db->update('table=>' . $db->escape($type), "where=>((`id` = " . join(") OR (`id` = ", $ids) . ")) AND (`published_at` IS NULL) AND (`deleted_at` IS NULL)", array('published_at' => NOW))) {
				echo 'true';
			} else {
				echo "There was an error publishing your selected items. Try again and if the problem persists, try individually publishing them.";
			}
			
		} else if ($action == 'unpublish') {
			
			if ($db->update('table=>' . $db->escape($type), "where=>((`id` = " . join(") OR (`id` = ", $ids) . ")) AND (`published_at` IS NOT NULL) AND (`deleted_at` IS NULL)", array('published_at' => NULL))) {
				echo 'true';
			} else {
				echo "There was an error un-publishing your selected items. Try again and if the problem persists, try individually un-publishing them.";
			}
			
		} else if ($action == 'delete') {
			
			if ($db->update('table=>' . $db->escape($type), "where=>((`id` = " . join(") OR (`id` = ", $ids) . "))  AND (`deleted_at` IS NULL)", array('deleted_at' => NOW))) {
				echo 'true';
			} else {
				echo "There was an error deleting your selected items. Try again and if the problem persists, try individually deleting them.";
			}
			
		} else if ($action == 'undelete') {
			
			if ($db->update('table=>' . $db->escape($type), "where=>((`id` = " . join(") OR (`id` = ", $ids) . "))  AND (`deleted_at` IS NOT NULL)", array('deleted_at' => NULL))) {
				echo 'true';
			} else {
				echo "There was an error un-deleting your selected items. Try again and if the problem persists, try individually un-deleting them.";
			}
			
		}
		
	}

?>