<?php
	
	require_plugin('votes');
	require_plugin('clips');
	require_plugin('authors');
	
	// load plugin specific loading code...
	define('BLOGS_PLUGIN', dirname(__FILE__) . "/");
	
	require_once('class.blog.php');
	include_once('functions.php');
	
	/*
	if ((get_var('content') == 'blogs') && get_var('id')) :
	
		$blog = new blog(get_var('id'));
		set_page_var('item', $blog);
	
	else:
		
		$blog = new blog();
		
	endif;
	
	set_page_var('blog', $blog);
	*/
	
?>