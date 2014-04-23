<?php

/*
	Time/Date Related Constants/Variables
*/
	define("DEFAULT_TIMEZONE",'America/New_York');
	ini_set('date.timezone', DEFAULT_TIMEZONE);
	
	define('DATE_FORMAT', "Y-m-d");
	define('TIME_FORMAT', "H:i:s");
	define('TIMESTAMP_FORMAT', DATE_FORMAT . " " . TIME_FORMAT);
	define('TODAY', date(DATE_FORMAT));
	define('TIME', date(TIME_FORMAT));
	define('NOW', date(TIMESTAMP_FORMAT));
	define('ISO8601_FORMAT', "Y-m-d\TH:i:s+00:00");

	$tz = (int)(trim(date('O'), '0+'));
	define('TZ', $tz);

	$tz_offset = -1*$tz;
	define('TZ_OFFSET', $tz_offset);
	
	define('GM_TODAY', gmdate(DATE_FORMAT));
	define('GM_TIME', gmdate(TIME_FORMAT));
	define('GM_NOW', gmdate(TIMESTAMP_FORMAT));
	define('GM_ISO8601', gmdate(ISO8601_FORMAT));

?>