<?php
	
	function get_comment_section($comment) {
		global $db;
		$parent = $comment->parent();//get_comment_parent($comment);
		
		if (is_array($parent) && isset($parent['sitemap_id'])) {
			return $parent['sitemap_id'];
		} else if (is_object($comment) && ($section = $comment->parent('section'))) {
			return $section;
		} else if ($section = get_sitemap_section($comment->table, 'content')) {
			return $section['id'];
		} else if (($comment->table == 'gallery_album_photos') && ($section = get_sitemap_section('galleries', 'content'))) {
			return $section['id'];
		} else {
			return $db->getProperty('table=>' . singularize($comment->table) . '_sitemap', "where=>(`" . singularize($comment->table) . "_id` = " . $comment->table_id . ") AND (`deleted_at` IS NULL)", 'property=>sitemap_id');
		}
		return false;
	} // get_comment_section
	
	function count_comments($table = false, $id = false) {
		global $db;
		if (!($comments = get_memcache('count_comments--' . $table . '--' . $id))) {
			$comments = $db->count('table=>comments', "where=>(`table` = '" . $db->escape($table) . "') AND (`table_id` = " . $db->escape($id) . ") AND (`approved_at` IS NOT NULL) AND (`moderated_at` IS NULL) AND (`deleted_at` IS NULL)");
			set_memcache('count_comments--' . $table . '--' . $id, $comments);
		}
		return $comments;
	} // count_comments
	
	function get_comments($table = false, $id = false) {
		global $db;
		//return $db->get('table=>comments', "where=>(`table` = '" . $db->escape($table) . "') AND (`table_id` = " . $db->escape($id) . ") AND (`approved_at` IS NOT NULL) AND (`moderated_at` IS NULL) AND (`deleted_at` IS NULL)");
		if (!($comments = get_memcache('get_comments--' . $table . '--' . $id))) {
			$comments = $db->get('table=>comments', "where=>(`table` = '" . $db->escape($table) . "') AND (`table_id` = " . $db->escape($id) . ") AND (`deleted_at` IS NULL)");
			set_memcache('get_comments--' . $table . '--' . $id, $comments);
		}
		
		if (is_array($comments)) {
			$_comments = array();
			foreach ($comments as $commentNum => $comment) {
				$_comments[$comment['id']] = get_comment($comment);
			}
			$comments = $_comments;
		}
		
		return $comments;
	} // get_comments
	
	function get_user_comments($user_id = 0) {
		global $db;
		if (!($comments = get_memcache('user_comments--' . $user_id))) {
			$comments = $db->get('table=>comments', "where=>(`user_id` = " . $db->escape($user_id) . ") AND (`rating` IS NULL) AND (`approved_at` IS NOT NULL) AND (`moderated_at` IS NULL) AND (`deleted_at` IS NULL)", "order=>created_at DESC");
			set_memcache('user_comments--' . $user_id, $comments);
		}
		return $comments;
	} // get_user_comments
		
	function get_user_reviews($user_id = 0) {
		global $db;
		if (!($comments = get_memcache('user_reviews--' . $user_id))) {
			$comments = $db->get('table=>comments', "where=>(`user_id` = " . $db->escape($user_id) . ") AND (`rating` IS NOT NULL) AND (`approved_at` IS NOT NULL) AND (`moderated_at` IS NULL) AND (`deleted_at` IS NULL)", "order=>created_at DESC");
			set_memcache('user_reviews--' . $user_id, $comments);
		}
		return $comments;
	} // get_user_reviews
	
	function no_comments($comments) {
		if (count($comments) <= 0) {
			if (is_file(get_path() . "/includes/comments-none.php")) {
				// directory / theme / includes / comment.php
				include(get_path() . "/includes/comments-none.php");
			} else if (is_file(PLUGINS . "comments/includes/comments-none.php")) {
				// COMMENTS_PLUGIN / includes / comment.php
				include(PLUGINS . "comments/includes/comments-none.php");
			} else {
				echo "<div class=\"no-comments-container\">There aren't any comments yet.</div>";
			}
		}
	}
	
	function comments($table = false, $table_id = false) {
		$comments = (is_array($table)) ? $table : get_comments($table, $table_id);
		$i=0;
		foreach($comments as $comment) :
			if (!is_object($comment))
				$comment = get_comment($comment, $i);
			load_comment($comment, $i);
			$i++;
		endforeach;		
		no_comments($comments);
	} // comments
	
	function comments_form($table = false, $table_id = false, $section_id = false) {
		if (is_file(get_path() . "/includes/comments-form.php")) {
			// directory / theme / includes / comment.php
			include(get_path() . "/includes/comments-form.php");
		} else if (is_file(PLUGINS . "comments/includes/comments-form.php")) {
			// COMMENTS_PLUGIN / includes / comment.php
			include(PLUGINS . "comments/includes/comments-form.php");
		} else {
			echo "<div class=\"no-comments-form\">Uh oh. There was an error trying to load the comments form.</div>";
		}
	} // comments_form
	
	function load_comment($comment, $count = 0) {
		if (!MODERATE_COMMENTS || $comment->isApproved()) {
			if (is_file(get_path() . "/includes/comment.php")) {
				// directory / theme / includes / comment.php
				include(get_path() . "/includes/comment.php");
			} else if (is_file(PLUGINS . "comments/includes/comment.php")) {
				// COMMENTS_PLUGIN / includes / comment.php
				include(PLUGINS . "comments/includes/comment.php");
			} else {
				echo "No comment include file found...";
				dump($comment);
			}
		} else {
			if (is_file(get_path() . "/includes/comment-unapproved.php")) {
				// directory / theme / includes / comment.php
				include(get_path() . "/includes/comment-unapproved.php");
			} else if (is_file(PLUGINS . "comments/includes/comment-unapproved.php")) {
				// COMMENTS_PLUGIN / includes / comment.php
				include(PLUGINS . "comments/includes/comment-unapproved.php");
			} else {
				echo "This comment still needs to be approved.";
			}
		}
	} // load_comment
	
	function get_comment($id, $count = 0) {
		$comment = get_memcache('comments-' . $id);
		
		if ($comment) {
			return new comment($comment, $count);
		}
		return new comment($id, $count);
	} // get_comment
	
	function link_to_comment($comment_id) {
		$comment = get_comment($comment_id);
		return '<a href="' . $comment->link() . '">' . $comment->parent('title') . '</a>';
	} // link_to_comment

	function is_review($comment_id) {
		$comment = get_comment($comment_id);
		return $comment->isReview();
	} // is_review

	function comments_activity_text($activity) {
		if (is_review($activity['table_id'])) {
			return 'reviewed ' . link_to_comment($activity['table_id']);
		}
		return 'commented on ' . link_to_comment($activity['table_id']);
	} // comments_activity_text
	
	function comments_activity_class($activity) {
		if (is_review($activity['table_id'])) {
			return 'reviews';
		}
		return 'comments';
	} // comments_activity_class
	
	function build_rating($rating) {
		$html = '<div class="star-rating-container"><ul';
		//$html .= ($rating > 0) ? ' class="star-rating">' : '>';
		$html .= ' class="star-rating">';
		$html .= ($rating > 0) ? '<li class="current-rating" style="width: ' . $rating*19 . 'px;">' . $rating . '/5 Star Rating.</li>' : '<li class="empty">Not Rated Yet</li>';
		$html .= '</ul></div>';
		return $html;
	} // build_rating

	function search_comments($terms = false) {
		global $db;
		
		if (!is_array($terms)) 
			$terms = $_REQUEST;
		
		$content = array();
		$criteria = array();
		
		if (isset($terms['keywords']) && !empty($terms['keywords'])) {
			//if (isset($_POST['exact-match'])) {
			//} else {
				// this needs to be reworked for better, more forgiving search
				$criteria[] = "(`comment` LIKE '%" . $db->escape($terms['keywords']) . "%') OR (`name` LIKE '%" . $db->escape($terms['keywords']) . "%')";
			//}
		}
		
		/*if (!isset($terms['section']) || empty($terms['section']) || !is_numeric($terms['section'])) {
			$root = get_sitemap_root();
			$terms['section'] = $root['id'];
		}
		$subsections = array($terms['section']);
			
		$sections = get_sitemap_subsections($terms['section']);
		while (count($sections) > 0) {
			$tempsections = array();
			foreach ($sections as $section) {
				$subsections[] = $section['id'];
				$tempsections = array_merge($tempsections,$section['subsections']);
			}
			$sections = $tempsections;
		}

		$criteria[] = "(`sitemap_id` = " . join(") OR (`sitemap_id` = ", $subsections) . ")";			
		*/		
		if (is_admin()) {
			if (isset($terms['status']) && !empty($terms['status'])) {
				if ($terms['status'] == 'approved') {
					$criteria[] = "`approved_at` IS NOT NULL";
					$criteria[] = "`deleted_at` IS NULL";
				} elseif ($terms['status'] == 'unapproved') {
					$criteria[] = "`approved_at` IS NULL";
					$criteria[] = "`deleted_at` IS NULL";
				} elseif ($terms['status'] == 'moderated') {
					$criteria[] = "`moderated_at` IS NOT NULL";
					$criteria[] = "`deleted_at` IS NULL";
				} else if ($terms['status'] == 'deleted') {
					$criteria[] = "`deleted_at` IS NOT NULL";
				} else if ($terms['status'] == 'draft') {
					$criteria[] = "`approved_at` IS NULL";
					$criteria[] = "`deleted_at` IS NULL";
				} else if ($terms['status'] == 'public') {
					//$criteria[] = "`published_at` IS NOT NULL";
					$criteria[] = "`approved_at` IS NOT NULL";
					$criteria[] = "`deleted_at` IS NULL";
				}
			} else {
				$criteria[] = "`deleted_at` IS NULL";				
			}
		} else {
			$criteria[] = "`approved_at` IS NOT NULL";
			$criteria[] = "`moderated_at` IS NULL";
			$criteria[] = "`deleted_at` IS NULL";
		}
		
		if (isset($terms['date']) && !empty($terms['date'])) {
			$criteria[] = "(`created_at` LIKE '" . $db->escape($terms['date']) . "%') OR (`approved_at` LIKE '" . $db->escape($terms['date']) . "%')";
		}

		$criteria[] = '`deleted_at` IS NULL';
		
		$sql = "SELECT *, CONCAT('comments') AS t FROM `comments` WHERE (" . join(') AND (', $criteria) . ")";
		
		if (isset($terms['sort'])) {
			$sql = $sql . " ORDER BY " . $db->escape($terms['sort']);
		} else {
			$sql = $sql . " ORDER BY `created_at` DESC";
		}
		
		if (isset($terms['limit'])) {
			$sql = $sql . " LIMIT " . $db->escape($terms['limit']);
		}
		//$content = $db->get($sql);	
		if (!($content = get_memcache($sql))) {
			$content = $db->get($sql);
			set_memcache($sql, $content);
		}
		$content = objectize('comments', $content);
		
		return $content;
	} // search_comments
	
	function section_comments($numberToReturn = false, $section = false) {
		global $db;
		
		if ($section === false) $section = get_var('level');
		
		if ($comments = get_memcache('section_comments--' . $numberToReturn . '--' . $section))
			return objectize('comment', $comments);
		
		if ($section['parent_id'] === '0') {

			$subsections = array($section['id']);

			$sections = get_sitemap_subsections($section['id']);
			while (count($sections) > 0) {
				$tempsections = array();
				foreach ($sections as $s) {
					$subsections[] = $s['id'];
					$tempsections = array_merge($tempsections,$s['subsections']);
				}
				$sections = $tempsections;
			}

			$sql = "SELECT * FROM `comments` WHERE (`approved_at` IS NOT NULL) AND (`moderated_at` IS NULL) AND (`deleted_at` IS NULL) AND ((`sitemap_id` = " . join(") OR (`sitemap_id` = ", $subsections) . ")) ORDER BY `created_at` DESC" . ((is_numeric($numberToReturn)) ? " LIMIT $numberToReturn" : '');
			
		} else if ($section['type'] == 'content') {
		
			$sql = "SELECT * FROM `comments` WHERE (`sitemap_id` = " . $db->escape($section['id']) . ") AND (`approved_at` IS NOT NULL) AND (`moderated_at` IS NULL) AND (`deleted_at` IS NULL) ORDER BY `created_at` DESC" . ((is_numeric($numberToReturn)) ? " LIMIT $numberToReturn" : '');
		
		} else {
			
			//$subsections = array_flatten($section['subsections'], 'id');
			$subsections = array($section['id']);

			$sections = get_sitemap_subsections($section['id']);
			while (count($sections) > 0) {
				$tempsections = array();
				foreach ($sections as $s) {
					$subsections[] = $s['id'];
					$tempsections = array_merge($tempsections,$s['subsections']);
				}
				$sections = $tempsections;
			}
			
			$sql = "SELECT * FROM `comments` WHERE ((`sitemap_id` = " . join(") OR (`sitemap_id` = ", $subsections) . ")) AND (`approved_at` IS NOT NULL) AND (`moderated_at` IS NULL) AND (`deleted_at` IS NULL) ORDER BY `created_at` DESC" . ((is_numeric($numberToReturn)) ? " LIMIT $numberToReturn" : '');
			
		}
		
		if (!($comments = get_memcache($sql))) {
			$comments = $db->get($sql);
			set_memcache($sql, $comments);
		}
		//$comments = $db->get($sql);

		set_memcache('section_comments--' . $numberToReturn . '--' . $section, $comments);
		return objectize('comment', $comments);
	} // section_comments
	
	function most_commented() {
		global $db;

		$root = get_sitemap_root();
		$subsections = array($root['id']);
			
		$sections = get_sitemap_subsections($root['id']);
		while (count($sections) > 0) {
			$tempsections = array();
			foreach ($sections as $section) {
				$subsections[] = $section['id'];
				$tempsections = array_merge($tempsections,$section['subsections']);
			}
			$sections = $tempsections;
		}

		$sql = "SELECT *, COUNT(*) as count FROM `comments` WHERE ((`sitemap_id` = " . join(") OR (`sitemap_id` = ", $subsections) . ")) AND (`created_at` >= '" . daysAway(-14) . "') AND (`approved_at` IS NOT NULL) AND (`moderated_at` IS NULL) GROUP BY `table`, `table_id` ORDER BY count DESC, created_at DESC LIMIT 5";
		if (!($comments = get_memcache($sql))) {
			$comments = $db->get($sql);
			set_memcache($sql, $comments);
		}
		
		return objectize('comment', $comments);
	} // most_commented
	
?>