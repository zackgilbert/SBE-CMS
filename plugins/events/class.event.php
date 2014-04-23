<?php

	class event {
		
		var $table = "events";
		var $_table = false;
		var $_attendees = false;
		var $_comments = false;
		var $_date = false;
		var $_exists = false;
		var $_link = false;
		var $_photo = false;
		var $_photos = false;
		var $_type = false;
		var $_videos = false;
		var $_venue = false;
		var $_artist = false;
		var $id = false;
		var $name = false;
		var $repeats_every = false;
		var $event_category_id = false;
		var $subcategory = false;
		var $start_date = false;
		var $end_date = false;
		var $ends_on = false;
		var $hours = false;
		var $location = false;
		var $price = false;
		var $age = false;
		var $contact = false;
		var $description = false;
		var $ends_at = false;
		var $created_by = false;
		var $_dates = false;
		var $created_at = false;
		var $updated_at = false;
		var $deleted_at = false;
		var $published_at = false;
		var $_status_options = array("public" => "Publish Publicly", "private" => "Publish Privately", "registered_only" => "Publish For Registered Users Only");
		var $status = 'public';
		var $_comment_status_options = array("open" => "Open", "closed" => "Closed", "registered_only" => "Registered Users Only");
		var $comment_status = 'open';
		var $comment_count = 0;
		var $_show_comments;
		var $thumb = false;
		
		var $_all_dates = false;
		var $_next_date = false;

		function event($fields = false, $date = false) {
			$this->_table = singularize($this->table);
			
			if ($fields !== false) {
				//if (is_array($fields) && !isset($fields['t'])) {
				if (is_array($fields) && (count($fields) > 3)) {
					$this->_setFields($fields);
					$this->_date = $date;
				} else if (is_array($fields) && isset($fields['id'])) {
					$this->_date = $date;
					$this->id = $fields['id'];
					$this->_byId();
				} else if (is_numeric($fields)) {
					$this->_date = $date;
					$this->id = $fields;
					$this->_byId();
				}
				if (isset($this->attending_on) && is_string($this->attending_on)) {
					$this->_date = $this->attending_on;
				}
				if (!$this->_date) {
					$this->_date = $this->nextDate();
					//$this->_date = current($this->allDates());
				}
			} else if (is_admin()) {
				$this->created_by = 'staff';
			}
			
			return $this;
		} // event constructor
		
		function _byId() {
			global $db;

			// get it from the db
			$events = $db->get('table=>events', "where=>(`id` = " . $db->escape($this->id) . ")");

			/*if (!is_array($events)) {
				error('Attempting to get an event by id (' . $this->id . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
			} else if (count($events) < 1) {
				warning('Attempting to get a single event by id (' . $this->id . ') has failed. An empty array was returned for supplied id. This suggests the event does not exist.', __FILE__, __FUNCTION__, __LINE__);
			} else if (count($events) > 1) {
				warning('Attempting to get a single event by id (' . $this->id . ') has failed. ' . count($events) . " events were returned for supplied id. First event returned has been used.", __FILE__, __FUNCTION__, __LINE__);
			}*/

			$event = (isset($events[0])) ? $events[0] : false;

			if (is_array($event)) {

				$this->_setFields($event);

			} else {
				//error('Attempting to get a single event by id (' . $this->id . ') has failed.', __FILE__, __FUNCTION__, __LINE__);
			}

		} // private byId

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
		} // private setFields

		function _updateCommentCount() {
			global $db;
			if (!is_array($this->_comments))
				$this->comments();
			if ($this->comment_count != count($this->_comments)) :
				$this->comment_count = count($this->_comments);
				return $db->update('table=>' . $this->table, 'id=>' . $this->id, array('comment_count' => $this->comment_count));
			endif;
			return false;
		} // _updateCommentCount

		function allDates($end_date = false) {
			global $db;
			// figure out all dates for an event...
			
			if (!$this->isRecurring()) {
				$days = dateRangeToArray($this->start_date, $this->end_date(), 'days');
			} else {				
				// if event is recurring, this is a little trickier...
				// have to figure out based on original date and repeat formula...
			
				$start_date = $this->start_date;
				
				if (!$end_date) {
					$end_date = ($this->ends_on && ($this->ends_on <= daysFromNow(90))) ? $this->ends_on : substr(daysFromNow(90), 0, 10);
				} else if ($this->ends_on && ($this->ends_on < $end_date)) {
					$end_date = $this->ends_on;
				}
				
				if (is_string($this->_dates) && !empty($this->_dates)) {
					$days = explode(",", $this->_dates);
					sort($days);
					$days = array_unique($days);
					
					//foreach ($days as $key => $day)
					//	if ($day < TODAY)
					//		unset($days[$key]);
					
					if (is_array($days) && (count($days) > 0)) {
						$latest_day = end($days);
						
						if ($latest_day >= $end_date) {
							return $days;
						} else {
							//dump("allDates -- #" . $this->id . " -- " . $latest_day . " >= " . $end_date);

							$current_date = $latest_day;	
						}

					} else {
						$days = array();
						$current_date = $start_date;						
					}
					
				} else {
					$days = array();
					$current_date = $start_date;
				}
				
				$numberOfDays = $this->numberOfDays();
				
				if (substr($this->repeats_every,0,1) == '|') $this->repeats_every = "1" . $this->repeats_every;
				$repeat = explode("|", $this->repeats_every);
				
				$year = year($start_date);
				$month = month($start_date);
				$day = day($start_date);
				$start_date = TODAY;
				
				//dump("allDates -- #" . $this->id . " -- " . $this->repeats_every . " -- " . $current_date . " -- " . $end_date . " -- " . join(",", $days));
							
				if ($repeat[1] == 'weeks') {
					// numbers of weeks | 'weeks' | day of the week (sun(0)-sat(6)) (+ day of the week)
					$weekday = dayOfWeek($start_date);
					//$weekday_offset = $repeat[2];	// old way... if only value was 0-6
					
					// week values -- can be a single digit of (0-6) but also +(0-6) for a second day or ,(0-6) for through
					$weekday_offset = substr($repeat[2], 0, 1);
					if ((strlen($repeat[2]) > 1) && substr($repeat[2], 1, 1) == "+") {
						$weekday_offset2 = substr($repeat[2], 2, 1) - $weekday_offset;
					} else if ((strlen($repeat[2]) > 1) && substr($repeat[2], 1, 1) == ',') {
						$numberOfDays = 1 + substr($repeat[2], 2, 1) - $weekday_offset;
					}
					
					$day = (((0+day($start_date))-$weekday)+$weekday_offset);
					if ($day < day($start_date)) $day += 7;
					$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
									
					while ($current_date <= ($end_date+($repeat[0]*7))) {
						if (isset($weekday_offset2) && is_numeric($weekday_offset2)) {
							$current_day = substr($current_date, 0, 10);
							if ($current_day >= $start_date) {
								$days[] = $current_day;
							}
							$current_day2 = substr(daysFromNow($weekday_offset2, $current_date), 0, 10);
							if ($current_day2 >= $start_date) {
								$days[] = $current_day2;
							}
						} else {
							for ($i=0; $i<$numberOfDays; $i++) {
								$current_day = substr(daysFromNow($i, $current_date), 0, 10);
								if ($current_day >= $start_date) {
									$days[] = $current_day;
								}
							}
						}
						
						$day += ($repeat[0]*7);
						$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
					}
					
				} else if ($repeat[1] == 'months') {
					
					$dates = $repeat[2];
					
					if (strpos($dates, '+') !== false) {
						list($date1, $date2) = explode("+", $dates);
						$date2 = explode(":", $date2);
						$inclusive = false;
					} else if (strpos($dates, ',') !== false) {
						list($date1, $date2) = explode(",", $dates);						
						$date2 = explode(":", $date2);
						$inclusive = true;
					} else {
						$date1 = $dates;
					}
					
					$date1 = explode(":", $date1);
					
					if (count($date1) == 2) {
						
						$month = month();
						$year = year();
						
						do {
							
							if ($date1[1] == 'day') {
								
								if ($date1[0] > 0) {
									$day = pad($date1[0]);				
								} else if ($date1[0] === '-1') {
									$day = day(timestamp($year, $month+1, 0));
								} else if ($date1[0] === '-2') {
									$day = day(timestamp($year, $month+1, -1));
								}
								
							} else if (is_numeric($date1[1])) {
								
								if ($date1[0] > 0) {
									// nth weekday (0-6 || sun-mon) of month
									$weekday = dayOfWeek(timestamp(false,$month,1));
									$weekday_offset = $date1[1];
									$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
									$day += ((($day>0)?-1:0)+$date1[0])*7;
								} else {
									// -nth weekday (0-6) of month
									$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
									$weekday = dayOfWeek($last_day_of_month);
									$weekday_offset = $date1[1];
									$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
									if ($day > daysInMonth($month, $year)) $day -= 7;
									$day += (($date1[0]+1)*7);
									//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
								}

							}

							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
							//if (($current_date >= $start_date) && ($current_date <= $end_date)) {
							if (($current_date >= $start_date)) {
								$days[] = $current_date;
							}
							
							if (isset($date2)) {
								
								if ($date2[1] == 'day') {

									if ($date2[0] > 0) {
										$day = pad($date2[0]);				
									} else if ($date2[0] === '-1') {
										$day = day(timestamp($year, $month+1, 0));
									} else if ($date2[0] === '-2') {
										$day = day(timestamp($year, $month+1, -1));
									}

								} else if (is_numeric($date2[1])) {

									if ($date2[0] > 0) {
										// nth weekday (0-6 || sun-mon) of month
										$weekday = dayOfWeek(timestamp(false,$month,1));
										$weekday_offset = $date2[1];
										$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
										$day += ((($day>0)?-1:0)+$date2[0])*7;
									} else {
										// -nth weekday (0-6) of month
										$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
										$weekday = dayOfWeek($last_day_of_month);
										$weekday_offset = $date2[1];
										$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
										if ($day > daysInMonth($month, $year)) $day -= 7;
										$day += (($date2[0]+1)*7);
										//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
									}

								}

								$current_date2 = format_date(timestamp($year, $month, $day), 'Y-m-d');
								if ($inclusive) {
									//dump($current_date);
									//dump($current_date2);
									$rangeDates = dateRangeToArray($current_date, $current_date2, 'days');
									foreach ($rangeDates as $rangeDate) {
										//if (($rangeDate != $current_date) && ($rangeDate >= $start_date) && ($rangeDate <= $end_date)) {
										if (($rangeDate != $current_date) && ($rangeDate >= $start_date)) {
											$days[] = $rangeDate;
										}
									}
								} else {
									//if (($current_date2 >= $start_date) && ($current_date2 <= $end_date)) {
									if (($current_date2 >= $start_date)) {
										$days[] = $current_date2;
									}
								}
								
							}
							
							$month++;
							
						} while ($current_date <= substr(monthsFromNow(1, $end_date), 0, 10));
						
					}
					
					/*
					if (($repeat[3] == 'day') && ($repeat[2] > 0)) {
						// nth day of the month (+ nth day of the month)
						$day = $repeat[2];
						$maxDaysInMonth = daysInMonth($month, $year);
						$tempDay = (($day > $maxDaysInMonth)) ? $maxDaysInMonth : $day;
						$current_date = format_date(timestamp($year, $month, $tempDay), 'Y-m-d');
					
						while ($current_date <= $end_date) {
							for ($i=0; $i<$numberOfDays; $i++) {
								$current_day = substr(daysFromNow($i, $current_date), 0, 10);
								if ($current_day >= $start_date) {
									$days[] = $current_day;
								}
							}

							$month += $repeat[0];
							$maxDaysInMonth = daysInMonth($month, $year);
							$tempDay = (($day > $maxDaysInMonth)) ? $maxDaysInMonth : $day;
							$current_date = format_date(timestamp($year, $month, $tempDay), 'Y-m-d');
						}
						
					} else if (($repeat[3] == 'day') && ($repeat[2] < 0)) {
						// -nth day of the month
						$month = $month+1;
						$day = $repeat[2];
						$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
					
					
						while ($current_date <= $end_date) {
							for ($i=0; $i<$numberOfDays; $i++) {
								$current_day = substr(daysFromNow($i, $current_date), 0, 10);
								if ($current_day >= $start_date) {
									$days[] = $current_day;
								}
							}

							$month = $month + $repeat[0];
							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
						}
						
					} else if (is_numeric($repeat[3]) && ($repeat[2] > 0)) {
						// handle the nth weekday of the month (+ nth weekday of the month)
						$weekday = dayOfWeek(timestamp(false,$month,1));
						$weekday_offset = $repeat[3];
						$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
						$day += ((($day>0)?-1:0)+$repeat[2])*7;
						$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
						
						while ($current_date <= $end_date) {
							for ($i=0; $i<$numberOfDays; $i++) {
								$current_day = substr(daysFromNow($i, $current_date), 0, 10);
								if ($current_day >= $start_date) {
									$days[] = $current_day;
								}
							}

							$month += $repeat[0];
							$weekday = dayOfWeek(timestamp($year,$month,1));
							$day = (((0+day(timestamp($year,$month,1)))-$weekday)+$weekday_offset);
							$day += ((($day>0)?-1:0)+$repeat[2])*7;

							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
						}
						
					} else if (is_numeric($repeat[3]) && ($repeat[2] <= 0)) {
						// handle the -nth weekday of the month
						$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
						$weekday = dayOfWeek($last_day_of_month);
						$weekday_offset = $repeat[3];
						$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
						if ($day > daysInMonth($month, $year)) $day -= 7;
						$day += (($repeat[2]+1)*7);
						$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
						
						while($current_date <= $end_date) {
							for ($i=0; $i<$numberOfDays; $i++) {
								$current_day = substr(daysFromNow($i, $current_date), 0, 10);
								if ($current_day >= $start_date) {
									$days[] = $current_day;
								}
							}

							$month += $repeat[0];
							$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
							$weekday = dayOfWeek($last_day_of_month);
							$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
							if ($day > daysInMonth($month)) $day -= 7;
							$day += (($repeat[2]+1)*7);
							
							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
						}
						
					}
					*/
				} else if ($repeat[1] == 'years') {
					$yearsToSkip = $repeat[0];
					$dates = $repeat[2];
					
					if (strpos($dates, '+') !== false) {
						list($date1, $date2) = explode("+", $dates);
						$date2 = explode(":", $date2);
						$inclusive = false;
					} else if (strpos($dates, ',') !== false) {
						list($date1, $date2) = explode(",", $dates);
						$date2 = explode(":", $date2);
						$inclusive = true;
					} else {
						$date1 = $dates;
					}
					
					$date1 = explode(":", $date1);
					
					if (count($date1) == 3) {
						
						$month = pad($date1[0]);
						$year = year();
						
						do {
							
							if ($date1[2] == 'day') {
								
								if ($date1[1] > 0) {
									$day = pad($date1[1]);				
								} else if ($date1[1] === '-1') {
									$day = day(timestamp($year, $month+1, 0));
								} else if ($date1[1] === '-2') {
									$day = day(timestamp($year, $month+1, -1));
								}
								
							} else if (is_numeric($date1[2])) {
								
								if ($date1[1] > 0) {
									// nth weekday (0-6 || sun-mon) of month
									$weekday = dayOfWeek(timestamp(false,$month,1));
									$weekday_offset = $date1[2];
									$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
									$day += ((($day>0)?-1:0)+$date1[1])*7;
								} else {
									// -nth weekday (0-6) of month
									$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
									$weekday = dayOfWeek($last_day_of_month);
									$weekday_offset = $date1[2];
									$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
									if ($day > daysInMonth($month, $year)) $day -= 7;
									$day += (($date1[0]+1)*7);
									//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
								}

							}

							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
							//if (($current_date >= $start_date) && ($current_date <= $end_date)) {
							if (($current_date >= $start_date)) {
								$days[] = $current_date;
							}
							
							if (isset($date2) && is_array($date2)) {
								
								if ($date2[2] == 'day') {

									if ($date2[1] > 0) {
										$day = pad($date2[1]);				
									} else if ($date2[1] === '-1') {
										$day = day(timestamp($year, $month+1, 0));
									} else if ($date2[1] === '-2') {
										$day = day(timestamp($year, $month+1, -1));
									}

								} else if (is_numeric($date2[2])) {

									if ($date2[1] > 0) {
										// nth weekday (0-6 || sun-mon) of month
										$weekday = dayOfWeek(timestamp(false,$month,1));
										$weekday_offset = $date2[2];
										$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
										$day += ((($day>0)?-1:0)+$date2[1])*7;
									} else {
										// -nth weekday (0-6) of month
										$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
										$weekday = dayOfWeek($last_day_of_month);
										$weekday_offset = $date2[2];
										$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
										if ($day > daysInMonth($month, $year)) $day -= 7;
										$day += (($date2[0]+1)*7);
										//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
									}

								}

								$current_date2 = format_date(timestamp($year, $month, $day), 'Y-m-d');
								if ($inclusive) {
									//dump($current_date);
									//dump($current_date2);
									$rangeDates = dateRangeToArray($current_date, $current_date2, 'days');
									foreach ($rangeDates as $rangeDate) {
										//if (($rangeDate != $current_date) && ($rangeDate >= $start_date) && ($rangeDate <= $end_date)) {
										if (($rangeDate != $current_date) && ($rangeDate >= $start_date)) {
											$days[] = $rangeDate;
										}
									}
								} else {
									//if (($current_date2 >= $start_date) && ($current_date2 <= $end_date)) {
									if (($current_date2 >= $start_date)) {
										$days[] = $current_date2;
									}
								}
								
							}
							
							$year++;
							
						} while ($current_date <= substr(yearsFromNow(1, $end_date), 0, 10));
						
					}
					
					
					/*
					$weekday = dayOfWeek($start_date);
					//$weekday_offset = $repeat[2];	// old way... if only value was 0-6
					
					// week values -- can be a single digit of (0-6) but also +(0-6) for a second day or ,(0-6) for through
					$weekday_offset = substr($repeat[2], 0, 1);
					if ((strlen($repeat[2]) > 1) && substr($repeat[2], 1, 1) == "+") {
						$weekday_offset2 = substr($repeat[2], 2, 1) - $weekday_offset;
					} else if ((strlen($repeat[2]) > 1) && substr($repeat[2], 1, 1) == ',') {
						$numberOfDays = 1 + substr($repeat[2], 2, 1) - $weekday_offset;
					}
					
					$day = (((0+day($start_date))-$weekday)+$weekday_offset);
					if ($day < day($start_date)) $day += 7;
					$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
					*/
					
					// needs to be tested...
					/*if (count($repeat) == 4) {
						// number of years to skip | 'years' | month | day
						$year = ((($start_date > (year()."-".pad($repeat[2])."-".pad($repeat[3]))) ? 1 : 0) + year($start_date));
						$month = pad($repeat[2]);
						$day = pad($repeat[3]);
						$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
				
						while ($current_date <= $end_date) {
							for ($i=0; $i<$numberOfDays; $i++) {
								$current_day = substr(daysFromNow($i, $current_date), 0, 10);
								if ($current_day >= $start_date) {
									$days[] = $current_day;
								}
							}

							$year += $repeat[0];
							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
						}
					} else if (count($repeat) == 5) {
						// number of years to skip | 'years' | month | nth | weekday of month
					}*/
				}
			
				//return $this->_all_dates[$end_date] = $days;
				sort($days);
				$days = array_unique($days);
				$this->_dates = join(",", $days);
				$db->update('table=>events', "id=>" . $this->id, array('_dates' => $this->_dates));

				//dump($this->_dates);

			}
			
			return $days;
		} // allDates
		
		/*function artist($return = false) {
			if (is_plugin('artists') && is_numeric($this->name)) {
 				load_plugin('artists');
				$this->_artist = get_artist($this->name);
			} 
			
			if (is_array($this->_artist) || is_object($this->_artist)) {
				if ($return) {
					if (is_array($this->_artist) && isset($this->_artist[$return])) {
						return $this->_artist[$return];
					} else if (is_object($this->_artist) && method_exists($this->_artist, $return)) {
						return $this->_artist->$return();
					} else if (is_object($this->_artist) && isset($this->_artist->$return)) {
						return $this->_artist->$return;
					} else {
						return false;
					}
				} else {
					return $this->_artist;
				}
			}
			return false;
			//return $this->_artist;
		} // artist
		*/

		function asDates($endDate = false, $startDate = TODAY) {
			$arrayOfDates = array();
			foreach ($this->getDates($endDate, $startDate) as $day) {
				$tempEventDate = get_object_vars($this);
				$tempEventDate['_date'] = substr($day,0,10);
				$arrayOfDates[] = $tempEventDate;
			}
			//dump($endDate);
			//dump($startDate);
			return $arrayOfDates;
		} // asDates

		function attendees($date = 'total', $status = false) {
			global $db;
			
			if (!isset($this->_attendees[$date])) {
				
				$dateSQL = ($date != 'total') ? " AND (`attending_on` = '" . $date . "')" : '';
				$this->_attendees[$date] = $db->get('table=>users_events', "where=>(`table` = 'events') AND (`table_id` = " . $db->escape($this->id) . ")" . $dateSQL . " AND (`deleted_at` IS NULL)");
			}
			
			// now figure out if we want people that have a certain status...
			
			return $this->_attendees[$date];
		} // attendees
		
		function body() {
			return $this->description();
		} // body

		/*function comments($numberToReturn = 0, $force = false) {
			
			if (!is_array($this->_comments) || $force) {
				
				if (is_plugin('comments')) {
					load_plugin('comments');
					
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
		
		function comments($numberToReturn = 0, $force = false) {
			
			if (!$this->show_comments())
				return false;
			
			if (!is_array($this->_comments) || $force) {
				
				$this->_comments = get_comments($this->table, $this->id);
				
				if (!is_array($this->_comments)) {
					warning('Attempting to get event comments has failed for user (id: ' . $this->id . ', url: ' . $this->url . ').', __FILE__, __FUNCTION__, __LINE__);
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
			
			return true;
		} // delete
		
		function description() {
			if (!empty($this->description)) {
				return valid(nl2br($this->description));
			}
			return false;
		} // description
		
		function display_comments() {
			if ($this->show_comments())
				comments($this->table, $this->id);
		} // display_comments

		function end_date() {
			if (is_string($this->end_date)) {
				return $this->end_date;
			}
			return $this->start_date;
		} // end_date
		
		function getDates($end_date = false, $start_date = TODAY) {
			// should default to next 30 days?
			if ($end_date === false) $end_date = '30-days';
			if (substr($end_date, -5) == '-days') $end_date = substr($end_date, 0, -5);
			if (is_numeric($end_date) || $end_date === 0) {
				$end_date = substr(daysFromNow($end_date, $start_date), 0, 10);
			}
			$dates = $this->allDates($end_date);
			$range_dates = array();
			foreach ($dates as $key => $date) {
				$date = substr($date, 0, 10);
				if (($date >= $start_date) && ($date <= $end_date)) {
					$range_dates[$key] = $date;
				}
			}
			return $range_dates;
		} // getDates

		function hasMultipleDays() {
			return ($this->end_date() != $this->start_date);
		} // hasMultipleDays

		function hasPhoto() {
			if (is_array($this->_photo)) :
				return $this->_photo;
			endif;
			$photo = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '.gif');
			if ($photo['found']) :
				return $this->_photo = $photo;
			endif;
			$photo = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '.jpg');
			if ($photo['found']) :
				return $this->_photo = $photo;
			endif;
			$photo = get_presentation_file('uploads/' . $this->table . '/' . $this->id . '.png');
			if ($photo['found']) :
				return $this->_photo = $photo;
			endif;
			return false;
		} // hasPhoto	
		
		function hours() {
			if (!empty($this->hours)) {
				return valid(nl2br($this->hours));
			}
			return false;
		} // hours

		function isApproved() {
			return (is_string($this->published_at)) ? true : false;
		} // isApproved
		
		function isAttending($date = 'total', $user_id = false) {
			if (!is_logged_in()) return false;
			if (!is_numeric($user_id)) $user_id = user('id');
			
			$attendees = $this->attendees($date);
			$attendees = array_flatten($attendees, 'user_id');
			if (is_array($attendees) && in_array($user_id, $attendees)) {
				return true;
			}
			return false;
		} // isAttending

		function isOnDate($date = TODAY) {
			// if isn't recurring, then if it's on or between start and end dates, then true...
			if ((daysApart($date, $this->start_date) >= 0) && (daysApart($date, $this->end_date()) <= 0)) {
				return true;
			}
			// but what about recurring ones?
			return false;
		} // isOnDate

		function isRecurring() {
			return (is_string($this->repeats_every) && !empty($this->repeats_every)) ? true : false;
		} // isRecurring
		
		function link() {
			return $this->section_url() . $this->id . "/";
		} // link

		function location() {
			// once venues are installed, this should check to see if $this->location is numeric and go grab the venue info...
			if (is_numeric($this->location)) {
				$this->venue = $this->venue('name') . '<br/>' . nl2br(strip_tags($this->venue('address'))) . '<br/>' . format_phone($this->venue('phone'));
			} else {
				$this->venue = $this->location;
			}
			
			return valid($this->venue);
		} // location

		function link_to_name() {
			return valid($this->name);			
		} // link_to_name

		function name() {
			/*if (is_plugin('artists') && is_numeric($this->name)) {
 				return valid($this->artist('name'));
			}*/
			return valid($this->name);
		} // name
		
		function nextDate() {
			if (is_string($this->_next_date)) {
				return $this->_next_date;
			}			
			
			if (!$this->isRecurring()) {
				$days = dateRangeToArray($this->start_date, $this->end_date(), 'days');
				$this->_next_date = current($days);
				return $this->_next_date;
			} else {				
				// if event is recurring, this is a little trickier...
				// have to figure out based on original date and repeat formula...
				
				$start_date = $this->start_date;
				
				$end_date = false;
				
				if (is_string($this->_dates) && !empty($this->_dates)) {
					
					$days = explode(",", $this->_dates);
					sort($days);
					$days = array_unique($days);
					
					if (is_array($days) && (count($days) > 0)) {
						return $this->_next_date = current($days);
					}
				}
				
				$days = array();
				$current_date = $start_date;
				
				$numberOfDays = $this->numberOfDays();
				
				if (substr($this->repeats_every,0,1) == '|') $this->repeats_every = "1" . $this->repeats_every;
				$repeat = explode("|", $this->repeats_every);
				
				$year = year($start_date);
				$month = month($start_date);
				$day = day($start_date);
				
				if ($repeat[1] == 'weeks') {
					// numbers of weeks | 'weeks' | day of the week (sun(0)-sat(6)) (+ day of the week)
					$weekday = dayOfWeek($start_date);
					//$weekday_offset = $repeat[2];	// old way... if only value was 0-6
					
					// week values -- can be a single digit of (0-6) but also +(0-6) for a second day or ,(0-6) for through
					$weekday_offset = substr($repeat[2], 0, 1);
					if ((strlen($repeat[2]) > 1) && substr($repeat[2], 1, 1) == "+") {
						$weekday_offset2 = substr($repeat[2], 2, 1) - $weekday_offset;
					} else if ((strlen($repeat[2]) > 1) && substr($repeat[2], 1, 1) == ',') {
						$numberOfDays = 1 + substr($repeat[2], 2, 1) - $weekday_offset;
					}
					
					$day = (((0+day($start_date))-$weekday)+$weekday_offset);
					if ($day < day($start_date)) $day += 7;
					$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
									
					while ($current_date <= $end_date) {
						if (isset($weekday_offset2) && is_numeric($weekday_offset2)) {
							$current_day = substr($current_date, 0, 10);
							if ($current_day >= $start_date) {
								return $this->_next_date = $current_day;
							}
							$current_day2 = substr(daysFromNow($weekday_offset2, $current_date), 0, 10);
							if ($current_day2 >= $start_date) {
								return $this->_next_date = $current_day2;
							}
						} else {
							for ($i=0; $i<$numberOfDays; $i++) {
								$current_day = substr(daysFromNow($i, $current_date), 0, 10);
								if ($current_day >= $start_date) {
									return $this->_next_date = $current_day;
								}
							}
						}
						
						$day += ($repeat[0]*7);
						$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
					}
					
				} else if ($repeat[1] == 'months') {
					
					$dates = $repeat[2];
					
					if (strpos($dates, '+') !== false) {
						list($date1, $date2) = explode("+", $dates);
						$date2 = explode(":", $date2);
						$inclusive = false;
					} else if (strpos($dates, ',') !== false) {
						list($date1, $date2) = explode(",", $dates);						
						$date2 = explode(":", $date2);
						$inclusive = true;
					} else {
						$date1 = $dates;
					}
					
					$date1 = explode(":", $date1);
					
					if (count($date1) == 2) {
						
						$month = month();
						$year = year();
						
						do {
							
							if ($date1[1] == 'day') {
								
								if ($date1[0] > 0) {
									$day = pad($date1[0]);				
								} else if ($date1[0] === '-1') {
									$day = day(timestamp($year, $month+1, 0));
								} else if ($date1[0] === '-2') {
									$day = day(timestamp($year, $month+1, -1));
								}
								
							} else if (is_numeric($date1[1])) {
								
								if ($date1[0] > 0) {
									// nth weekday (0-6 || sun-mon) of month
									$weekday = dayOfWeek(timestamp(false,$month,1));
									$weekday_offset = $date1[1];
									$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
									$day += ((($day>0)?-1:0)+$date1[0])*7;
								} else {
									// -nth weekday (0-6) of month
									$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
									$weekday = dayOfWeek($last_day_of_month);
									$weekday_offset = $date1[1];
									$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
									if ($day > daysInMonth($month, $year)) $day -= 7;
									$day += (($date1[0]+1)*7);
									//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
								}

							}

							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
							if (($current_date >= $start_date) && ($current_date <= $end_date)) {
								return $this->_next_date = $current_date;
							}
							
							if (isset($date2)) {
								
								if ($date2[1] == 'day') {

									if ($date2[0] > 0) {
										$day = pad($date2[0]);				
									} else if ($date2[0] === '-1') {
										$day = day(timestamp($year, $month+1, 0));
									} else if ($date2[0] === '-2') {
										$day = day(timestamp($year, $month+1, -1));
									}

								} else if (is_numeric($date2[1])) {

									if ($date2[0] > 0) {
										// nth weekday (0-6 || sun-mon) of month
										$weekday = dayOfWeek(timestamp(false,$month,1));
										$weekday_offset = $date2[1];
										$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
										$day += ((($day>0)?-1:0)+$date2[0])*7;
									} else {
										// -nth weekday (0-6) of month
										$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
										$weekday = dayOfWeek($last_day_of_month);
										$weekday_offset = $date2[1];
										$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
										if ($day > daysInMonth($month, $year)) $day -= 7;
										$day += (($date2[0]+1)*7);
										//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
									}

								}

								$current_date2 = format_date(timestamp($year, $month, $day), 'Y-m-d');
								if ($inclusive) {
									//dump($current_date);
									//dump($current_date2);
									$rangeDates = dateRangeToArray($current_date, $current_date2, 'days');
									foreach ($rangeDates as $rangeDate) {
										if (($rangeDate != $current_date) && ($rangeDate >= $start_date) && ($rangeDate <= $end_date)) {
											return $this->_next_date = $rangeDate;
										}
									}
								} else {
									if (($current_date2 >= $start_date) && ($current_date2 <= $end_date)) {
										return $this->_next_date = $current_date2;
									}
								}
								
							}
							
							$month++;
							
						} while ($current_date <= $end_date);
						
					}
					
				} else if ($repeat[1] == 'years') {
					$yearsToSkip = $repeat[0];
					$dates = $repeat[2];
					
					if (strpos($dates, '+') !== false) {
						list($date1, $date2) = explode("+", $dates);
						$date2 = explode(":", $date2);
						$inclusive = false;
					} else if (strpos($dates, ',') !== false) {
						list($date1, $date2) = explode(",", $dates);
						$date2 = explode(":", $date2);
						$inclusive = true;
					} else {
						$date1 = $dates;
					}
					
					$date1 = explode(":", $date1);
					
					if (count($date1) == 3) {
						
						$month = pad($date1[0]);
						$year = year();
						
						do {
							
							if ($date1[2] == 'day') {
								
								if ($date1[1] > 0) {
									$day = pad($date1[1]);				
								} else if ($date1[1] === '-1') {
									$day = day(timestamp($year, $month+1, 0));
								} else if ($date1[1] === '-2') {
									$day = day(timestamp($year, $month+1, -1));
								}
								
							} else if (is_numeric($date1[2])) {
								
								if ($date1[1] > 0) {
									// nth weekday (0-6 || sun-mon) of month
									$weekday = dayOfWeek(timestamp(false,$month,1));
									$weekday_offset = $date1[2];
									$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
									$day += ((($day>0)?-1:0)+$date1[1])*7;
								} else {
									// -nth weekday (0-6) of month
									$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
									$weekday = dayOfWeek($last_day_of_month);
									$weekday_offset = $date1[2];
									$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
									if ($day > daysInMonth($month, $year)) $day -= 7;
									$day += (($date1[0]+1)*7);
									//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
								}

							}

							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
							if (($current_date >= $start_date) && ($current_date <= $end_date)) {
								return $this->_next_date = $current_date;
							}
							
							if (isset($date2) && is_array($date2)) {
								
								if ($date2[2] == 'day') {

									if ($date2[1] > 0) {
										$day = pad($date2[1]);				
									} else if ($date2[1] === '-1') {
										$day = day(timestamp($year, $month+1, 0));
									} else if ($date2[1] === '-2') {
										$day = day(timestamp($year, $month+1, -1));
									}

								} else if (is_numeric($date2[2])) {

									if ($date2[1] > 0) {
										// nth weekday (0-6 || sun-mon) of month
										$weekday = dayOfWeek(timestamp(false,$month,1));
										$weekday_offset = $date2[2];
										$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
										$day += ((($day>0)?-1:0)+$date2[1])*7;
									} else {
										// -nth weekday (0-6) of month
										$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
										$weekday = dayOfWeek($last_day_of_month);
										$weekday_offset = $date2[2];
										$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
										if ($day > daysInMonth($month, $year)) $day -= 7;
										$day += (($date2[0]+1)*7);
										//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
									}

								}

								$current_date2 = format_date(timestamp($year, $month, $day), 'Y-m-d');
								if ($inclusive) {
									//dump($current_date);
									//dump($current_date2);
									$rangeDates = dateRangeToArray($current_date, $current_date2, 'days');
									foreach ($rangeDates as $rangeDate) {
										if (($rangeDate != $current_date) && ($rangeDate >= $start_date) && ($rangeDate <= $end_date)) {
											return $this->_next_date = $rangeDate;
										}
									}
								} else {
									if (($current_date2 >= $start_date) && ($current_date2 <= $end_date)) {
										return $this->_next_date = $current_date2;
									}
								}
								
							}
							
							$year++;
						
						} while ($current_date <= $end_date);
						
					}

				}
			}
			
			//return $this->_next_date = current($days);
			return $this->_next_date = $this->start_date;
		} // nextDate
		
		/*function nextDate() {
			global $db;
			// figure out all dates for an event...
			
			if (is_string($this->_next_date)) {
				return $this->_next_date;
			}			
			
			if (!$this->isRecurring()) {
				$days = dateRangeToArray($this->start_date, $this->end_date(), 'days');
				$this->_next_date = current($days);
				return $this->_next_date;
			} else {				
				// if event is recurring, this is a little trickier...
				// have to figure out based on original date and repeat formula...
				
				$start_date = $this->start_date;
				
				$end_date = false;
				if (!$end_date) {
					$end_date = ($this->ends_on && ($this->ends_on <= daysFromNow(90))) ? $this->ends_on : substr(daysFromNow(90), 0, 10);
				} else if ($this->ends_on && ($this->ends_on < $end_date)) {
					$end_date = $this->ends_on;
				}
				
				dump("nextDate -- #" . $this->id . " -- " . $this->repeats_every . " -- " . $start_date . " -- " . $end_date . " -- " . $this->_dates);
				
				if (is_string($this->_dates) && !empty($this->_dates)) {
					
					$days = trim_explode(",", $this->_dates);
					sort($days);
					$days = array_unique($days);
					
					foreach ($days as $key => $day)
						if ($day < TODAY)
							unset($days[$key]);
					
					if (is_array($days) && (count($days) > 0)) {
						reset($days);
						return $this->_next_date = current($days);
					}
				}
				
				$days = array();
				$current_date = $start_date;
				$start_date = TODAY;

				$numberOfDays = $this->numberOfDays();
				
				if (substr($this->repeats_every,0,1) == '|') $this->repeats_every = "1" . $this->repeats_every;
				$repeat = trim_explode("|", $this->repeats_every);
				
				$year = year($start_date);
				$month = month($start_date);
				$day = day($start_date);
				
				if ($repeat[1] == 'weeks') {
					// numbers of weeks | 'weeks' | day of the week (sun(0)-sat(6)) (+ day of the week)
					$weekday = dayOfWeek($start_date);
					//$weekday_offset = $repeat[2];	// old way... if only value was 0-6
					
					// week values -- can be a single digit of (0-6) but also +(0-6) for a second day or ,(0-6) for through
					$weekday_offset = substr($repeat[2], 0, 1);
					if ((strlen($repeat[2]) > 1) && substr($repeat[2], 1, 1) == "+") {
						$weekday_offset2 = substr($repeat[2], 2, 1) - $weekday_offset;
					} else if ((strlen($repeat[2]) > 1) && substr($repeat[2], 1, 1) == ',') {
						$numberOfDays = 1 + substr($repeat[2], 2, 1) - $weekday_offset;
					}
					
					$day = (((0+day($start_date))-$weekday)+$weekday_offset);
					if ($day < day($start_date)) $day += 7;
					$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
									
					while ($current_date <= $end_date) {
						if (isset($weekday_offset2) && is_numeric($weekday_offset2)) {
							$current_day = substr($current_date, 0, 10);
							if ($current_day >= $start_date) {
								$days[] = $current_day;
							}
							$current_day2 = substr(daysFromNow($weekday_offset2, $current_date), 0, 10);
							if ($current_day2 >= $start_date) {
								$days[] = $current_day2;
							}
						} else {
							for ($i=0; $i<$numberOfDays; $i++) {
								$current_day = substr(daysFromNow($i, $current_date), 0, 10);
								if ($current_day >= $start_date) {
									$days[] = $current_day;
								}
							}
						}
						
						$day += ($repeat[0]*7);
						$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
						
						if (count($days) > 0) {
							sort($days);
							$this->_dates = join(",", $days);
							$db->update('table=>events', "id=>" . $this->id, array('_dates' => $this->_dates));
							return $this->_next_date = current($days);
						}
					}
					
				} else if ($repeat[1] == 'months') {
					
					$dates = $repeat[2];
					
					if (strpos($dates, '+') !== false) {
						list($date1, $date2) = trim_explode("+", $dates);
						$date2 = trim_explode(":", $date2);
						$inclusive = false;
					} else if (strpos($dates, ',') !== false) {
						list($date1, $date2) = trim_explode(",", $dates);						
						$date2 = trim_explode(":", $date2);
						$inclusive = true;
					} else {
						$date1 = $dates;
					}
					
					$date1 = trim_explode(":", $date1);
					
					if (count($date1) == 2) {
						
						$month = month();
						$year = year();
						
						do {
							
							if ($date1[1] == 'day') {
								
								if ($date1[0] > 0) {
									$day = pad($date1[0]);				
								} else if ($date1[0] === '-1') {
									$day = day(timestamp($year, $month+1, 0));
								} else if ($date1[0] === '-2') {
									$day = day(timestamp($year, $month+1, -1));
								}
								
							} else if (is_numeric($date1[1])) {
								
								if ($date1[0] > 0) {
									// nth weekday (0-6 || sun-mon) of month
									$weekday = dayOfWeek(timestamp(false,$month,1));
									$weekday_offset = $date1[1];
									$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
									$day += ((($day>0)?-1:0)+$date1[0])*7;
								} else {
									// -nth weekday (0-6) of month
									$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
									$weekday = dayOfWeek($last_day_of_month);
									$weekday_offset = $date1[1];
									$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
									if ($day > daysInMonth($month, $year)) $day -= 7;
									$day += (($date1[0]+1)*7);
									//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
								}

							}

							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
							if (($current_date >= $start_date) && ($current_date <= $end_date)) {
								$days[] = $current_date;
							}
							
							if (isset($date2)) {
								
								if ($date2[1] == 'day') {

									if ($date2[0] > 0) {
										$day = pad($date2[0]);				
									} else if ($date2[0] === '-1') {
										$day = day(timestamp($year, $month+1, 0));
									} else if ($date2[0] === '-2') {
										$day = day(timestamp($year, $month+1, -1));
									}

								} else if (is_numeric($date2[1])) {

									if ($date2[0] > 0) {
										// nth weekday (0-6 || sun-mon) of month
										$weekday = dayOfWeek(timestamp(false,$month,1));
										$weekday_offset = $date2[1];
										$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
										$day += ((($day>0)?-1:0)+$date2[0])*7;
									} else {
										// -nth weekday (0-6) of month
										$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
										$weekday = dayOfWeek($last_day_of_month);
										$weekday_offset = $date2[1];
										$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
										if ($day > daysInMonth($month, $year)) $day -= 7;
										$day += (($date2[0]+1)*7);
										//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
									}

								}

								$current_date2 = format_date(timestamp($year, $month, $day), 'Y-m-d');
								if ($inclusive) {
									//dump($current_date);
									//dump($current_date2);
									$rangeDates = dateRangeToArray($current_date, $current_date2, 'days');
									foreach ($rangeDates as $rangeDate) {
										if (($rangeDate != $current_date) && ($rangeDate >= $start_date) && ($rangeDate <= $end_date)) {
											$days[] = $rangeDate;
										}
									}
								} else {
									if (($current_date2 >= $start_date) && ($current_date2 <= $end_date)) {
										$days[] = $current_date2;
									}
								}
								
							}
							
							$month++;
							
							if (count($days) > 0) {
								sort($days);
								$this->_dates = join(",", $days);
								$db->update('table=>events', "id=>" . $this->id, array('_dates' => $this->_dates));
								return $this->_next_date = current($days);
							}
							
						} while ($current_date <= $end_date);
						
					}
					
				} else if ($repeat[1] == 'years') {
					$yearsToSkip = $repeat[0];
					$dates = $repeat[2];
					
					if (strpos($dates, '+') !== false) {
						list($date1, $date2) = trim_explode("+", $dates);
						$date2 = trim_explode(":", $date2);
						$inclusive = false;
					} else if (strpos($dates, ',') !== false) {
						list($date1, $date2) = trim_explode(",", $dates);
						$date2 = trim_explode(":", $date2);
						$inclusive = true;
					} else {
						$date1 = $dates;
					}
					
					$date1 = trim_explode(":", $date1);
					
					if (count($date1) == 3) {
						
						$month = pad($date1[0]);
						$year = year();
						
						do {
							
							if ($date1[2] == 'day') {
								
								if ($date1[1] > 0) {
									$day = pad($date1[1]);				
								} else if ($date1[1] === '-1') {
									$day = day(timestamp($year, $month+1, 0));
								} else if ($date1[1] === '-2') {
									$day = day(timestamp($year, $month+1, -1));
								}
								
							} else if (is_numeric($date1[2])) {
								
								if ($date1[1] > 0) {
									// nth weekday (0-6 || sun-mon) of month
									$weekday = dayOfWeek(timestamp(false,$month,1));
									$weekday_offset = $date1[2];
									$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
									$day += ((($day>0)?-1:0)+$date1[1])*7;
								} else {
									// -nth weekday (0-6) of month
									$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
									$weekday = dayOfWeek($last_day_of_month);
									$weekday_offset = $date1[2];
									$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
									if ($day > daysInMonth($month, $year)) $day -= 7;
									$day += (($date1[0]+1)*7);
									//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
								}

							}

							$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
							if (($current_date >= $start_date) && ($current_date <= $end_date)) {
								$days[] = $current_date;
							}
							
							if (isset($date2) && is_array($date2)) {
								
								if ($date2[2] == 'day') {

									if ($date2[1] > 0) {
										$day = pad($date2[1]);				
									} else if ($date2[1] === '-1') {
										$day = day(timestamp($year, $month+1, 0));
									} else if ($date2[1] === '-2') {
										$day = day(timestamp($year, $month+1, -1));
									}

								} else if (is_numeric($date2[2])) {

									if ($date2[1] > 0) {
										// nth weekday (0-6 || sun-mon) of month
										$weekday = dayOfWeek(timestamp(false,$month,1));
										$weekday_offset = $date2[2];
										$day = (((0+day(timestamp(false,$month,1)))-$weekday)+$weekday_offset);
										$day += ((($day>0)?-1:0)+$date2[1])*7;
									} else {
										// -nth weekday (0-6) of month
										$last_day_of_month = format_date(timestamp($year,$month+1,0), 'Y-m-d');
										$weekday = dayOfWeek($last_day_of_month);
										$weekday_offset = $date2[2];
										$day = (((0+day($last_day_of_month))-$weekday)+$weekday_offset);
										if ($day > daysInMonth($month, $year)) $day -= 7;
										$day += (($date2[0]+1)*7);
										//$current_date = format_date(timestamp($year, $month, $day), 'Y-m-d');
									}

								}

								$current_date2 = format_date(timestamp($year, $month, $day), 'Y-m-d');
								if ($inclusive) {
									//dump($current_date);
									//dump($current_date2);
									$rangeDates = dateRangeToArray($current_date, $current_date2, 'days');
									foreach ($rangeDates as $rangeDate) {
										if (($rangeDate != $current_date) && ($rangeDate >= $start_date) && ($rangeDate <= $end_date)) {
											$days[] = $rangeDate;
										}
									}
								} else {
									if (($current_date2 >= $start_date) && ($current_date2 <= $end_date)) {
										$days[] = $current_date2;
									}
								}
								
							}
							
							$year++;
							
							if (count($days) > 0) {
								sort($days);
								$this->_dates = join(",", $days);
								$db->update('table=>events', "id=>" . $this->id, array('_dates' => $this->_dates));
								return $this->_next_date = current($days);
							}
						
						} while ($current_date <= $end_date);
						
					}

				}
			}
			
			//return $this->_all_dates[$end_date] = $days;
			return $this->_next_date = current($days);

			//return $days;
		} // nextDate
		*/

		function numberOfDays() {
			return 1 + daysApart($this->end_date(), $this->start_date);
		} // numberOfDays

		function photo($width = 150, $height = false, $cropratio = false) {
			//$info = '?width=' . $width . '&amp;height=' . $height . '&amp;cropratio=' . $cropratio;
			if ($this->hasPhoto()) :
				return add_photo_info($this->_photo['versioned'], $width, $height, $cropratio);
			endif;
			return add_photo_info('plugins/' . $this->table . '/images/0.gif', $width, $height, $cropratio);
			// return LOCATION . 'images/uploads/' . $this->table . '-0.gif' . $info;
		} // photo

		function photopath() {
			if ($this->hasPhoto()) :
				return $this->_photo['path'] . $this->_photo['name'] . "." . $this->_photo['ext'];
			endif;
			return false;
		} // photopath
		
		function repeat($which = false) {
			if (is_string($this->repeats_every)) {
				$repeats = explode("|", $this->repeats_every);
				if (is_numeric($which) && isset($repeats[$which]))
					return $repeats[$which];
				return $repeats;
			}
			return false;
		} // repeat

		function schedule() {
			// this translates $this->repeats_every into words that make sense...
			$repeats = explode('|', $this->repeats_every);
			
			$text = '';
			if (count($repeats) > 1) {
				if ($repeats[1] == 'weeks') {
					$text = "Every ";
					if ($repeats[0] == '2') {
						$text .= 'other ';
					} else if ($repeats[0] > 2) {
						$text .= daySuffix($repeats[0]) . ' ';
					}
					
					$weekday1 = substr($repeats[2], 0, 1);
					$text .= friendlyWeekday($weekday1);
					
					if ((strlen($repeats[2]) > 1) && substr($repeats[2], 1, 1) == "+") {
						$weekday2 = substr($repeats[2], 2, 1);
						$text .= " and " . friendlyWeekday($weekday2);
					} else if ((strlen($repeats[2]) > 1) && substr($repeats[2], 1, 1) == ',') {
						$weekday2 = substr($repeats[2], 2, 1);
						$text .= " thru " . friendlyWeekday($weekday2);
					}
				} else if ($repeats[1] == 'months') {
					$text = '';
					if ($repeats[0] === '1') {
						$text .= "Every ";
					} else if ($repeats[0] == '2') {
						$text .= "Every Other ";
					} else {
						$text .= "Every " . $repeats[0] . " ";
					}
					
					$dates = $repeats[2];
					if (strpos($dates, '+') !== false) {
						list($date1, $date2) = explode("+", $dates);
						$date1 = explode(":", $date1);
						$date2 = explode(":", $date2);
						$inclusive = false;
					} else if (strpos($dates, ',') !== false) {
						list($date1, $date2) = explode(",", $dates);						
						$date1 = explode(":", $date1);
						$date2 = explode(":", $date2);
						$inclusive = true;
					} else {
						$date1 = explode(":", $dates);
					}
					
					if ($date1[1] == 'day') {
						if ($date1[0] > 0) {
							$occurance1 = $date1[0] . daySuffix($date1[0]);
						} else if ($date1[0] === '-1') {
							$occurance1 = 'Last Day'; 
						} else if ($date1[0] === '-2') {
							$occurance1 = 'Second to Last Day';
						}						
					} else if (is_numeric($date1[1])) {
						if ($date1[0] > 0) {
							$occurance1 = $date1[0] . daySuffix($date1[0]) . " " . friendlyWeekday($date1[1]);
						} else if ($date1[0] === '-1') {
							$occurance1 = 'Last ' . friendlyWeekday($date1[1]); 
						} else if ($date1[0] === '-2') {
							$occurance1 = 'Second to Last ' . friendlyWeekday($date1[1]);
						}
					}
					$occurance2 = '';
					if (isset($date2) && is_array($date2)) {
						$occurance2 = ($inclusive) ? ' thru ' : ' and ';
						if ($date2[1] == 'day') {
							if ($date2[0] > 0) {
								$occurance2 .= $date2[0] . daySuffix($date2[0]);
							} else if ($date2[0] === '-1') {
								$occurance2 .= 'the Last Day'; 
							} else if ($date2[0] === '-2') {
								$occurance2 .= 'the Second to Last Day';
							}						
						} else if (is_numeric($date2[1])) {
							if ($date2[0] > 0) {
								$occurance2 .= $date2[0] . daySuffix($date2[0]) . " " . friendlyWeekday($date2[1]);
							} else if ($date2[0] === '-1') {
								$occurance2 .= 'the Last ' . friendlyWeekday($date2[1]); 
							} else if ($date2[0] === '-2') {
								$occurance2 .= 'the Second to Last ' . friendlyWeekday($date2[1]);
							}
						}
					}
					$text .= $occurance1 . $occurance2 . ' of the month';

					/*$text = '';
					if ($repeats[2] === '-1') {
						$text .= 'Last';
					} else if ($repeats[2] === '-2') {
						$text .= 'Second to Last';
					} else if (($repeats[3] == 'day') || ($repeats[2] > 4)) {
						$text .= $repeats[2] . daySuffix($repeats[2]);
					} else if (($repeats[3] != 'day') && ($repeats[2] === '1')) {
						$text .= 'First';
					} else if (($repeats[3] != 'day') && ($repeats[2] === '2')) {
						$text .= 'Second';
					} else if (($repeats[3] != 'day') && ($repeats[2] === '3')) {
						$text .= 'Third';
					} else if (($repeats[3] != 'day') && ($repeats[2] === '4')) {
						$text .= 'Fourth';
					}

					if ($repeats[3] != 'day') {
						$text .= ' ' . friendlyWeekday($repeats[3]);
					} else if (($repeats[3] == 'day') && ($repeats[2] < 0)) {
						$text .= ' Day';
					} 

					$text .= ' of Every Month';*/
				} else if ($repeats[1] == 'years') {
					//$text = 'Yearly, Every ' . friendlyMonth($repeats[2]) . ' ' . $repeats[3] . daySuffix($repeats[3]);
					$text = '';
					if ($repeats[0] === '1') {
						$text .= "Every ";
					} else if ($repeats[0] == '2') {
						$text .= "Every Other ";
					} else {
						$text .= "Every " . $repeats[0] . " Years on the ";
					}
					
					$dates = $repeats[2];
					if (strpos($dates, '+') !== false) {
						list($date1, $date2) = explode("+", $dates);
						$date1 = explode(":", $date1);
						$date2 = explode(":", $date2);
						$inclusive = false;
					} else if (strpos($dates, ',') !== false) {
						list($date1, $date2) = explode(",", $dates);						
						$date1 = explode(":", $date1);
						$date2 = explode(":", $date2);
						$inclusive = true;
					} else {
						$date1 = explode(":", $dates);
					}
					
					if ($date1[2] == 'day') {
						if ($date1[1] > 0) {
							$occurance1 = $date1[1] . daySuffix($date1[1]);
						} else if ($date1[1] === '-1') {
							$occurance1 = 'Last Day'; 
						} else if ($date1[1] === '-2') {
							$occurance1 = 'Second to Last Day';
						}
					} else if (is_numeric($date1[2])) {
						if ($date1[1] > 0) {
							$occurance1 = $date1[1] . daySuffix($date1[1]) . " " . friendlyWeekday($date1[2]);
						} else if ($date1[1] === '-1') {
							$occurance1 = 'Last ' . friendlyWeekday($date1[2]); 
						} else if ($date1[1] === '-2') {
							$occurance1 = 'Second to Last ' . friendlyWeekday($date1[2]);
						}
					}
					
					$occurance2 = '';
					if (isset($date2) && is_array($date2)) {
						$occurance2 = ($inclusive) ? ' thru ' : ' and '; 
						if ($date2[2] == 'day') {
							if ($date2[1] > 0) {
								$occurance2 .= $date2[1] . daySuffix($date2[1]);
							} else if ($date2[1] === '-1') {
								$occurance2 .= 'the Last Day'; 
							} else if ($date2[1] === '-2') {
								$occurance2 .= 'the Second to Last Day';
							}
						} else if (is_numeric($date2[2])) {
							if ($date2[1] > 0) {
								$occurance2 .= $date2[1] . daySuffix($date2[1]) . " " . friendlyWeekday($date2[2]);
							} else if ($date2[1] === '-1') {
								$occurance2 .= 'the Last ' . friendlyWeekday($date2[2]); 
							} else if ($date2[1] === '-2') {
								$occurance2 .= 'the Second to Last ' . friendlyWeekday($date2[2]);
							}
						}
					}
					
					$text .= $occurance1 . $occurance2 . ' of ' . friendlyMonth($date1[0]);
				}
			}
			
			if ($this->hasMultipleDays()) {
				$text .=  " (" . $this->numberOfDays() . " days)";
			}
			
			return $text;
		} // schedule
		
		function section() {
			$sections = get_sitemap_sections_by_content('events');
			if (isset($sections[0]['id']))
				return $sections[0]['id'];
			return false;
		} // section
		
		function section_url() {
			return get_sitemap_section_url($this->sitemap_id);
		} // section_url
		
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
		
		function store() {
			//storage("$this->table[$this->id]", get_object_vars($this));
		} // store
		
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
			if ($this->hasPhoto()) :
				return $this->_photo['versioned'];
			endif;
			return upload_folder($this->table) . '0.gif';
		} // get_thumb
		
		/*
		function thumb() {
			return $this->photo(50, 50);
		} // thumb
		*/
		
		function time() {
			return $this->hours;
		} // time
				
		function title() {
			return $this->name();
		} // title
		
		function type($return = false) {
			global $db;
			
			if (!is_array($this->_type)) {
				$this->_type = $db->getOne("table=>event_categories", "where=>(`id` = " . $db->escape($this->event_category_id) . ")");
			}
			
			if ($return) {
				if (isset($this->_type[$return])) {
					return $this->_type[$return];
				}
				return false;
			}
			
			return $this->_type;
		} // type
		
		function venue($return = false) {
			
			if (!is_numeric($this->location))
				return valid($this->location);
			
			if (!is_array($this->_venue)) {
				$this->_venue = get_event_venue($this->location);
			}
			
			if ($return) {
				if (is_object($this->_venue)) {
					if (method_exists($this->_venue, $return)) {
						return $this->_venue->$return();
					} else {
						if (isset($this->_venue->$return)) return $this->_venue->$return;
					}
				} else if (is_array($this->_venue)) {
					if (isset($this->_venue[$return])) return $this->_venue[$return];
				}
				return false;
			}
			return $this->_venue;
		} // venue
		
		function wasFound() {
			return $this->_exists;
		} // wasFound
		
		function when() {
			// $event->start_date; to $event->end_date(); (<?= $event->numberOfDays(); days)<br />
			// $event->start_time; to $event->end_time;
			if ($this->isRecurring()) {
				return $this->schedule();
			} else if ($this->hasMultipleDays()) {
				return format_date($this->start_date, 'M jS, Y') . ' to ' . format_date($this->end_date(), 'M jS, Y') . " (" . $this->numberOfDays() . " days)";
			} else {
				return format_date($this->start_date, 'l, F jS, Y');
			}
			
		} // when
		
	} // event class

?>