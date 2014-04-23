<?php

	global $memcache;
	$memcache = false;
	$memcacheIsConnected = false;
	$memcacheAttempted = false;
	$memcacheHost = 'localhost';
	$memcachePort = 11211;
	$memcacheFlag = (defined('MEMCACHE_COMPRESSED')) ? MEMCACHE_COMPRESSED : 0;
	$memcacheExpires = 3600;
	
	if (!defined('USE_MEMCACHE'))
		define('USE_MEMCACHE', false);

	function memcache($command = 'flush') {
		global $memcache, $memcacheIsConnected, $memcacheAttempted, $memcacheHost, $memcachePort;
		if (PRODUCTION && defined('USE_MEMCACHE') && USE_MEMCACHE && class_exists('Memcache') && !$memcache && !$memcacheAttempted) {
			$memcache = new Memcache;
			$memcacheIsConnected = $memcache->connect($memcacheHost, $memcachePort);
			$memcacheAttempted = true;
			
			if (!$memcacheIsConnected) {
				//dump("CAN'T CONNECT TO MEMCACHE");
				//sendmail('zack@areyouseen.com', "Rochester City Newspaper <noreply@" . SERVER . ">", 'Memcache not connecting.', "Memcache couldn't connect.\n\nHost: " . $memcacheHost . "\nPort: " . $memcachePort . "\nTime: " . NOW . "\n\nCheck server to make sure it's running.");
			}
		}
		if ($memcacheIsConnected && is_object($memcache) && method_exists($memcache, $command)) {
			return $memcache;
		}
		//dump('no memcache');
		return false;
	} // memcache
	
	function flush_memcache() {
		if ($memcache = memcache('flush')) {
			return $memcache->flush();
		}
		return false;
	} // memcache_flush
	
	function get_memcache($key, $flag = false) {
		global $memcacheFlag;
		if (!$flag) $flag = $memcacheFlag;
		if ($memcache = memcache('get')) {
			return $memcache->get(md5($key), $flag);
		}
		return false;		
	} // memcache_get

	function set_memcache($key, $var, $flag = false, $expires = false) {
		global $memcacheFlag, $memcacheExpires;
		if (!$flag) $flag = $memcacheFlag;
		if (!$expires) $expires = $memcacheExpires;
		if ($memcache = memcache('set')) {
			return $memcache->set(md5($key), $var, $flag, $expires);
		}
		return false;
	} // memcache_set	

?>