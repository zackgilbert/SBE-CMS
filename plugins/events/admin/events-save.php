<?php

	/*
	**********************************************
	**
	**	File: site/default/scripts/events-save.php
	**	Creation Date: 8/7/08
	**	Description: Save an event to the database
	**	Called From: site/default/includes/event-submitnew.php
	**
	**********************************************
	*/
	clear_field_values();
	clear_required_fields();
	
	require_once(LIBRARY . 'handleRequired.php');
	
	$table = 'event';
	include(LIBRARY . 'handlePostVars.php');
	
	//dump($event);	
	
	// lets start with a fresh array... too many non-database fields submitted...
	$new_event = array();
	
	// take care of fields that have a direct relation with database fields (name, event_category_id, subcategory, start_date, hours, location, price, age, contact, description, ends_on)
	if (isset($event['id']))
		$new_event['id'] = $event['id'];
	$new_event['name'] = $event['name'];
	$new_event['event_category_id'] = $event['category'];
	if (isset($event['subcategory']) && !empty($event['subcategory']))
		$new_event['subcategory'] = $event['subcategory'];
	if (isset($event['hours']) && !empty($event['hours']))
		$new_event['hours'] = strip_tags($event['hours']);
	if (isset($event['price']) && !empty($event['price']))
		$new_event['price'] = $event['price'];
	if (isset($event['age']) && !empty($event['age']))
		$new_event['age'] = $event['age'];	
	$new_event['description'] = strip_tags($event['description']);
	if (isset($event['ends_on']) && !empty($event['ends_on']))
		$new_event['ends_on'] = $event['ends_on'];
	
	if (isset($event['sitemap_id']) && is_numeric($event['sitemap_id'])) {
		$new_event['sitemap_id'] = $event['sitemap_id'];
	} else {
		$section = get_sitemap_section('events', 'content');
		$new_event['sitemap_id'] = $section['id'];
	}
	
	//$new_event['location'] = $event['location'];
	if (isset($event['location']) && is_numeric($event['location'])) {
		$new_event['location'] = $event['location'];
	} else if (isset($event['venue']) && is_array($event['venue']) && !empty($event['venue']['name'])) {
		// insert new venue into database...
		$venue = $event['venue'];
		$venue['url'] = url_friendly($venue['name']);
		$venue['sitemap_id'] = $new_event['sitemap_id'];
		
		$db->insert('table=>event_venues', $venue);
		$new_event['location'] = $venue_id = $db->last_id;
	} elseif (!empty($event['location'])) {
		$new_event['location'] = $event['location'];
	} else {
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		$_SESSION['requiredFields'] = array('event[location]');
		failure('Events require a location so people know where the event is occurring.');
		redirect_failure();
	}
	
	
	if (isset($event['contact']) && !empty($event['contact'])) {
		$new_event['contact'] = $event['contact'];
	} else {
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		$_SESSION['requiredFields'] = array('event[contact]');
		failure('Contact information for this event is required. Please supply one before submitting your event.');
		redirect_failure();
	}

	
	// take care of other fields... mostly date related...
	if ($event['date']['occurrence'] == 'once') {
		// once
		$new_event['start_date'] = $event['date']['once']['start_date'];
		if (isset($event['date']['once']['end_date']) && !empty($event['date']['once']['end_date']))
			$new_event['end_date'] = $event['date']['once']['end_date'];
			
	} elseif ($event['date']['occurrence'] == 'repeats') {
		// repeats
		if ($event['date']['repeats']['frequency'] == 'weeks') {
			// weeks ---- number of weeks | 'weeks' | days occurs (day or day,day or day+day)
			$new_event['repeats_every'] = $event['date']['repeats']['every'] . "|" . $event['date']['repeats']['frequency'] . "|" . $event['date']['repeats']['weeks']['start'];
			if (isset($event['date']['repeats']['weeks']['end']) && is_numeric($event['date']['repeats']['weeks']['end'])) {
				$new_event['repeats_every'] = $new_event['repeats_every'] . "," . $event['date']['repeats']['weeks']['end'];
			} else if (isset($event['date']['repeats']['weeks']['additional']) && is_numeric($event['date']['repeats']['weeks']['additional'])) {
				$new_event['repeats_every'] = $new_event['repeats_every'] . "+" . $event['date']['repeats']['weeks']['additional'];
			}
			
		} else if ($event['date']['repeats']['frequency'] == 'months') {
			// months ---- number of months | 'months' | days occurs on (num:day or num:day,num:day or num:day+num:day)
			$new_event['repeats_every'] = $event['date']['repeats']['every'] . "|" . $event['date']['repeats']['frequency'] . "|" . $event['date']['repeats']['months']['start']['num'] . ':' . $event['date']['repeats']['months']['start']['day'];
			
			if (isset($event['date']['repeats']['months']['end']) && is_array($event['date']['repeats']['months']['end']) && is_numeric($event['date']['repeats']['months']['end']['num'])) {
			
				$new_event['repeats_every'] = $new_event['repeats_every'] . "," . $event['date']['repeats']['months']['end']['num'] . ':' . $event['date']['repeats']['months']['end']['day'];
			
			} else if (isset($event['date']['repeats']['months']['additional']) && is_array($event['date']['repeats']['months']['additional']) && is_numeric($event['date']['repeats']['months']['additional']['num'])) {
			
				$new_event['repeats_every'] = $new_event['repeats_every'] . "+" . $event['date']['repeats']['months']['additional']['num'] . ':' . $event['date']['repeats']['months']['additional']['day'];
			
			}
			
		} else if ($event['date']['repeats']['frequency'] == 'years') {
			// years ---- number of years | 'years' | days occurs on (month-num-day or month-num-day,month-num-day or month-num-day+month-num-day)
			$new_event['repeats_every'] = $event['date']['repeats']['every'] . "|" . $event['date']['repeats']['frequency'] . "|" . $event['date']['repeats']['years']['start']['month'] . ':' . $event['date']['repeats']['years']['start']['num'] . ':' . $event['date']['repeats']['years']['start']['day'];
			
			if (isset($event['date']['repeats']['years']['end']) && is_array($event['date']['repeats']['years']['end']) && is_numeric($event['date']['repeats']['years']['end']['num'])) {
			
				$new_event['repeats_every'] = $new_event['repeats_every'] . "," . $event['date']['repeats']['years']['end']['month'] . ':' . $event['date']['repeats']['years']['end']['num'] . ':' . $event['date']['repeats']['years']['end']['day'];
			
			} else if (isset($event['date']['repeats']['years']['additional']) && is_array($event['date']['repeats']['years']['additional']) && is_numeric($event['date']['repeats']['years']['additional']['num'])) {
			
				$new_event['repeats_every'] = $new_event['repeats_every'] . "+" . $event['date']['repeats']['years']['additional']['month'] . ':' . $event['date']['repeats']['years']['additional']['num'] . ':' . $event['date']['repeats']['years']['additional']['day'];
			
			}
			
		}
		
		// start running this event
		if (isset($event['date']['repeats']['start_date']) && !empty($event['date']['repeats']['start_date'])) {
			$new_event['start_date'] = $event['date']['repeats']['start_date'];
		} else {
			$new_event['start_date'] = TODAY;
		}
		
		// when event should stop running
		if (isset($event['date']['repeats']['ends_on']) && !empty($event['date']['repeats']['ends_on'])) {
			$new_event['ends_on'] = $event['date']['repeats']['ends_on'];
		}
		
	} else {
		// ERROR!!!
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error submitting your event. We were entirely sure when your event was happening.');
		redirect_failure();
	}
	
	if (isset($_POST['publish']) || isset($_POST['publish-continue'])) {
		$new_event['published_at'] = NOW;
	}
	
	if (strpos($new_event['start_date'], "-") !== 4) {
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure("There was an error find when this event was happening. Please make sure all information was supplied.");
		redirect_failure();
	}
	
	if (isset($event['deleted_at']) && empty($event['deleted_at'])) {
		$new_event['deleted_at'] = NULL;
	}
	
	//dump($event);
	//dump($new_event);
	//storage('kill_me');
		
	if ($db->save('table=>events', $new_event)) {
		$event_id = (isset($new_event['id'])) ? $new_event['id'] : $db->last_id;
		
		$mediaErrors = array();
		fix_files_superglobal();
		
		$file = (isset($_FILES['event']['photo']) && is_array($_FILES['event']['photo'])) ? $_FILES['event']['photo'] : array();
		
		if (isset($file['tmp_name']) && isset($file['name']) && !empty($file['tmp_name']) && !empty($file['name'])) {
			$ftmp = $file['tmp_name'];
			$uploadfile = upload_folder('events') . $event_id . '.' . get_ext($file['name']);

			if (!($file_url = upload_media($file, $uploadfile, 'EVENT_THUMB'))) {
			//if (!upload($file, $uploadfile)) {
				// error uploading file
				$mediaErrors[] = array("photo" => "Your event has been submitted, but there was an error uploading the event's photo.");
					
			} else {
				$db->update('table=>events', 'id=>' . $event_id, array('thumb' => ''));
			}
			
		}
		
		if (count($mediaErrors) == 0) {
			
			flush_memcache();

			success('Your event was successfully saved.');
			
			if (isset($_POST['continue']) || isset($_POST['publish-continue'])) {
				if (substr($_POST['redirect']['failure'], -5) == '/add/') {
					redirect(substr($_POST['redirect']['failure'], 0, -5) . "/" . $event_id . "/");
				} else {
					redirect_failure();
				}
			} else {
				
				redirect_success();
	
			}
			
		} else {
			
			if (count($mediaErrors) == 1) {
				
				failure(current($mediaErrors[0]));
				
			} else {
				
				failure("There were a number of errors uploading media for this event.");
				
			}
			
			if (isset($blogs['id']) && substr($_POST['redirect']['failure'], -5) == '/add/') {
				redirect(substr($_POST['redirect']['failure'], 0, -5) . "/" . $event_id . "/");
			} else {
				$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
				redirect_failure();
			}
			
		}
		
	} else {
		
		$_SESSION[$table] = $_SESSION['postFields'] = $postFields;
		failure('There was an error submitting your event. This usually means an issue with the database. Try again in a few minutes.');
		redirect_failure();
		
	}
	
?>