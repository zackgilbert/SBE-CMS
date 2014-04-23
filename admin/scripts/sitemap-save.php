<?php

	$sitemap = array();
	
	if (is_ajax()) {
		
		foreach ($_POST as $field => $value) {
			$sitemap[$field] = $db->escape($value);
		}
		
	} else {
		
		foreach (post('sitemap', array()) as $field => $value) {
			if (is_array($value) && is_numeric($field)) $value = join(',', $value);
			$sitemap[$field] = $db->escape($value);
		}
		
	}
	
	if (!isset($sitemap['site_id']))
		$sitemap['site_id'] = site_id();
	
	$wasSuccessful = $db->save('table=>sitemap', $sitemap);
	
	if ($wasSuccessful) {

		if (!isset($sitemap['id']) || ($sitemap['id'] == '0')) 
			$sitemap['id'] = $db->getInsertId();

		if (is_plugin($sitemap['type']) && !plugin_is_installed($sitemap['type'])) {
			$installed = install_plugin($sitemap['type']);

			if ($installed !== true) {
				failure("Changes were made but with errors: " . $installed);
			} else {
				success('Changes were successfully made.');	
			}
		} else {
			success('Changes were successfully made.');
		}
		
	} else {
		failure('Attempting to save sitemap section the database failed.');
	}
	
	delete_sitemap_cache();
	flush_memcache();
	
	/*if (is_ajax()) {

		if ($wasSuccessful) {

			$sitemap['subsections'] = array();
			load_include('sitemap-section', array('section' => $sitemap));

		} else {
			echo 'Attempting to save sitemap section to the database failed.';
		}
		
	} else {*/
				
		redirect_success();
		
	//}
	
?>