<?php

	$r = (get_session_var('referral_page')) ? get_session_var('referral_page') : LOCATION . 'admin/login/';
	
	user_logout();
	redirect($r);

?>