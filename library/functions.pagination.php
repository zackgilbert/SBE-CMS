<?php

	add_stylesheet('pagination');

	function pagination($previous = '&laquo; Previous Page', $next = 'Next Page &raquo;') {
		if (get_page_var('items') !== false) {
			$pages = pagination_page_count();

			// if more than one page, then display pagination include...
			if ($pages > 1) {
				load_include('pagination.php', array('previous' => $previous, 'next' => $next));
			}
		} else {
			warning("Pagination function called but no items have been defined and stored.", __FILE__, __FUNCTION__, __LINE__);
		}
	} // pagination
	
	function pagination_viewing_start() {
		return ((pagination_current_page()-1)*pagination_browse_limit())+1;
	} // pagination_viewing_range
	
	function pagination_viewing_end() {
		return ((pagination_current_page()-1)*pagination_browse_limit()) + count(get_search_items());
	} // pagination_viewing_end
	
	function pagination_browse_limit() {
		if (defined(get_folder() . '_BROWSE_LIMIT'))
			return constant(get_folder() . '_BROWSE_LIMIT');
		if (defined('BROWSE_LIMIT'))
			return constant('BROWSE_LIMIT');
		return 20;
	} // pagination_browse_limit
	
	function pagination_current_page() {
		$page = request('page', 1);
		$pages = pagination_page_count();
		if ($page > $pages)
			$page = count($pages);
		return $page;
	} // pagination_page
	
	function pagination_page_count() {
		if (count_search_items() && is_numeric(count_search_items()) && (count_search_items() > 1))
			return ceil(count_search_items()/pagination_browse_limit());
		return false;
	} // pagination_pages
	
	function pagination_is_first() {
		return (pagination_current_page() == 1);
	} // pagination_is_first
	
	function pagination_is_last() {
		return (pagination_current_page() == pagination_page_count());
	} // pagination_is_last
		
	function pagination_next($text = 'Next Page &raquo;') {
		return (!pagination_is_last()) ? ('<a href="?' . join("&amp;", query_string(array('page' => pagination_current_page()+1))) . '">' . $text . '</a>') : $text;
	} // pagination_next
	
	function pagination_previous($text = ' &laquo; Previous Page') { pagination_prev($text); }
	function pagination_prev($text = ' &laquo; Previous Page') {
		return (!pagination_is_first()) ? ('<a href="?' . join("&amp;", query_string(array('page' => pagination_current_page()-1))) . '">' . $text . '</a>') : $text;
	} // pagination_prev

	function pagination_all_pages() {
		if (!get_var('pagination_all_pages')) {
			$pages = array();
			for ($i=1; $i<=pagination_page_count(); $i++) {
				$pages[$i] = '?' . join("&amp;", query_string(array('page' => $i)));
			}
			set_var('pagination_all_pages', $pages);
		}
		return get_var('pagination_all_pages');
	} // pagination_all_pages

	function pagination_has_pages() {
		
		$pages = pagination_pages();		
		$current_page = get_var('pagination_current_page');
		
		if ($current_page === false) {
			reset($pages);
			$current_page = key($pages);
			set_var('pagination_current_page', $current_page);
			return current($pages);
		} else {
			array_set_current($pages, $current_page);
		}
		
		if (next($pages)) {
			$current_page = key($pages);
		} else {
			$current_page = false;
		}
		set_var('pagination_current_page', $current_page);
		
		return $current_page;
	} // pagination_has_pages

	function pagination_pages() {	
	
		if (!get_var('pagination_pages')) {
		
			$allPages = pagination_all_pages();
			$pages = array();
			$linkLimit = 10;

			if (count($allPages) <= ($linkLimit)) {

				$pages = $allPages;

			} else {

				if (pagination_current_page() <= ($linkLimit/2)) {
					//echo "show pages 1 through " . $linkLimit . " ... " . count($pages);
					for ($i=1; $i<=$linkLimit; $i++) {
						$pages[$i] = $allPages[$i];
					}
					$pages['separator2'] = '...';
					$pages[array_end_key($allPages)] = end($allPages);
				} else if (pagination_current_page() >= (count($allPages) - ($linkLimit/2))) {
					//echo "show pages 1 ... " . (count($pages)-$linkLimit) . " through " . count($pages);
					$pages[1] = reset($allPages);
					$pages['separator1'] = '...';
					for ($i=((count($allPages)-$linkLimit))+1; $i<=count($allPages); $i++) {
						$pages[$i] = $allPages[$i];
					}
				} else {
					//echo "show pages 1 ... " . (($page+1) - ($linkLimit/2)) . " through " . (($page+1) + ($linkLimit/2)) . " ... " . count($pages); 
					$pages[1] = $allPages[1];
					$pages['separator1'] = '...';
					for ($i=(pagination_current_page()-($linkLimit/2))+1; $i<(pagination_current_page()+($linkLimit/2)); $i++) {
						$pages[$i] = $allPages[$i];
					}
					$pages['separator2'] = '...';
					$pages[array_end_key($allPages)] = end($allPages);
				}
				
			}
			
			set_var('pagination_pages', $pages);

		}

		return get_var('pagination_pages');
	} // pagination_pages

	function pagination_page_is_current() {
	 	return (pagination_current_page() == get_var('pagination_current_page'));
	} // pagination_page_is_current
	
	function pagination_page_number() {
		$pages = pagination_pages();
		array_set_current($pages, get_var('pagination_current_page'));
		return key($pages);
	} // pagination_page_number
	
	function pagination_page_is_separator() {
		$pages = pagination_pages();
		return ($pages[get_var('pagination_current_page')] === '...');
	} // pagination_page_is_separator
	
	function pagination_page_link() {
		$pages = pagination_pages();
		return $pages[get_var('pagination_current_page')];
	} // pagination_page_link
		
?>