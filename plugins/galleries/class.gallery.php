<?php

	class gallery {
		
		var $table = 'galleries'; 
		var $_table = 'gallery';
		var $_exists = false;
		var $id = false;
		var $sitemap_id = false;
		var $url = false;
		var $name = false;
		var $description = false;
		var $num_photos = false;
		var $_comment_status_options = array("open" => "Open", "closed" => "Closed", "registered_only" => "Registered Users Only");
		var $comment_status = 'open';
		var $thumb = false;
		var $published_at = false;
		var $created_at = false;
		var $updated_at = false;
		var $deleted_at = false;
		var $_link = false;
		var $_photo = false;
		var $_photos = false;
		
		
		function gallery($fields = false) {
			if (is_array($fields) && !isset($fields['t'])) {
				$this->_setFields($fields);
			} else if (is_array($fields) && isset($fields['id'])) {
				$this->id = $fields['id'];
				$this->_byId();
			} else if (is_numeric($fields)) {
				$this->id = $fields;
				$this->_byId();
			} else if (is_string($fields)) {
				$this->url = $fields;
				$this->_byUrl();
			}
			
			return $this;
		} // gallery constructor
		
		function _byId() {
			global $db;
			
			$sql = "SELECT * FROM `" . $db->escape($this->table) . "` WHERE (`id` = " . $db->escape($this->id) . ") AND (`deleted_at` IS NULL)";				
			//$items = $db->get($sql);
			if (!($items = get_memcache('galleries--' . $this->id))) {
				$items = $db->get($sql);
				set_memcache('galleries--' . $this->id, $items);
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
		
		function _byUrl() {
			global $db;
			
			// get it from the db
			$sql = "SELECT * FROM `" . $db->escape($this->table) . "` WHERE (`url` = '" . $db->escape($this->url) . "') AND (`deleted_at` IS NULL)";
			//$items = $db->get($sql);
			if (!($items = get_memcache($sql))) {
				$items = $db->get($sql);
				set_memcache($sql, $items);
			}
				
			if (!is_array($items)) {
				error('Attempting to get a single ' . $this->_table . ' by url (' . $this->url . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
			} else if (count($items) < 1) {
				//warning('Attempting to get a single ' . $this->_table . ' by url (' . $this->url . ') has failed. An empty array was returned for supplied url.', __FILE__, __FUNCTION__, __LINE__);
			} else if (count($items) > 1) {
				warning('Attempting to get a single ' . $this->_table . ' by url (' . $this->url . ') has failed. ' . count($items) . " " . $this->table . " were returned for supplied url. The first returned has been used.", __FILE__, __FUNCTION__, __LINE__);
			}
				
			$item = (isset($items[0])) ? $items[0] : false;
				
			if (is_array($item)) {
					
				$this->_setFields($item);
		
			} else {
				//error('Attempting to get a single ' . $this->_table . ' by url (' . $this->url . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
			}

		} // _byUrl
	
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
		
		function description() {
			return valid(str_replace('<br>', "<br/>", nl2br($this->description)));
		} // description
		
		function hasPhoto() {
			if (is_array($this->_photo)) :
				return true;
			endif;
			$photo = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '.gif');
			if ($photo['found']) :
				$this->_photo = $photo;
				return true;
			endif;
			$photo = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '.jpg');
			if ($photo['found']) :
				$this->_photo = $photo;
				return true;
			endif;
			$photo = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '.png');
			if ($photo['found']) :
				$this->_photo = $photo;
				return true;
			endif;
			return false;
		} // hasPhoto
		
		function link() {

			if (!is_string($this->_link)) {
				$this->_link = get_sitemap_section_url($this->sitemap_id) . $this->url . "/";
			}
			
			return $this->_link;
		} // link
		
		function photo($width = false, $height = false, $cropratio = false) {
			if ($this->hasPhoto()) {
				return add_photo_info($this->_photo['versioned'], $width, $height, $cropratio);
			}
			/*if ($this->photos()) {
				$photos = $this->photos();
				return $photos[0]->thumb($width, $height, $cropratio);
			}*/
			return upload_folder($this->table) . '0.gif' . $info;
		} // photo

		function photopath() {
			if ($this->hasPhoto()) :
				return $this->_photo['path'] . $this->_photo['name'] . "." . $this->_photo['ext'];
			endif;
			return false;
		} // photopath
		
		function photos() {
			
			if (!is_array($this->_photos)) {
				
				$this->_photos = get_gallery_photos($this->id);
				
			}
			
			return $this->_photos;
		} // photos
		
		function photo_count() {
			global $db;
			
			if (is_array($this->photos())) {
			
				if (count($this->photos()) != $this->num_photos) {
					$db->update('table=>' . $db->escape($this->table), 'id=>' . $db->escape($this->id), array('num_photos' => count($this->photos())));
				}
				
				return count($this->photos());
				
			}

			return $this->num_photos;
		} // photo_count
		
		function section() {
			return $this->sitemap_id;
		} // section
				
		function store() {
		} // store
		
		function thumb($width = 50, $height = 50, $cropratio = '1:1') {
			global $db;
			
			if (!is_string($this->thumb) || ($this->thumb != 'false')) {

				// if we don't know if there's a thumb yet...
				if (empty($this->thumb)) {
					// figured it out and go get it.
					$thumb = $this->get_thumb();
					$this->thumb = (is_string($thumb)) ? $thumb : 'false';

					if (is_numeric($this->id))
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
			if ($this->hasPhoto()) {
				return $this->_photo['versioned'];
			}
			//if ($photos = $this->photos()) {
			//	$photos[0]->_getPhoto();
			//	return $photos[0]->_photo['versioned'];
			//}
			if (file_exists(PLUGINS . $this->table . '/images/0.gif')) {
				return 'plugins/' . $this->table . '/images/0.gif';
			}
			return upload_folder() . 'photos-0.gif';
		} // get_thumb
		
		/*
		function thumb($width = 50, $height = 50, $cropratio = '1:1') {
			return $this->photo($width, $height, $cropratio);
		} // thumb
		*/
		
		function title() {
			return valid($this->name);
		} // title
		
		function wasFound() {
			return $this->_exists;
		} // wasFound
		
	} // class gallery

?>