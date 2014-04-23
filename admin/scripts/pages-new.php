<?php

	clear_field_values();
	clear_required_fields();
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'page';
	include(LIBRARY . 'handlePostVars.php');
	

	//$page = request('page');
	
	if (!isset($page['name']) || empty($page['name'])) {
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error adding this page. No page name supplied.');
		redirect_failure();
	}
	
	if (!isset($page['url']) || empty($page['url'])) {
		$page['url'] = trim(str_replace(array(".", "?", "!", ";", ":", ",", '"'), "", str_replace(" ", "-", strtolower($page['name']))), "-");
	}
	
	// figure out order and parent_id...
	if ($page['where'] == 'child') {
		$page['parent_id'] = $page['section'];
	} else if (in_array($page['where'], array('before', 'after'))) {
		$section = get_sitemap_section($page['section']);
		$page['parent_id'] = $section['parent_id'];
		$sections = $db->get(sprintf("SELECT * FROM `sitemap` WHERE (`parent_id` = %d) AND (`deleted_at` IS NULL) ORDER BY `order`, `created_at` ASC", $section['parent_id']));
		$order = false;
		foreach ($sections as $sect) {
			if ($sect['id'] == $section['id']) {
				if ($page['where'] == 'before') {
					$page['order'] = $sect['order'];
					$db->update('table=>sitemap', 'id=>' . $sect['id'], array('order' => $sect['order']+1));
					$order = $sect['order']+1;
				} else {
					$page['order'] = $order = $sect['order']+1;
				}
			} else if (is_numeric($order)) {
				$db->update('table=>sitemap', 'id=>' . $sect['id'], array('order' => $order+1));				
			}
		}
	}
	
	unset($page['where']);
	unset($page['section']);
	
	if ($db->save('table=>sitemap', $page)) {
		if (!isset($page['id']))
			$page['id'] = $db->last_id;
		
		//$_POST['redirect'] = str_replace('/add', '/' . $page['id'], $_POST['redirect']);
		$_POST['redirect'] = LOCATION . "admin/pages/?" . $page['id'] . "#page-" . $page['id'];
	
		if (is_plugin($page['type']) && !plugin_is_installed($page['type'])) {
			$installed = install_plugin($page['type']);

			if ($installed !== true) {
				failure("Your page was created but with an error: " . $installed);
			} else {
				success("Your new page has successfully been added.");
			}
		} else {
			success("Your new page has successfully been added.");
		}
	
		delete_sitemap_cache();
		flush_memcache();
		
		//redirect_success();
		echo '<script type="text/javascript"> window.parent.location.href = "' . $_POST['redirect'] . '"; </script>';
	} else {
		
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error adding this page.');
		redirect_failure();

	}

?>