<?php

	require_once LIBRARY . 'functions.editable.php';
	
	// load a previous version of a page...
	$version_id = get_var('id');
	$version = $db->getOne('table=>versions', 'id=>' . $version_id);
	$section = get_sitemap_section($version['sitemap_id']);
	
	// things we need...
	// the previous version, the sitemap section the page is in, template, page
	$routes = get_route_vars(str_replace_once(LOCATION, "", get_sitemap_section_url($section['id'])));
	set_vars($routes);
	// then we need to load the old version in the template (if there is one)...
	set_sitemap_levels($routes['params']);
	
	$levels = get_var('levels');
	$level = get_var('level');
	$params = get_var('params');
	
	// set template (if empty, then false, if 'DEFAULT' then use DEFAULT_TEMPLATE)
	if (empty($level['template'])) :
		$template = false;
	elseif ($level['template'] == 'DEFAULT') :
		$template = DEFAULT_TEMPLATE;
	else :
		$template = $level['template'];
	endif;
	
	$page = get_prefix();
	
	set_var('defaults', 'default');	
	set_var('folder', get_site());
	set_var('template', $template);
	set_var('page', $page);
	
	$versions = previous_edits($section);
	if (count($versions) > 0) {
		$select_html = '<select onchange="if (this.value>0) window.location.href=\'' . LOCATION . 'admin/pages/versions/\'+this.value+\'/\';">' . "\n";
		$select_html .=	'	<option value="0">&nbsp;</option>' . "\n";
		for ($i=count($versions)-1; $i>=0; $i--) :
			$select_html .= '<option value="' . $versions[$i]['id'] . '"' . (($versions[$i]['id'] == $version_id) ? ' selected="selected"' : '') . '>v.' . pad($i+1) . '</option>' . "\n";
		endfor; 
		$select_html .= '</select>' . "\n";
	} else {
		$select_html = "No previous versions found.";
	}
	
	$page_code = $version['content'];
	$page_code = $page_code . "\n\n" . sprintf(get_file(ABSPATH . 'admin/includes/editable-rollback.php'), LOCATION . "admin/pages/" . $section['id'] . "/", LOCATION . "admin/pages/rollback/" . $version_id . "/", $select_html);
	
	set_var('page_code', $page_code);
	
	add_presentation(ABSPATH . 'admin/stylesheets/pages-versions.css');
		
	include_once 'sites/' . get_site() . '/' . get_theme() . '/templates/' . $template . '.php';
		
	//dump($section);
	//dump($version);

?>