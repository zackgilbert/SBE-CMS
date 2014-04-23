<?php

	class comment {
		
		var $_author = false;
		var $_exists = false;
		var $_parent = false;
		var $count = 0;
		var $id = false;
		var $table = false;
		var $table_id = false;
		var $user_id = false;
		var $name = false;
		var $email = false;
		var $url = false;
		var $comment = false;
		var $rating = false;
		var $approved_at;
		var $moderated_at;
		var $created_at;
		var $updated_at;
		var $deleted_at;
		
		function comment($comment = false, $count = 0) {
			if (is_array($comment)) {
				$this->_setFields($comment);
			} else if (is_numeric($comment)) {
				$this->id = $comment;
				$this->_byId();
			}
			
			return $this;
		} // comment constructor
		
		function _byId() {
			global $db;
			
			if (!($fields = get_memcache('comments--' . $this->id))) {
				$fields = $db->getOne('table=>comments', 'where=>(`id` = ' . $db->escape($this->id) . ')');
				set_memcache('comments--' . $this->id, $fields);
			}
			
			if ($fields) {
				return $this->_setFields($fields);				
			}
			
			return false;
		} // _byId
		
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
		
		function author($return = false) {
			
			/*if (!is_array($this->_author)) {
				$this->_author = get_profile($this->user_id);
			}
			
			if (!is_array($this->_author) && !is_object($this->_author)) {
				warning('Attempting to get forum author has failed (forum id: ' . $this->id . ', author id: ' . $this->user_id . ').', __FILE__, __FUNCTION__, __LINE__);
			}
			
			if ($return) {
				if (is_object($this->_author)) {
					if (method_exists($this->_author, $return)) {
						return $this->_author->$return();
					} else {
						if (isset($this->_author->$return)) return $this->_author->$return;
					}
				} else if (is_array($this->_author)) {
					if (isset($this->_author[$return])) return $this->_author[$return];
				}
				return false;
			}
			
			return $this->_author;*/
			return false;
		} // author
		
		function authorlink() {
			
			if (is_string($this->name)) {
				// use supplied info
				if (is_string($this->url) && !empty($this->url)) {
					// if url is supplied, link it.
					return '<a href="' . $this->url . '">' . $this->name . '</a>';
				}
				return $this->name;
			//} else if (is_numeric($this->user_id) && $this->author()) {
				// use profile info
			//	return '<a href="' . $this->author('link') . '">' . $this->author('name') . '</a>';
			}
			
		} // authorlink
		
		function authorthumb($default = false) {
			/*$author = $this->author();
			if (is_object($author) && $author->wasFound() && method_exists($author, 'thumb')) {
				return $author->thumb();
			}*/
			if (is_string($default)) {
				return $default;
			}
			return add_photo_info(upload_folder('users') . '0.gif', 50, 50, '1:1');
			// return LOCATION . 'images/uploads/user_profiles-0.gif?width=50&amp;height=50';
		} // authorthumb
		
		function delete() {
			global $db;

			if (!$this->_exists || ($this->id <= 0)) {
				warning('Attempting to delete comment (' . $this->id . ') failed because comment does not exist.', __FILE__, __FUNCTION__, __LINE__);
				return false;
			}
			
			// delete comment from comments table,
			if (!$db->delete('table=>comments', "id=>" . $this->id, false)) {
				warning('An error occurred when trying to delete comment (' . $this->id . ') from comments table in database.', __FILE__, __FUNCTION__, __LINE__);
				return false;
			}
			
			flush_memcache();
			
			return true;
		} // delete
		
		function isApproved() {
			if (is_string($this->approved_at)) {
				return true;
			}
			return false;
		} // isApproved

		function isModerated() {
			if (is_string($this->moderated_at)) {
				return true;
			}
			return false;
		} // isModerated

		function isReview() {
			if (is_numeric($this->rating) && ($this->rating >= 0) && ($this->rating <= 10)) {
				return true;
			}
			return false;
		} // isReview

		function link() {
			return $this->parent('link') . '#comment-' . $this->id;
		} // link

		function linkToParent() {
			//dump($this->parent());
			if ($this->parent('link')) {
				return '<a href="' . $this->parent('link') . '">' . $this->parent('title') . '</a>';
			} else {
				return $this->parent('title');
			}
		} // linkToParent

		function parent($return = false) {
			global $db;
			
			if (is_bool($this->_parent)) {
				if (is_plugin($this->table))
					load_plugin($this->table);
				if (function_exists('get_'.singularize($this->table))) {
					$this->_parent = call_user_func('get_'.singularize($this->table), $this->table_id);
				} else {
					// this is the catch all way of doing it... that only works for articles and blogs...
					$this->_parent = $db->getOne('table=>' . $this->table, 'id=>' . $this->table_id);
					//if (!isset($this->_parent['sitemap_id'])) {
					//	$this->_parent['sitemap_id'] = $db->getProperty('table=>' . singularize($this->table) . '_sitemap', "where=>(`" . singularize($this->table) . "_id` = " . $db->escape($this->table_id) . ") AND (`deleted_at` IS NULL)", 'property=>sitemap_id');
					//}
					// if theres no title, that means its most likely a gallery photo... for now...
					if (!isset($this->_parent['title']) && !isset($this->_parent['url'])) {
						$this->_parent['title'] = $db->getProperty('table=>media', "where=>(`table` = '" . $db->escape($this->table) . "') AND (`table_id` = " . $db->escape($this->table_id) . ") AND (`deleted_at` IS NULL)", 'property=>title');
					}
					
					if (isset($this->_parent['sitemap_id']) && isset($this->_parent['url'])) {
						$this->_parent['link'] = get_sitemap_section_url($this->_parent['sitemap_id']) . $this->_parent['url'] . '/';
					} else {
						$this->_parent['link'] = false;
					}
					
				}
			}
			
			if ($return) {
				if (is_object($this->_parent)) {
					if (method_exists($this->_parent, $return)) {
						return $this->_parent->$return();
					} else {
						if (isset($this->_parent->$return)) return $this->_parent->$return;
					}
				} else if (is_array($this->_parent)) {
					if (isset($this->_parent[$return])) return $this->_parent[$return];
				}
				return false;
			}
			return $this->_parent;
		} // parent

		function store() {
		} // store

		function wasFound() {
			return $this->_exists;
		} // wasFound
		
	} // comment class

?>