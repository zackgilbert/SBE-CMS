<?php

	function get_editable_container($editableArea) {
		$html = $editableArea->outertext;//$editableArea->asXML();
		if (get_editable_type($editableArea) == 'photo') {
			//$atts = $editableArea->attributes();
			$content = str_replace($editableArea->getAttribute('src')/*$atts['src']*/, "%s", $html);
		} elseif (get_editable_type($editableArea) == 'title') {
			$content = substr($html, 0, strpos($html, ">")+1) . "%s" . substr($html, strrpos($html, "<"));
		} else {
			$content = substr($html, 0, strpos($html, ">")+1) . "\n%s\n" . substr($html, strrpos($html, "<"));			
		}
		return $content;
	} // get_editable_container

	function get_editable_content($editableArea) {
		// strip off the wrapper tag and just return the innards as a string...
		$html = $editableArea->innertext;//$editableArea->asXML();
		if (get_editable_type($editableArea) == 'photo') {
			//$atts = $editableArea->attributes();
			$content = $editableArea->getAttribute('src');//$atts['src'];
		} else {
		//	$html = substr($html, strpos($html, ">")+1);
		//	$content = substr($html, 0, strrpos($html, "<"));
			$content = $html;
		}
		$content = str_replace(array("&amp;lt;?", "?&amp;gt;", "&lt;?", "?&gt;", ">SEENREPLACEME</textarea>"), array("<?", "?>",  "<?", "?>", "></textarea>"), $content);
		$content = tagstolower($content);
		$content = rtrim($content, "\n");
		return $content;
		//return $editableArea->asXML();
	} // get_editable_content
	
	function get_editable_title($editableArea, $default) {
		if (!is_object($editableArea))
			return $default;
			
		/*$atts = $editableArea->attributes();
		
		if (isset($atts['title']) && !empty($atts['title']))
			$class = $atts['title'];
		elseif (isset($atts['alt']) && !empty($atts['alt']))
			$class = $atts['alt'];
		elseif (isset($atts['rel']) && !empty($atts['rel']))
			$class = $atts['rel'];
		elseif (isset($atts['id']) && !empty($atts['id']))
			$class = $atts['id'];
		else
			$class = $default;*/
			
		if (isset($editableArea->title) && !empty($editableArea->title))
			$title = $editableArea->title;
		elseif (isset($editableArea->alt) && !empty($editableArea->alt))
			$title = $editableArea->alt;
		elseif (isset($editableArea->rel) && !empty($editableArea->rel))
			$title = $editableArea->rel;
		elseif (isset($editableArea->id) && !empty($editableArea->id))
			$title = $editableArea->id;
		else
			$title = $default;

		return $title;
	} // get_editable_title

	function get_editable_type($editableArea) {
		if (!is_object($editableArea))
			return false;
			
		/*$atts = $editableArea->attributes();
		$classes = explode(' ', $atts['class']);*/
		$classes = explode(' ', $editableArea->getAttribute('class'));
		
		$class = 'editable';
		foreach ($classes as $c)
			if (substr($c, 0, strlen('editable')) == 'editable')
				$class = $c;
		
		if ($class == 'editable')
			$type = get_editable_type_by_tag($editableArea->tag);//$editableArea->getName());
		else 
			$type = get_editable_type_by_class($class);
		
		return $type;
	} // get_editable_type
	
	function get_editable_type_by_class($class = 'editable-html') {
		// just return whatever is after editable-
		return substr($class, strpos($class, '-')+1);
	} // get_editable_type_by_class
	
	function get_editable_type_by_tag($tag = 'div') {
		if (in_array($tag, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6')))
			return 'title';
		//if (in_array($tag, array('p')))
		//	return 'text';
		if (in_array($tag, array('img')))
			return 'photo';
		
		return 'html';
	} // get_editable_type_by_tag
	
	function get_recently_edited_pages($numberToReturn = 5) {
		global $db;
		
		// SELECT data.* FROM data INNER JOIN (SELECT MAX(id) AS id FROM data GROUP BY url) ids ON data.id = ids.id 
		$sql = "SELECT `versions`.`id` as `version_id`, `versions`.`created_at`, `versions`.`created_by`, `sitemap`.`id`, `sitemap`.`name`, `users`.`name` as `creator` FROM `versions` INNER JOIN (SELECT MAX(`versions`.`id`) AS `id` FROM `versions` GROUP BY `versions`.`sitemap_id`) `ids` ON `versions`.`id` = `ids`.`id` INNER JOIN `sitemap` ON `versions`.`sitemap_id` = `sitemap`.`id` INNER JOIN `users` ON `versions`.`created_by` = `users`.`id` WHERE (`versions`.`deleted_at` IS NULL) AND (`sitemap`.`deleted_at` IS NULL) GROUP BY `versions`.`sitemap_id` ORDER BY `versions`.`created_at` DESC LIMIT " . $numberToReturn;
		$edits = $db->get($sql);
		
		return $edits;
	} // get_recently_edited_pages
	
	function previous_edits($section = false) {
		global $db;
		
		if (is_string($section)) {
			$versions = $db->get('table=>versions', "where=>(`sitemap_id` = 0) AND (`filename` LIKE '%" . $db->escape($section) . "') AND (`deleted_at` IS NULL)", "order=>created_at DESC");
		} else {
			if (!$section) $section = get_var('level');

			$versions = $db->get('table=>versions', "where=>(`sitemap_id` = " . $section['id'] . ") AND (`deleted_at` IS NULL)", "order=>created_at DESC");
		}
				
		return $versions;
	} // previous_edits

?>