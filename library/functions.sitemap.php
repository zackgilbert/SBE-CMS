<?php
	
	function build_page_location($section = false, $delimiter = ' / ') {
		$levels = get_sitemap_section_levels($section);
		$page_location = '';
		foreach ($levels as $level) {
			// temp fix...
			if ($level['id'] > 1)
				$page_location = $page_location . $delimiter . valid($level['name']);
		}
		return trim($page_location, $delimiter);
	} // build_page_location
	
	function cache_sitemap($sitemapFlatArray) {
		$sitemapCache = serialize($sitemapFlatArray);
		
		return file_put_contents(CACHE . 'sitemap-' . site_id() . '.cache', $sitemapCache);
		//return false; // removed in favor of memcache
	} // cache_sitemap
	
	function delete_sitemap_cache() {
		foreach (glob(CACHE . 'sitemap*.cache') as $cache)
			unlink($cache);
		return true;
		//return deletecache(CACHE . 'sitemap.cache');
	} // delete_sitemap_cache
	
	function delete_sitemap_section($id) {
		global $db;

		if ($db->delete('table=>sitemap', 'id=>' . $db->escape($id))) :
			delete_sitemap_cache();
			flush_memcache();
			return true;//delete_sitemap_subsections($id);
		else :
			return error("Attempting to delete sitemap section (" . $id . ") failed.", __FILE__, __FUNCTION__, __LINE__);
		endif;
	} // delete_sitemap_section

	function delete_sitemap_subsections($parent_id) {
		global $db;
		
		if ($db->delete('table=>sitemap', "where=>(`parent_id` = " . $db->escape($parent_id) . ") AND (`deleted_at` IS NULL)", false)) :
			delete_sitemap_cache();
			return true;
		else :
			return error("Attempting to delete sitemap subsections for section (" . $id . ") failed.", __FILE__, __FUNCTION__, __LINE__);
		endif;
		
	} // delete_sitemap_subsections
	
	function find_sitemap_parent($parentSections = array(), $sectionDB) {

		foreach ($parentSections as $sectionID => $sectionValues) :
					
			if ($sectionID == $sectionDB['parent_id']) :
				$parentSections[$sectionID]['subsections'][$sectionDB['id']] = $sectionDB + array('subsections' => array());
			elseif (isset($parentSections[$sectionID])) :
				$parentSections[$sectionID]['subsections'] = find_sitemap_parent($parentSections[$sectionID]['subsections'], $sectionDB);			
			endif;

		endforeach;
	
		return $parentSections;
	} // find_sitemap_parent

	function get_sitemap() {
		if (get_var('sitemap') || get_sitemap_from_cache() || get_sitemap_from_memcache() || get_sitemap_from_database()) :
			set_memcache('sitemap-' . site_id(), get_var('sitemap'));
			return get_var('sitemap');
		endif;
		return error("Attempting to get site's sitemap failed.", __FILE__, __FUNCTION__, __LINE__);
	} // get_sitemap
	
	function get_sitemap_from_cache() {
		if (file_exists(CACHE . "sitemap-" . site_id() . '.cache')) {
			$serial = file_get_contents(CACHE . "sitemap-" . site_id() . '.cache');
			$sitemap = (is_string($serial)) ? unserialize($serial) : false;
			if (is_array($sitemap))
				set_var('sitemap', $sitemap);
			return $sitemap;
		}
		return false;
	} // get_sitemap_from_cache
	
	function get_sitemap_from_database() {
		global $db;

		$sitemapDB = $db->get('table=>sitemap', 'where=>(`site_id` = "' . site_id() . '") AND (`deleted_at` IS NULL)', 'order=>parent_id, `order` ASC');
		
		if ($sitemapDB) :
		
			$sitemap = array();
			/*foreach ($sitemapDB as $section_key => $sectionDB) :
				if ($sectionDB['parent_id'] == 0) :
					$sitemap[$sectionDB['id']] = $sectionDB + array('subsections' => (isset($sitemap[$sectionDB['id']]['subsections'])) ? $sitemap[$sectionDB['id']] : array());
				else :
					$sitemap = find_sitemap_parent($sitemap, $sectionDB);
				endif;
			endforeach;*/
			$refs = array();
			$list = array();
			foreach ($sitemapDB as $sectionDB) {
				$thisref = &$refs[ $sectionDB['id'] ];

				$sectionDB['subsections'] = (isset($thisref['subsections'])) ? $thisref['subsections'] : array();
				//$thisref['parent_id'] = $sectionDB['parent_id'];
				//$thisref['name'] = $sectionDB['name'];
				$thisref = $sectionDB;

				if ($sectionDB['parent_id'] == 0) {
					$list[ $sectionDB['id'] ] = &$thisref;
				} else {
					$refs[ $sectionDB['parent_id'] ]['subsections'][ $sectionDB['id'] ] = &$thisref;
				}
			}
			$sitemap = $list; // ?
			unset($list);
			unset($refs);
			
			set_var('sitemap', $sitemap);
			
			cache_sitemap($sitemap);			

			return $sitemap;

		endif;
		
		return warning("Attempting to get site's sitemap from the database failed.", __FILE__, __FUNCTION__, __LINE__);
	} // get_sitemap_from_database	
	
	function get_sitemap_from_memcache() {
		require_once(LIBRARY . 'memcache.php');
		if ($sitemap = get_memcache('sitemap-' . site_id()))
			set_var('sitemap', $sitemap);
		return $sitemap;
	} // get_sitemap_from_memcache
	
	function get_sitemap_root($urlParts = false) {
		$roots = get_sitemap_roots();
		$defaultRoot = current($roots);
		
		if (!$urlParts) 
			$urlParts = get_var('current_url');
		if (!is_array($urlParts) && is_string($urlParts))
			$urlParts = trim_explode('/', $urlParts);
			
		// figure out from the roots, which one fits with url
		foreach ($roots as $rootNum => $root) :
		
			if (isset($urlParts[0]) && ($root['url'] == $urlParts[0])) :
				return $root;
			elseif (!isset($urlParts[0]) && ($root['url'] == '')) :
				return $root;
			elseif ($root['url'] == '') :
				$defaultRoot = $root;
			endif;
			
		endforeach;
		
		return $defaultRoot;
	} // get_sitemap_root

	function get_sitemap_roots() {
		$sitemap = get_sitemap();
		$roots = array();
		
		foreach ($sitemap as $rootNum => $root) :
			$roots[$rootNum] = $root;
		endforeach;
		
		return $roots;
	} // get_sitemap_roots
	
	function get_sitemap_section($value, $field = 'id', $sitemap = false) {
		if (!$sitemap) $sitemap = get_sitemap();
		if (isset($sitemap['subsections']) && is_array($sitemap['subsections'])) $sitemap = $sitemap['subsections'];
		
		foreach ($sitemap as $section) :
		
			if ($section[$field] == $value) :
				return $section;
			else :
				$section = get_sitemap_section($value, $field, $section);
				if ($section) return $section;
			endif;

		endforeach;
		
		return false;
	} // get_sitemap_section
	
	function get_sitemap_section_level($section, $field = 'id', $level = 0) {
		if (!is_array($section)) $section = get_sitemap_section($section, $field);

		if ($section['parent_id'] != 0) :
			$level++;
			$level = get_sitemap_section_level($section['parent_id'], 'id', $level);
		endif;

		return $level;
	} // get_sitemap_section_level
	
	function get_sitemap_section_levels($section, $field = 'id', $levels = array()) {
		if (!is_array($section)) $section = get_sitemap_section($section, $field);
		$levels[$section['id']] = $section;
		
		if ($section['parent_id'] != 0) :
			$levels = get_sitemap_section_levels($section['parent_id'], 'id', $levels);
		endif;
		ksort($levels);
		
		return $levels;
	} // get_sitemap_section_levels
	
	function get_sitemap_section_url($id = false) {
		if (!$id) $id = get_var('level_id');
		$section = get_sitemap_section($id);
		$urlParts = $section['url'];
		
		while ($section['parent_id'] > 0) {
			$section = get_sitemap_section($section['parent_id']);
			$urlParts = $section['url'] . "/" . $urlParts;
		}
		
		if (ltrim($urlParts, "/") == '')
			return get_location();
		
		return get_location() . ltrim($urlParts, "/") . "/";
	} // get_sitemap_section_url
	
	function get_sitemap_subsection($parent_id, $value, $field = 'id') {
		$subsections = get_sitemap_subsections($parent_id);
		foreach ($subsections as $section) :

			if (isset($section[$field]) && $section[$field] == $value) :
				return $section;
			endif;
			
		endforeach;
		
		return false;
	} // get_sitemap_subsection
	
	// return all subsections of intended section (sitemaps with parent_id = $parent_id)
	function get_sitemap_subsections($parent, $sitemap = false) {
		$section = false;
		if (is_array($parent) && isset($parent['subsections'])) :
			$section = $parent['subsections'];
		elseif (is_numeric($parent)) :
			$section = get_sitemap_section($parent, 'id', $sitemap);
			if (isset($section['subsections']) && is_array($section['subsections'])) $section = $section['subsections'];
		endif;
		return $section;
	} // get_sitemap_subsections
	
	function get_sitemap_sections_by_content($content = 'articles', $levels = false, $sections = array()) {
		if (!$levels) $levels = get_sitemap_roots();
		
		if (isset($levels['subsections']) && is_array($levels['subsections']))
			$levels = array($levels);
		foreach ($levels as $level) {
			if (($level['type'] == $content) || (($level['type'] == 'content') && ($level['content'] == $content))) {
				$sections[] = $level;
			}
			if (is_array($level['subsections']) && (count($level['subsections']) > 0)) {
				$sections = get_sitemap_sections_by_content($content, $level['subsections'], $sections);
			}
		}
		return $sections;
	} // get_sitemap_sections_by_content

	function has_parent($section) {
		if (!isset($section['parent_id'])) $section = get_sitemap_section($section);
		return ($section['parent_id'] != 0);		
	} // has_parent
	
	function has_subsections($section) {
		if (!isset($section['subsections'])) $section = get_sitemap_section($section);
		return (count($section['subsections']) > 0);
	} // has_subsections
	
	function link_to_section($section = false) {
		$section = section(false, $section);
		if (is_array($section) && isset($section['id']) && isset($section['name'])) {
			return '<a href="' . get_sitemap_section_url($section['id']) . '">' . valid($section['name']) . '</a>';
		}
		return false;
	} // link_to_section
	
	function section($return = false, $section = false) {
		if ($section === false) {
			$section = get_var('level');
		} else if (is_numeric($section)) {
			$section = get_sitemap_section($section);
		} else if (is_array($section) && isset($section['id']) && is_numeric($section['id'])) {
			// perfect...
		} else if (is_string($section) && !empty($section)) {
			// name vs url?
			$section = get_sitemap_section($section, 'name');
			if (!isset($section['id'])) $section = get_sitemap_section($section, 'url');
		}
		
		if (is_array($section) && isset($section['id']) && is_numeric($section['id'])) {
			if (is_string($return) && !empty($return)) {
				if (isset($section[$return])) {
					return $section[$return];
				} else {
					return false;
				}
			} else {
				return $section;
			}
		} 
		return false;
	} // section
	
	function section_header_image($section = false) {
		$headerImage = section('image');
		if (is_string($headerImage) && !empty($headerImage)) {
			return ' style="background-image: url(' . $headerImage . ')"';
		}
		return false;
	} // section_header_image
	
	function section_id($section = false) {
		return section('id', $section);
	} // section_id
	
	function section_name($section = false) {
		return section('name', $section);
	} // section_name
	
	function section_link($section = false) {
		return get_sitemap_section_url(section_id($section));
	} // section_link
	
	function set_sitemap_levels($url = false) {
		if (is_array($url) && isset($url['url'])) $url = $url['url'];
		
		if (is_string($url) && !empty($url)) :
			$urlParts = explode('/', $url);
		else :
			$urlParts = array();
		endif;
		
		if (current($urlParts) == get_site())
			array_shift($urlParts);
			
		$parent = get_sitemap_root($urlParts);
		set_var('root', $parent);
		
		$levels = array($parent['id'] => get_sitemap_section($parent['id']));
		
		if (empty($url) && !empty($parent['url'])) :
			
			foreach ($parent['subsections'] as $subsection) :
			
				if (empty($subsection['url']) || ($subsection['url'] == '/')) :
					$levels[$subsection['id']] = $subsection;
				endif;
			
			endforeach;
		
		elseif (!empty($url) && (current($urlParts) == $parent['url']))	:

			array_shift($urlParts);

		endif;
		
		$params = array();
		
		foreach($urlParts as $urlPart) :
			if ((count($params) == 0) && ($section = get_sitemap_subsection($parent['id'], $urlPart, 'url'))) :
				$levels[$section['id']] = $section;
				$parent = $section;
			else :
				$params[] = $urlPart;
			endif;

		endforeach;
		
		set_var('levels', $levels);
		//$root = current($levels);
		//set_var('root', $root);
		//set_var('root_id', $root['id']);
		$section = current($levels);
		set_var('top_level', $section);
		if (!is_admin())
			set_var('section', $section);
		$subsection = next($levels);
		set_var('level2', $subsection);
		set_var('subsection', $subsection);
		$level = end($levels);
		set_var('level', $level);
		set_var('level_id', $level['id']);
		set_var('level_url', get_sitemap_section_url($level['id']));		
		$parent_level = (($level['parent_id'] !== '0') && isset($levels[$level['parent_id']])) ? $levels[$level['parent_id']] : false;
		set_var('parent', $parent_level);

		set_var('params', $params);
		
		if ($level['parent_id'] > 0)
			title($level['name']);
		
		set_page_var('level', $level);

		return false;
	} // set_sitemap_levels

?>