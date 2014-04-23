<?php

	rename(ABSPATH . 'install', ABSPATH . '__install__');
	
	user_logout();
	
	redirect(LOCATION . 'admin/welcome/');

?>