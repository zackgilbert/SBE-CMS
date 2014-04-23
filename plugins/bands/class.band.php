<?php

	class band {
		
		var $table = 'bands';
		var $_table = false;
		var $_exists = false;
		var $_link = false;
		var $_media = false;
		var $_photo = false;
		var $id = false;
		var $sitemap_id = false;
		var $url = false;
		var $name = false;
		var $tagline = false;
		var $members = false;
		var $biography = false;
		var $website_url = false;
		var $thumb = false;
		
		function band($fields) {
			$this->_table = singularize($this->table);
			
			if (is_array($fields) && !isset($fields['t'])) {
				$this->_setFields($fields);
			} else if (is_array($fields) && isset($fields['id'])) {
				$this->id = $fields['id'];
				$this->_byId();
			} else if (is_numeric($fields)) {
				$this->id = $fields;
				$this->_byId();
			} else if (is_string($fields) && !empty($fields)) {
				$this->url = $fields;
				$this->_byUrl();
			}
			
			return $this;
		} // artist constructor
		
		function _byId() {
			global $db;
			
			// get it from the db
			$sql = "SELECT * FROM `" . $db->escape($this->table) . "` WHERE (`id` = " . $db->escape($this->id) . ")";				
			$items = $db->get($sql);
			
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
				error('Attempting to get a single ' . $this->_table . ' by id (' . $this->id . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
			}

		} // _byId
		
		function _byUrl() {
			global $db;
			
			// get it from the db
			$sql = "SELECT * FROM `" . $db->escape($this->table) . "` WHERE ((`url` LIKE '" . $db->escape($this->url) . "') OR (`url` LIKE '" . $db->escape(str_replace(" ", "_", $this->url)) . "')) AND (`deleted_at` IS NULL)";
			$items = $db->get($sql);
				
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
				error('Attempting to get a single ' . $this->_table . ' by url (' . $this->url . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
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
		
		function audio($numberToReturn = false) {
			return array_max($this->media('audio'), $numberToReturn);
		} // audio
		
		function bio() {
			return $this->biography();
		} // bio
		
		function biography() {
			return $this->biography;
		} // biography
		
		function hadErrors() {
			return $this->_errors;
		} // hadErrors

		function hasAudio() {
			if (count($this->audio()) > 0) {
				return true;
			}
			return false;
		} // hasAudio

		function hasPhoto() {
			if (is_array($this->_photo)) {
				return $this->_photo;
			}
			$photo = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '.gif');
			if ($photo['found']) {
				return $this->_photo = $photo;
			}
			$photo = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '.jpg');
			if ($photo['found']) {
				return $this->_photo = $photo;
			}
			$photo = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '.png');
			if ($photo['found']) {
				return $this->_photo = $photo;
			}
			return false;
		} // hasPhoto
		
		function hasPhotos() {
			if (count($this->photos()) > 0) {
				return true;
			}
			return false;
		} // hasPhotos
		
		function hasShows() {
			if (count($this->shows()) > 0) {
				return true;
			}
			return false;
		} // hasShows
		
		function hasVideos() {
			if (count($this->videos()) > 0) {
				return true;
			}
			return false;
		} // hasVideos
		
		function link() {

			if (!is_string($this->_link)) {
				$base_url = get_sitemap_section_url($this->section());
				$this->_link = $base_url . $this->url . "/";
			}
			
			return $this->_link;
		} // link
		
		function media($return = false) {
			global $db;
			
			if (!is_array($this->_media)) {
				
				$this->_media = $db->get('table=>media', "where=>(`table` = '" . $this->table . "') AND (`table_id` = " . $db->escape($this->id) . ") AND (`deleted_at` IS NULL)");
				
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
		} // media
		
		function members() {
			return format_text($this->members);
		} // members
		
		function name() {
			return valid($this->name);
		} // name
		
		function photo($width = 150, $height = 150, $cropratio = '1:1') {
			//$info = '?width=' . $width . '&amp;height=' . $height . '&amp;cropratio=' . $cropratio;
			if ($this->hasPhoto()) :
				return add_photo_info($this->_photo['versioned'], $width, $height, $cropratio);
			endif;
			return add_photo_info(upload_folder($this->table) . '0.gif', $width, $height, $cropratio);
			// return LOCATION . 'images/uploads/' . $this->table . '-0.gif' . $info;
		} // photo
		
		function photopath() {
			if ($this->hasPhoto()) :
				return $this->_photo['path'] . $this->_photo['name'] . "." . $this->_photo['ext'];
			endif;
			return false;
		} // photopath
		
		function photos() {
			return $this->media('photo');
		} // photos
		
		function section() {
			if (is_numeric($this->sitemap_id))
				return $this->sitemap_id;
			$level_id = get_var('level_id');
			if (is_numeric($level_id))
				return $this->sitemap_id = $level_id;
			$id = get_var('id');
			if (is_numeric($id))
				return $this->sitemap_id = $id;
			$section = get_var('section');
			if (is_numeric($section))
				return $this->sitemap_id = $section;
			$levels = get_sitemap_sections_by_content('bands');
			if (isset($levels[0]['id']))
				return $this->sitemap_id = $levels[0]['id'];
			return false;
		} // section
		
		function store() {
			//storage("$this->table[$this->id]", get_object_vars($this));
		} // store
		
		function tagline() {
			return str_replace('"', '&quot;', valid($this->tagline));
		} // tagline
		
		function thumb($width = 50, $height = 50, $cropratio = '1:1') {
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
			if ($this->hasPhoto()) {
				return $this->_photo['versioned'];
			}
			if (file_exists(upload_folder($this->table, true) . '0.gif')) {
				return upload_folder($this->table) . "0.gif";
			}
			if (file_exists(PLUGINS . $this->table . '/images/0.gif')) {
				return 'plugins/' . $this->table . '/images/0.gif';
			}
			return upload_folder() . 'photos-0.gif';
		} // get_thumb
		
		function title() {
			return $this->name();
		} // title
		
		function videos($numberToReturn = false) {
			return array_max($this->media('video'), $numberToReturn);
		} // videos
		
		function wasFound() {
			return $this->_exists;
		} // wasFound

	} // band

?>