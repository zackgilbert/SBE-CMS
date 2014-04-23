<?php

	class gallery_photo {
		
		var $table = 'gallery_photos'; 
		var $_table = 'gallery photo';
		var $_exists = false;
		var $id = false;
		var $gallery_id = false;
		var $_gallery = false;
		var $order = false;
		var $times_viewed = false;
		var $comment_status = false;
		var $comment_count = false;
		var $_show_comments;
		var $created_at = false;
		var $updated_at = false;
		var $deleted_at = false;
		var $_link = false;
		var $_media = false;
		var $_photo = false;
		var $_previousPhoto = false;
		var $_nextPhoto = false;
		var $_comments = false;
		var $_use_comments = null;
		
		function gallery_photo($fields = false, $album = false) {
			if (is_array($fields)) {
				$this->_setFields($fields);
			} else if (is_numeric($fields)) {
				$this->id = $fields;
				$this->_byId();
			}
			
			if (!is_bool($album)) {
				if (is_numeric($album) && ($this->gallery_id !== $album)) {
					$this->_exists = false;
				}
			}
			
			return $this;
		} // gallery_photo constructor
		
		function _byId() {
			global $db;
			
			$sql = "SELECT * FROM `" . $db->escape($this->table) . "` WHERE (`id` = " . $db->escape($this->id) . ") AND (`deleted_at` IS NULL)";				
			//$items = $db->get($sql);
			if (!($items = get_memcache($sql))) {
				$items = $db->get($sql);
				set_memcache($sql, $items);
			}
			
			if (!is_array($items)) {
				error('Attempting to get a single ' . $this->_table . ' by id (' . $this->id . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
			} else if (count($items) < 1) {
				//warning('Attempting to get a single ' . $this->_table . ' by id (' . $this->id . ') has failed. An empty array was returned for supplied id. This suggests that it does not exist.', __FILE__, __FUNCTION__, __LINE__);
			} else if (count($items) > 1) {
				warning('Attempting to get a single ' . $this->_table . ' by id (' . $this->id . ') has failed. ' . count($items) . " " . $this->table . " were returned for supplied id. The first returned has been used.", __FILE__, __FUNCTION__, __LINE__);
			}
				
			$item = (isset($items[0])) ? $items[0] : false;
				
			if (is_array($item)) {
					
				$this->_setFields($item);
			
			} else {
				//error('Attempting to get a single ' . $this->_table . ' by id (' . $this->id . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
			}

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
		
		function _updateCommentCount() {
			global $db;
			if (!is_array($this->_comments))
				$this->comments();
			$this->comment_count = count($this->_comments);
			//$this->_deleteCache();
			return $db->update('table=>' . $this->table, 'id=>' . $this->id, array('comment_count' => $this->comment_count));
		} // _updateCommentCount		
		/*function _updateCommentCount() {
			global $db;
			if ($this->use_comments()) {
				if (!is_array($this->_comments))
					$this->comments();
				if ($this->comment_count != count($this->_comments)) {
					$this->comment_count = count($this->_comments);
					return $db->update('table=>' . $this->table, 'id=>' . $this->id, array('comment_count' => $this->comment_count));
				}
			}
			return false;
		} // _updateCommentCount*/
		
		function alt() {
			$alt = array();
			if ($this->title())
				$alt[] = $this->title();
			if ($this->description())
				$alt[] = $this->description();
			return join(" - ", $alt);
		} // alt
		
		function comments($numberToReturn = 0, $force = false) {
			
			if (!$this->show_comments())
				return false;
			
			if (!is_array($this->_comments) || $force) {
				
				$this->_comments = get_comments($this->table, $this->id);
				
				if (!is_array($this->_comments)) {
					warning('Attempting to get gallery comments has failed for user (id: ' . $this->id . ', url: ' . $this->url . ').', __FILE__, __FUNCTION__, __LINE__);
				}
				
			}
			
			if (is_array($this->_comments) && ($this->comment_count != count($this->_comments))) {
				$this->_updateCommentCount();
			}
			
			return array_max($this->_comments, $numberToReturn);
		} // comments		
		/*function comments($numberToReturn = 0, $force = false) {
			
			if (!$this->use_comments())
				return false;
			
			if (!is_array($this->_comments) || $force) {
				
				if (is_plugin('comments')) {
					require_plugin('comments');
					
					$comments = get_comments($this->table, $this->id);
					
					if (!is_array($comments)) {
						warning('Attempting to get ' . $this->_table . '\'s comments has failed (id: ' . $this->id . ', url: ' . $this->url . ').', __FILE__, __FUNCTION__, __LINE__);
					}
					
					if (is_array($comments)) {
						$this->_comments = array();
						foreach ($comments as $commentNum => $comment) {
							$this->_comments[$comment['id']] = new comment($comment);
						}
					}
					
				} else {
				
					warning('Attempting to get ' . $this->_table . '\'s comments has failed (id: ' . $this->id . ', url: ' . $this->url . ') because comments plugin is not installed.', __FILE__, __FUNCTION__, __LINE__);
					
				}

			}
			
			if ($this->comment_count != count($this->_comments)) {
				$this->_updateCommentCount();
			}
			
			return array_max($this->_comments, $numberToReturn);
		} // comments*/
		
		function comment_status($check = false) {
			$status = ($this->comment_status != 'inherit') ? $this->comment_status : $this->parent('comment_status');
			return (is_string($check)) ? ($check === $status) : $status;
		} // comment_status
		
		function description() {
			return valid($this->media('description'));
		} // description
		
		function display_comments() {
			if ($this->show_comments())
				comments($this->table, $this->id);
		} // display_comments
		
		function parent($return = false) {
			global $db;

			// get the parent gallery, based on this photos gallery_id
			if (!is_array($this->_gallery)) {
				if (!($album = get_memcache('galleries--' . $this->gallery_id))) {
					$album = $db->getOne('table=>galleries', "where=>(`id` = " . $db->escape($this->gallery_id) . ") AND (`deleted_at` IS NULL)");
					set_memcache('galleries--' . $this->gallery_id, $album);
				}
				$this->_gallery = get_gallery($album);
			}
			
			if (!is_bool($return)) {
				if (is_array($this->_gallery)) {
					if (isset($this->_gallery[$return])) {
						return $this->_gallery[$return];
					} else {
						return false;
					}
				} else if (is_object($this->_gallery)) {
					if (method_exists($this->_gallery, $return)) {
						return $this->_gallery->$return();
					} else if (isset($this->_gallery->$return)) {
						return $this->_gallery->$return;
					} else {
						return false;
					}
				}
			}
			return $this->_gallery;
		} // parent
		
		function media($return = false) {
			global $db;

			if (!is_array($this->_media)) {
				$this->_media = $db->getOne('table=>media', "where=>(`type` = 'photo') AND (`table` = '" . $db->escape($this->table) . "') AND (`table_id` = " . $db->escape($this->id) . ") AND (`deleted_at` IS NULL)");					
			}
			
			if (!is_bool($return)) {
				if (isset($this->_media[$return])) {
					return $this->_media[$return];
				} else {
					return false;
				}
			}
			return $this->_media;			
		} // media
		
		function _getPhoto() {
			if (is_array($this->_photo)) {
				return $this->_photo;
			}
			$this->_photo = get_presentation_file($this->media('location'));
			if ($this->_photo['found']) {
				return $this->_photo;
			}
			return false;
		} // _getPhoto
		
		function photo($width = false, $height = false, $cropratio = false) {
			if ($this->_getPhoto()) {
				return add_photo_info($this->_photo['versioned'], $width, $height, $cropratio);
			}
			return false;
		} // photo
		
		function next($return = false) {
			
			if (!is_array($this->_nextPhoto)) {
				$photos = $this->parent('photos');
				foreach ($photos as $key => $photo) {
					if ($photo->id === $this->id) {
						$this->_nextPhoto = next($photos);
						break;
					}
					next($photos);
				}
			}

			if (!is_bool($return)) {
				if (is_array($this->_nextPhoto)) {
					if (isset($this->_nextPhoto[$return])) {
						return $this->_nextPhoto[$return];
					} else {
						return false;
					}
				} else if (is_object($this->_nextPhoto)) {
					if (method_exists($this->_nextPhoto, $return)) {
						return $this->_nextPhoto->$return();
					} else if (isset($this->_nextPhoto->$return)) {
						return $this->_nextPhoto->$return;
					} else {
						return false;
					}
				}
			}
			return $this->_nextPhoto;
		} // next
		
		function previous($return = false) {

			if (!is_array($this->_previousPhoto)) {
				$photos = $this->parent('photos');
				foreach ($photos as $key => $photo) {
					if ($photo->id === $this->id) {
						$this->_previousPhoto = prev($photos);
						break;
					}
					next($photos);
				}
			}

			if (!is_bool($return)) {
				if (is_array($this->_previousPhoto)) {
					if (isset($this->_previousPhoto[$return])) {
						return $this->_previousPhoto[$return];
					} else {
						return false;
					}
				} else if (is_object($this->_previousPhoto)) {
					if (method_exists($this->_previousPhoto, $return)) {
						return $this->_previousPhoto->$return();
					} else if (isset($this->_previousPhoto->$return)) {
						return $this->_previousPhoto->$return;
					} else {
						return false;
					}
				}
			}
			return $this->_previousPhoto;
		} // previous
		
		function link() {

			if (!is_string($this->_link)) {
				$this->_link = $this->parent('link') . $this->id . "/";
			}
			
			return $this->_link;
		} // link
		
		function show_comments() {
			if (!is_bool($this->_show_comments)) {				
				if ((section('comments') == 'enable') && is_plugin('comments') && plugin_is_installed('comments')) {
					load_plugin('comments');	
					$this->_show_comments = true;
				//} else if (install_plugin('comments') === true) {
				//	dumped('just installed');
				//	load_plugin('comments');
				//	$this->_show_comments = true;
				} else {
					$this->_show_comments = false;
				}
			}
			
			return $this->_show_comments;
		} // show_comments
						
		function store() {
		} // store
		
		function thumb($width = 50, $height = 50, $cropratio = '1:1') {
			return $this->photo($width, $height, $cropratio);
		} // thumb
		
		function title() {
			if (is_string($this->media('title')) && ($this->media('title') != '')) {
				return valid($this->media('title'));
			} else if (is_string($this->media('description')) && ($this->media('description') != '')) {
				return valid($this->media('description'));
			} else {
				return valid($this->parent('title'));
			}
		} // title
		
		function wasFound() {
			return $this->_exists;
		} // wasFound
		
	} // class gallery_photo

?>