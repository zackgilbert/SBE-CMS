<?php

	if (flush_memcache()) {
		echo "memcache flush successful...";
	} else {
		echo "memcache flush was NOT successful...";
	}

?>