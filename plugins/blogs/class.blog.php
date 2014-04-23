<?php

	class blog {
		
		var $table = 'blogs';
		var $_table = false;
		var $_exists = false;
		var $_errors = false;
		var $_author = false;
		var $_banner = false;
		var $_comments = false;
		var $_media = false;
		var $_categories = false;
		var $_link = false;
		var $id = false;
		var $user_id = false;
		var $url = false;
		var $title = false;
		var $body = '';
		var $excerpt = false;
		var $_status_options = array("public" => "Publish Publicly", "private" => "Publish Privately", "registered_only" => "Publish For Registered Users Only");
		var $status = 'public';
		var $_comment_status_options = array("open" => "Open", "closed" => "Closed", "registered_only" => "Registered Users Only");
		var $comment_status = 'open';
		var $comment_count = 0;
		var $_show_comments;
		var $thumb = false;
		var $blog_categories = false;
		var $published_at;
		var $created_at;
		var $updated_at;
		var $deleted_at;
		var $sitemap_id = false;

		// constructor
		function blog($blog = false, $date = false) {
			$this->_table = singularize($this->table);
			
			if (is_array($blog) && !isset($blog['t'])) {
				// if array is supplied...
				// set each variable supplied
				$this->_setFields($blog);
			} else if (is_array($blog) && isset($blog['id'])) {
				$this->id = $blog['id'];
				$this->_byId();
			} else if (is_numeric($blog)) {
				// if id is supplied...
				$this->id = $blog;
				$this->_byId();
			} else if (is_string($blog) && !empty($blog)) {
				// if url is supplied
				$this->sitemap_id = get_var('level_id');
				$this->url = $blog;
				$this->_byUrl($date);
			}
			
			return $this;
		} // article constructor
		
		function _byId() {
			global $db;
			
			//if (!$this->_fromCache()) {
				// get it from the db
				$sql = "SELECT `blogs`.*, `blog_sitemap`.`sitemap_id` FROM `blogs` LEFT JOIN `blog_sitemap` ON `blogs`.`id` = `blog_sitemap`.`blog_id` WHERE (`blogs`.`id` = " . $db->escape($this->id) . ")";
				
				if (!($blogs = get_memcache($sql))) {
					$blogs = $db->get($sql);
					set_memcache($sql, $blogs);
				}
				
				//$blogs = $db->get($sql);
				//$blogs = $db->get('table=>blogs', "where=>(`id` = " . $db->escape($this->id) . ") AND (`deleted_at` IS NULL)");
				
				if (!is_array($blogs)) {
					//error('Attempting to get a single blog by id (' . $this->id . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
				} else if (count($blogs) < 1) {
					//warning('Attempting to get a single blog by id (' . $this->id . ') has failed. An empty array was returned for supplied id. This suggests the blog does not exist.', __FILE__, __FUNCTION__, __LINE__);
				} else if (count($blogs) > 1) {
					//warning('Attempting to get a single blog by id (' . $this->id . ') has failed. ' . count($blogs) . " blogs were returned for supplied id. First blog returned has been used.", __FILE__, __FUNCTION__, __LINE__);
				}
				
				$blog = (isset($blogs[0])) ? $blogs[0] : false;
				
				if (is_array($blog)) {
					
					$this->_setFields($blog);
					//$this->_cache();

				} else {
					//error('Attempting to get a single blog by id (' . $this->id . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
				}
			//}
		} // byId
		
		function _byUrl($date = false) {
			global $db;
			
			//if (!$this->_fromCache()) {
				// get it from the db
				$dateSQL = (is_string($date)) ? " AND (`blogs`.`published_at` LIKE '" . $db->escape($date) . "%')": '';
				$sql = "SELECT `blogs`.* FROM `blogs` LEFT JOIN `blog_sitemap` ON `blogs`.`id` = `blog_sitemap`.`blog_id` WHERE ((`blogs`.`url` LIKE '" . $db->escape($this->url) . "') OR (`blogs`.`title` LIKE '" . $db->escape(str_replace(" ", '_', $this->url)) . "'))" . $dateSQL . " AND (`blog_sitemap`.`sitemap_id` = " . $db->escape($this->sitemap_id) . ") AND (`blogs`.`deleted_at` IS NULL) AND (`blog_sitemap`.`deleted_at` IS NULL)";
				
				if (!($blogs = get_memcache($sql))) {
					$blogs = $db->get($sql);
					set_memcache($sql, $blogs);
				}
				//$blogs = $db->get($sql);
				//$blogs = $db->get('table=>blogs', "where=>(`url` LIKE '" . $db->escape($this->url) . "') AND (`deleted_at` IS NULL)");
				
				if (!is_array($blogs)) {
					error('Attempting to get a single blog by url (' . $this->url . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
				} else if (count($blogs) < 1) {
					//warning('Attempting to get a single blog by url (' . $this->url . ') has failed. An empty array was returned for supplied url.', __FILE__, __FUNCTION__, __LINE__);
				} else if (count($blogs) > 1) {
					warning('Attempting to get a single blog by url (' . $this->url . ') has failed. ' . count($blogs) . " blogs were returned for supplied url. First blog returned has been used.", __FILE__, __FUNCTION__, __LINE__);
				}
				
				$blog = (isset($blogs[0])) ? $blogs[0] : false;
				
				if (is_array($blog)) {
					
					$this->_setFields($blog);
					//$this->_cache();

				} else {
					//error('Attempting to get a single blog by url (' . $this->url . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
				}
			//}
		} // byUrl

		function _setFields($fields, $fieldsToSkip = array()) {
			if (is_array($fields)) {
				foreach ($fields as $key => $value) {
					if (!in_array($key, $fieldsToSkip)) {
						$this->$key = $value;
					}
				}
				$this->_exists = true;
				$this->store();
				return true;
			} else {
				error('Attempting to set fields based on fields supplied has failed. $fields variable wasn\'t an array.', __FILE__, __FUNCTION__, __LINE__);
				return false;
			}
		} // _setFields
		
		function _updateCommentCount() {
			global $db;
			if (!is_array($this->_comments))
				$this->comments();
			$this->comment_count = count($this->_comments);
			//$this->_deleteCache();
			return $db->update('table=>' . $this->table, 'id=>' . $this->id, array('comment_count' => $this->comment_count));
		} // _updateCommentCount

		function author($pretext = '', $link = true) {
			global $db;

			if (is_numeric($this->user_id)) {
				if (!is_array($this->_author)) {
					$this->_author = $db->getOne("table=>users", "id=>" . $db->escape($this->user_id));
				}
				if ($link) {
					return $pretext . '<a href="' . get_sitemap_section_url($this->section()) . '?author=' . $this->_author['id'] . '">' . valid($this->_author['name']) . '</a>';
				} else {
					return $pretext . valid($this->_author['name']);
				}
			}
			return false;
		} // authors
		
		function alt() {
			return str_replace('"', '&quot;', $this->title());
		} // alt
		
		function banner($return = false) {
			global $db;
			
			if (!is_array($this->_banner)) {
			
				$banner = $this->media('banner');
				if (is_array($banner) && (count($banner) > 0)) {
					$this->_banner = end($banner);
				} else {
					$this->_banner = array();
				}
			
			}
			
			if ($return) {
				if ($return == 'caption') $return = 'description';
				if ($return == 'image') $return = 'location';
				
				if (is_object($this->_banner)) {
					if (method_exists($this->_banner, $return)) {
						return $this->_banner->$return();
					} else {
						if (isset($this->_banner->$return)) return $this->_banner->$return;
					}
				} else if (is_array($this->_banner)) {
					if (isset($this->_banner[$return])) return $this->_banner[$return];
				}
				return false;
			}

			return $this->_banner;
		} // banner
		
		function body() {
			return format_text($this->body);
		} // body
		
		function categories() {
			if (!$this->_categories) {
				$this->_categories = trim_explode(",", $this->blog_categories);
			}
			return $this->_categories;
		} // categories

		function comment($id) {} // comment

		function comments($numberToReturn = 0, $force = false) {
			
			if (!$this->show_comments())
				return false;
			
			if (!is_array($this->_comments) || $force) {
				
				$this->_comments = get_comments($this->table, $this->id);
				
				if (!is_array($this->_comments)) {
					warning('Attempting to get blog comments has failed for user (id: ' . $this->id . ', url: ' . $this->url . ').', __FILE__, __FUNCTION__, __LINE__);
				}
				
			}
			
			if (is_array($this->_comments) && ($this->comment_count != count($this->_comments))) {
				$this->_updateCommentCount();
			}
			
			return array_max($this->_comments, $numberToReturn);
		} // comments
		
		function comment_status($check = false) {
			$status = $this->comment_status;
			return (is_string($check)) ? ($check === $status) : $status;
		} // comment_status
		
		function delete() {
			global $db;
			
			if (!$this->_exists || ($this->id <= 0)) {
				warning('Attempting to delete ' . $this->_table . ' (' . $this->id . ') failed because ' . $this->_table . ' does not exist.', __FILE__, __FUNCTION__, __LINE__);
				return false;
			}
			
			if (!$db->delete('table=>' . $db->escape($this->table), "id=>" . $db->escape($this->id), false)) {
				warning('An error occurred when trying to delete ' . $this->_table . ' (' . $this->id . ') from ' . $this->table . ' table in database.', __FILE__, __FUNCTION__, __LINE__);
				return false;
			}
			
			if (!$db->delete('table=>comments', "where=>(`table` = '" . $db->escape($this->table) . "') AND (`table_id` = " . $db->escape($this->id) . ")", false)) {
				warning('An error occurred when trying to delete ' . $this->_table . ' (' . $this->id . ') comments from database.', __FILE__, __FUNCTION__, __LINE__);
			}
						
			if (!$db->delete('table=>media', "where=>(`table` = '" . $db->escape($this->table) . "') AND (`table_id` = " . $db->escape($this->id) . ")", false)) {
				warning('An error occurred when trying to delete ' . $this->_table . ' (' . $this->id . ') media from database.', __FILE__, __FUNCTION__, __LINE__);
			}
			
			flush_memcache();
			return true;
		} // delete
		
		function display_comments() {
			if ($this->show_comments())
				comments($this->table, $this->id);
		} // display_comments
		
		function excerpt($length = 250, $words = false) {
			if (is_string($this->excerpt) && !empty($this->excerpt)) {
				return truncate(strip_tags($this->excerpt), $length, $words);
			}
			return truncate(strip_tags($this->body), $length, $words);
		} // excerpt
				
		function hadErrors() {
			return $this->_errors;
		} // hadErrors
		
		function hasSidebarContent() {
			
			if (count($this->sidebarContent()) > 0) {
				return true;
			}
			
			return false;
		} // hasSidebarContent

		function isInSection($section_id = false) {
			global $db;
			
			if (!$section_id) $section_id = get_var('level');
			if (isset($section_id['id'])) $section_id = $section_id['id'];
			$sections = $db->get('table=>blog_sitemap', "where=>(`blog_id` = " . $this->id . ") AND (`deleted_at` IS NULL)");

			foreach ($sections as $section) {
				if ($section['sitemap_id'] == $section_id)
					return true;
			}
			
			return false;
		} // isInSection

		function link() {
			
			if (!is_string($this->_link)) {
				//$this->_link = get_sitemap_section_url($this->section()) . $this->url . '/';
				$this->_link = get_sitemap_section_url($this->section()) . ((is_string($this->published_at)) ? format_date($this->published_at, 'Y/m/') : '') . $this->url . '/';
			}
			
			return $this->_link;
		} // link

		/*function media() {
			global $db;

			$this->_media = $db->get('table=>media', "where=>(`table` = 'blogs') AND (`table_id` = " . $db->escape($this->id) . ") AND (`deleted_at IS NULL)");
			
			return $this->_media;
		}// media */
		
		function media($return = false) {
			global $db;
			
			if (!$this->wasFound()) return array();
			
			if (!is_array($this->_media)) {
				
				$this->_media = $db->get('table=>media', "where=>(`table` = '" . $this->table . "') AND (`table_id` = " . $db->escape($this->id) . ") AND (`deleted_at` IS NULL)", "order=>created_at ASC");
				
			}
			
			if ($return) {
				if (is_array($this->_media)) {
					$mediaOfType = array();
					foreach ($this->_media as $media) {
						if ($media['type'] == $return) {
							$mediaOfType[] = $media;
						}
					}
					return $mediaOfType;
				}
				return false;
			}
			
			return $this->_media;
		}// media

		function permalink() {
			return $this->url;
		} // permalink	

		function save() { 
			/* THIS SHOULD BE BUILT SO THAT YOU CAN SAVE ARTICLE TO DB BASED ON CURRENT FIELDS */ 
		} // save

		function section() {
			global $db;
			
			if (!is_numeric($this->sitemap_id) && ($this->id > 0)) {
				$this->sitemap_id = $db->getProperty('table=>blog_sitemap', "where=>(`blog_id` = " . $db->escape($this->id) . ") AND (`deleted_at` IS NULL)", 'property=>sitemap_id');

			}
			if (is_numeric($this->sitemap_id)) {
				return $this->sitemap_id;
			}
			return get_var('level_id');
		} // section
		
		function show_comments() {
			if (!is_bool($this->_show_comments)) {
				if ((section('comments') == 'enable') && is_plugin('comments') && plugin_is_installed('comments')) {
					load_plugin('comments');	
					$this->_show_comments = true;
				//} else if (install_plugin('comments') === true) {
				//	load_plugin('comments');
				//	$this->_show_comments = true;
				} else {
					$this->_show_comments = false;
				}
			}
			
			return $this->_show_comments;
		} // show_comments
		
		function sidebarContent() {
			return array_merge($this->media('photo'), $this->media('pdf'), $this->media('file'));
		} // sidebarContent

		function store() {
			//storage("blogs[$this->id]", get_object_vars($this));
		} // store
				
		function title() {
			return valid($this->title);
		} // title
		
		function thumb($width = 85, $height = 85, $cropratio = '1:1') {
			global $db;
			
			if (!is_string($this->thumb) || ($this->thumb != 'false')) {

				// if we don't know if there's a thumb yet...
				if (empty($this->thumb)) {
					// figured it out and go get it.
					$thumb = $this->get_thumb();
					$this->thumb = (is_string($thumb)) ? $thumb : 'false';
					$db->update('table=>' . $this->table, 'id=>' . $this->id, array('thumb' => $this->thumb));
				}
				
				// if theres a thumbnail specified, return that...
				if (!in_array($this->thumb, array('', 'false'))) {
					return MEDIAPATH . add_photo_info($this->thumb, $width, $height, $cropratio);
				}
				
			}
			
			return false;
		} // thumb
			
		function get_thumb() {
			//$info = '?width=' . $width . '&amp;height=' . $height . '&amp;cropratio=' . $cropratio;
			if (is_array($this->_thumb)) :
				return $this->_thumb['versioned'];
			endif;
			$thumb = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '-thumb.gif');
			if ($thumb['found']) :
				$this->_thumb = $thumb;
				return $this->_thumb['versioned'];
			endif;
			$thumb = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '-thumb.jpg');
			if ($thumb['found']) :
				$this->_thumb = $thumb;
				return $this->_thumb['versioned'];
			endif;
			$thumb = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '-thumb.png');
			if ($thumb['found']) :
				$this->_thumb = $thumb;
				return $this->_thumb['versioned'];
			endif;
			
			return false;
		} // get_thumb
		
		function wasFound() {
			return $this->_exists;
		} // wasFound
		
	} // blog class

?>