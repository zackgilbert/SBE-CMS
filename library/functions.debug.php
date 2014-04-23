<?php

	function timer_start() {
		global $debug_timer;
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		if (function_exists('bcadd')) {
			$debug_timer['start'] = bcadd($mtime[1], $mtime[0], 10); //$mtime[1] + $mtime[0];	
		} else {
			$debug_timer['start'] = $mtime[1] + $mtime[0];
		}
		//register_shutdown_function('timer_stop');
		return true;
	} // timer_start

	function timer_stop($print = true) {
		global $debug_timer;
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		if (function_exists('bcadd')) {
			$debug_timer['end'] = bcadd($mtime[1], $mtime[0], 10);
		} else {
			$debug_timer['end'] = $mtime[1] + $mtime[0];
		}
		$debug_timer['total'] = $debug_timer['end'] - $debug_timer['start'];
		if ($print) 
			echo "<p>Page Loaded In: " . $debug_timer['total'] . "</p>";
		return $debug_timer['total'];
	} // timer_stop

?>