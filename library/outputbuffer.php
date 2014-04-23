<?php

	ob_start("ob_gzhandler");
	
	function stop_outputBuffer() {
		ob_flush();
	}

?>