<?php

	function ago($timestamp){
		$difference = time() - strtotime($timestamp);
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
		for($j = 0; $difference >= $lengths[$j]; $j++)
			$difference /= $lengths[$j];
		$difference = round($difference);
		if ($difference != 1)
			$periods[$j].= "s";
		$text = "$difference $periods[$j] ago";
		return $text;
	} // ago

	function dateRangeToArray($startDate = TODAY, $endDate = TODAY, $unit = 'months') {
		$range = array();
		$isReversed = false;
		$fullTime = (strpos($startDate, ":") !== false) ? true : false;
		
		if ($startDate > $endDate) {
			$isReversed = true;
			$start = $startDate;
			$startDate = $endDate;
			$endDate = $start;
		}
		
		while ($startDate <= $endDate) {
			$range[] = $startDate;
			if ($unit == 'years') {
				$startDate = format_date(yearsFromNow(1, $startDate), (($fullTime) ? "Y-m-d H:i:s" : "Y-m-d"));
			} else if ($unit == 'months') {
				$startDate = format_date(monthsFromNow(1, $startDate), (($fullTime) ? "Y-m-d H:i:s" : "Y-m-d"));
			} else if ($unit == 'days') {
				$startDate = format_date(daysFromNow(1, $startDate), (($fullTime) ? "Y-m-d H:i:s" : "Y-m-d"));
			}
		}
		if ($isReversed) $range = array_reverse($range);
		return $range;
	} // dateRangeToArray

	function day($ts = NOW) {
		return format_date($ts, "j");
	} // day
	
	function dayAfter($ts = NOW) {
		return daysFromNow(1, $ts);
	} // dayAfter
	
	function dayBefore($ts = NOW) {
		return daysFromNow(-1, $ts);
	} // dayBefore
	
	function daysApart($date, $date2) {
		$hours = ((strtotime($date)-strtotime($date2))/(60*60));
		$days = round($hours/24,0);
		return $days;
	} // daysApart
	
	function daysAway($date) {
		return daysApart($date, TODAY);
	} // daysAway
	
	function daysFromNow($days, $ts = NOW) {
		return format_date(timestamp(year($ts), month($ts), (day($ts)+$days), (hour($ts)), (minute($ts)), (second($ts))), 'Y-m-d H:i:s');
	} // daysFromNow

	function dayOfWeek($ts = false) {
		$ts = ($ts) ? $ts : NOW;
		return format_date($ts, 'w');
	} // dayOfWeek
		
	function daysInMonth($m = false, $y = false) {
		$m = ($m) ? $m : format_date(NOW, "n");
		$y = ($y) ? $y : format_date(NOW, "Y");
		$ts = (strlen($m) > 2) ? $m : timestamp($y, $m, 1);
		return format_date($ts, "t");
	} // daysInMonth
	
	function daySuffix($dayOfMonth) {
		return format_date("2000-01-" . pad($dayOfMonth), 'S');
	} // daySuffix
	
	function format_date($date, $format, $notime = false) {
		//if ((substr($date, -3) == ':01') && $notime) 
		//	$format = $notime;
		//$new_date = ((strlen($date) < 1) ? '' : ((substr($date, 4, 1) == "-") ? date($format, strtotime($date)) : date($format, $date)));
		if (strlen($date) < 1) {
			$new_date = '';
		//} else if (substr($date, 4, 1) == "-") {
		} else if (is_string($date) && ($format == 'relative')) {
			$new_date = ago($date);
		} else if (is_string($date)) {
			$new_date = date($format, strtotime($date));
		} else {
		//	$new_date = '';
		//} else if (is_numeric($date)) {
			$new_date = date($format, $date);
		}
		return $new_date;
	} // /format_date
	
	function friendlyWeekday($weekday = 0) {
		global $WEEKDAYS;
		foreach ($WEEKDAYS as $weekdayNum => $weekdayName)
			if ($weekdayNum == $weekday) 
				return $weekdayName;
		return false;		
	} // friendlyWeekday

	function friendlyMonth($month) {
		global $MONTHS;
		foreach ($MONTHS as $monthNum => $monthName)
			if ($monthNum == $month) 
				return $monthName;
		return false;		
	} // friendlyWeekday
	
	function hour($ts = NOW) {
		return format_date($ts, "G");
	} // hour

	function hoursFromNow($hours, $ts = NOW) {
		return format_date(timestamp(year($ts), (month($ts)), (day($ts)), (hour($ts)+$hours), (minute($ts)), (second($ts))), 'Y-m-d H:i:s');
	} // hoursFromNow
	
	function isToday($date = TODAY) {
		if (daysAway($date) === 0) {
			return true;
		}
		return false;
	} // isToday
	
	function minute($ts = NOW) {
		return format_date($ts, "i");
	} // minute
	
	function minutesFromNow($minutes, $ts = NOW) {
		return format_date(timestamp(year($ts), (month($ts)), (day($ts)), (hour($ts)), ($minutes + minute($ts)), (second($ts))), 'Y-m-d H:i:s');
	} // minutesFromNow

	function month($ts = NOW) {
		return format_date($ts, "n");
	} // month
	
	function months() {
		global $MONTHS;
		return $MONTHS;
	} // months
	
	function monthsFromNow($months, $ts = NOW) {
		return format_date(timestamp(year($ts), (month($ts)+$months), (day($ts)), (hour($ts)), (minute($ts)), (second($ts))), 'Y-m-d H:i:s');
	} // monthsFromNow
	
	function pad($day) {
		return (strlen($day) == 1) ? "0" . $day : $day;
	} // pad
	
	function second($ts = NOW) {
		return format_date($ts, "s");
	} // seconds

	function timestamp($y = false, $m = false, $d = false, $h = false, $min = false, $s = false) {
		/*global $log;
		$l = 'pre';
		$l .= 'h: ' . var_export($h, true);
		$l .= 'min: ' . var_export($min, true);
		$l .= 's: ' . var_export($s, true);
		$l .= 'm: ' . var_export($m, true);
		$l .= 'd: ' . var_export($d, true);
		$l .= 'y: ' . var_export($y, true);*/
		if ($y===false) $y = year(NOW);
		if ($m===false) $m = month(NOW);
		if ($d===false) $d = day(NOW);
		if ($h===false) $h = hour(NOW);
		if ($min===false) $min = minute(NOW);
		if ($s===false) $s = second(NOW);
		/*$l .= 'post';
		$l .= 'h: ' . var_export($h, true);
		$l .= 'min: ' . var_export($min, true);
		$l .= 's: ' . var_export($s, true);
		$l .= 'm: ' . var_export($m, true);
		$l .= 'd: ' . var_export($d, true);
		$l .= 'y: ' . var_export($y, true);
		$log->log($l);*/
		return mktime((int)$h, (int)$min, (int)$s, (int)$m, (int)$d, (int)$y);
	} // timestamp
	
	function timestampFromFilter($filterLength = '30-days') {
		$negative = 1;
		if (strpos($filterLength,"-") === false)
			$filterLength = '30-days';
		if (substr($filterLength, 0, 1) === '-') {
			$filterLength = substr($filterLength, 1);
			$negative = -1;
		}
		$filter = explode("-",$filterLength);
		if (!is_numeric($filter[1])) {
			$year = 0 + year() + ($negative * ((substr($filter[1],0,4) == 'year') ? $filter[0] : 0));
			$month = 0 + month() + ($negative * ((substr($filter[1],0,5) == 'month') ? $filter[0] : 0));
			$day = 0 + day() + ($negative * ((substr($filter[1],0,4) == 'week') ? ($filter[0]*7) : ((substr($filter[1],0,3) == 'day') ? $filter[0] : 0)));
		} else {
			$year = 0 + $filter[0];
			$month = 0 + $filter[1];
			$day = daysInMonth($month, $year);
		}
		return timestamp($year, $month, $day);
	} // timestampeFromFilter
	
	function weekdays() {
		global $WEEKDAYS;
		return $WEEKDAYS;
	} // weekdays

	function year($ts = NOW) {
		return format_date($ts, "Y");
	} // year
	
	function yearsFromNow($years, $ts = NOW) {
		return format_date(timestamp((year($ts)+$years), (month($ts)), (day($ts)), (hour($ts)), (minute($ts)), (second($ts))), 'Y-m-d G:i:s');
	} // yearsFromNow

?>