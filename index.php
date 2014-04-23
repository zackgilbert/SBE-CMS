<?php

	if (isset($_GET['flinturl']) && (trim($_GET['flinturl'], "/") == 'phpinfo')) :
		phpinfo();
		die();
	endif;

	require_once dirname(__FILE__) . "/library/session.php";
	
	// Include DEBUG variables and functions.
	require_once dirname(__FILE__) . "/config/debug.php";
	if (!PRODUCTION && DEBUG) :
		require_once dirname(__FILE__) . "/library/functions.debug.php";
		timer_start();
	endif;
	
	/* Include variables that are needed no matter what. */
	require_once dirname(__FILE__) . "/library/functions.php";
	require_once dirname(__FILE__) . "/library/constants.core.php";
	require_once dirname(__FILE__) . "/library/functions.variables.php";
	
	if (!isset($_GET['flinturl'])) :
		redirect('./');
	endif;
	
	// Throw up a temp page until installation is completed.
	/*if (file_exists(dirname(__FILE__) . "/install/comingsoon.php") && (strpos($_SERVER['REQUEST_URI'], 'admin/scripts/install-cleanup') === false)) :
		define("LOCATION", get_location());
		include_once dirname(__FILE__) . "/install/comingsoon.php";
		exit;
	endif;*/
	
	if (!PRODUCTION) : 
		require_once LIBRARY . "requirements.php";

		if (SHOW_FLINT_ERRORS) :
			if (!server_meets_minimum_requirements()) :
				include SITES . "_SERVER_/error-requirements.php";
				exit;
			endif;
		endif;

		if (SHOW_FLINT_WARNINGS) :
			if (!server_meets_preferred_requirements()) :
				set_var('errors', get_server_warnings());
				dump(get_var('errors'));
			endif;
		endif;
	endif;

	require_once LIBRARY . "functions.core.php";
	require_once LIBRARY . "constants.time.php";
	require_once LIBRARY . "variables.time.php";
	require_once LIBRARY . "functions.time.php";
	require_once LIBRARY . "logger.php";

	if (isset($_GET['ignore']) && ($_GET['ignore'] == SERVER)) :
		$_SESSION['maintenance_ignore'] = NOW;
	endif;
	
	if (!is_admin() && MAINTENANCE && (!isset($_SESSION['maintenance_ignore']) || (NOW > hoursFromNow(1, $_SESSION['maintenance_ignore'])))) :
		load_server_page("maintenance");
		exit;
	endif;

	require_once LIBRARY . "outputbuffer.php";

	// Load site config settings
	require_once CONFIG . "settings.php";

	// Use memcache if it's available
	require_once LIBRARY . "memcache.php";

	// Include all variables and functions allowing for routes to work
	require_once CONFIG . "defaults.php";
	require_once CONFIG . "routes.php";
	require_once LIBRARY . "functions.routes.php";
	
	// Get all the variables as assigned from the routes and assign them to the global Flint variable
	set_vars(get_route_vars());	
	
	// Amazon S3 integration
	require_once CONFIG . 'S3.php';
	require_once LIBRARY . "class.S3.php";
	
	define("LOCATION", get_location(!is_admin()));
	define("MEDIAPATH", ((S3_USE) ? ('http://' . S3_BUCKET_NAME . '.s3.amazonaws.com/') : LOCATION));
		
	////////////////////////////////////////////////////
	// LOAD ALL THINGS RELATING TO DATABASE 		  //
	////////////////////////////////////////////////////
	require_once LIBRARY . "database.php";
	
	if (page_uses_database() && !$db->connect()) :
		if (DB_HAS_BEEN_CONFIRMED) :
			load_server_page("error-database-down");
		else:
			load_server_page("error-database-configure");
		endif;
		exit;
	endif;
		
	////////////////////////////////////////////////////
	// LOAD ALL THINGS RELATING TO USERS	 		  //
	////////////////////////////////////////////////////
	if (page_uses_users()) :
		// Include all user specific variables, classes and functions
		require_once LIBRARY . "users.php";

		if (is_restricted() && !$usr->canAccess()) :
			remember_return_page();
			redirect(get_restricted_login());
		endif;
	endif;
	
	// Include rest of variables and functions that'll be used throughout site
	require_once LIBRARY . "functions.sitemap.php";
	require_once LIBRARY . "functions.plugins.php";
	require_once LIBRARY . "functions.unsorted.php";

	// Assign predefined Session variables (fields & required) to page vars.
	if (isset($_SESSION['postFields']))
		set_page_var('postFields', $_SESSION['postFields']);
	if (isset($_SESSION['requiredFields']))
		set_page_var('requiredFields', $_SESSION['requiredFields']);
	
	if (isset($_GET['dump']) && ($_GET['dump'] == 'flint')) :

		dump(get_vars());

	else:
			
		// Include additional tools/features that'll be used throughout site
		require_once LIBRARY . "message.php";
		require_once LIBRARY . "functions.form.php";
		require_once LIBRARY . "functions.presentation.php";
		require_once LIBRARY . "functions.media.php";
		require_once CONFIG . "support.php";
		
		init_plugins();
		
		// Include theme specific library files.
		if (is_file(get_path() . "/library/index.php")) :
			include_once get_path() . "/library/index.php";
		endif;
				
		// Call page specific function to handle all pre-page loading actions.
		if (get_var('function')) :
			if (function_exists(get_var('function'))) :
				call_user_func(get_var('function'), get_var('params'));
			endif;
		endif;
				
		if (is_template_file(get_var('current_url'))) :
			if ((strpos(get('flint'), '/admin/') !== false) && !user_is_admin())
				die("Sorry, but you don't have access to this file.");
			load_template_file(get_var('current_url'));
		elseif (is_template_file(trim(get('flinturl'), '/') . ".php")) :
			if ((strpos(get('flinturl'), '/admin/') !== false) && !user_is_admin())
				die("Sorry, but you don't have access to this file.");
			load_template_file(trim(get('flinturl'), '/') . ".php");
		elseif (get_var('message')) :
			load_template_file('messages/' . get_var('message'), $_REQUEST);
		elseif (get_var('include')) :
			load_include(get_var('include'));
		elseif (get_var('script')) :
			load_script(get_var('script'));
		else :
			load_page(get_page(), get_template());
			remember_return_page(); // store this page as the previous page loaded...
		endif;
		
	endif;

	if (!PRODUCTION && DEBUG) :
		timer_stop();
	endif;

	clear_field_values();
	clear_required_fields();
	stop_message();
	stop_outputBuffer();

?>