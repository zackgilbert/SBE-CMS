<?php
	
	$routes = array(
		"phpinfo" => array('template' => false, 'page' => 'phpinfo'),
		
		// hide these folders from any users
		// THIS COULD PROBABLY BE IT'S OWN FILE
		"config" => array('defaults' => 'private'),
		"cache" => array('defaults' => 'private'),
		"docs" => array('defaults' => 'private'),
		"library" => array('defaults' => 'private'),
		"logs" => array('defaults' => 'private'),
		"private" => array('defaults' => 'private'),
		"plugins" => array('defaults' => 'private'),
		"config/**" => array('defaults' => 'private'),
		"cache/**" => array('defaults' => 'private'),
		"docs/**" => array('defaults' => 'private'),
		"library/**" => array('defaults' => 'private'),
		"logs/**" => array('defaults' => 'private'),
		"private/**" => array('defaults' => 'private'),
		"plugins/**" => array('defaults' => 'private'),
		
		// ADMIN DIRECTORY
		"admin" => array('defaults' => 'admin', 'page' => 'index', 'javascripts' => array('tiny_mce/tiny_mce_gzip', 'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php', 'tiny_mce/jquery-tinymce')),
		"admin/login" => array('defaults' => 'admin', 'template' => false, 'page' => '{2}', 'table' => 'users', 'restricted' => false),
		"admin/logout" => array('defaults' => 'admin', 'template' => false, 'script' => '{2}', 'restricted' => false),
		
		"admin/logs" => array('defaults' => 'admin', 'page' => '{2}', 'section' => 'settings', 'stylesheets' => array('settings')),
		"admin/logs/*" => array('redirect' => 'admin/logs'),
		"admin/logs/*/*" => array('defaults' => 'admin', 'page' => '{2}-view', 'section' => 'settings', 'type' => '{3}', 'file' => '{4}', 'stylesheets' => array('settings')),
		"admin/logs/*/*/edit" => array('defaults' => 'admin', 'page' => '{2}-{5}', 'section' => 'settings', 'type' => '{3}', 'file' => '{4}', 'stylesheets' => array('settings')),
		"admin/logs/*/*/delete" => array('defaults' => 'admin', 'template' => false, 'script' => '{2}-{5}', 'type' => '{3}', 'file' => '{4}'),
		"admin/logs/*/*/save" => array('defaults' => 'admin', 'template' => false, 'script' => '{2}-{5}', 'type' => '{3}', 'file' => '{4}'),
		
		"admin/status" => array('defaults' => 'admin', 'page' => '{2}', 'section' => 'tools'),

		"admin/includes/*" => array('defaults' => 'admin', 'template' => false, 'include' => '{3}'),
		"admin/scripts/*" => array('defaults' => 'admin', 'template' => false, 'script' => '{3}'),
		
		"admin/settings" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_settings', 'params' => ''),
		"admin/settings/save" => array('defaults' => 'admin', 'template' => false, 'page' => '{2}-{3}'),
		"admin/settings/**" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_settings', 'params' => '{3}'),
		
		"admin/support" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_support', 'params' => ''),
		"admin/support/save" => array('defaults' => 'admin', 'template' => false, 'page' => '{2}-{3}'),
		"admin/support/**" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_support', 'params' => '{3}'),
		
		"admin/sites" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}'),
		
		"admin/pages" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'javascripts' => array('jquery-ui-1.7.2.custom.min')),
		"admin/pages/reorder" => array('defaults' => 'admin', 'page' => '{2}-{3}', 'section' => '{2}', 'javascripts' => array('jquery-ui-1.7.2.custom.min')),
		"admin/pages/list" => array('defaults' => 'admin', 'page' => '{2}-{3}', 'section' => '{2}', 'javascripts' => array('jquery-dimensions', 'jquery-ui.mouse', 'jquery-ui.draggable', 'jquery-ui.droppable', 'jquery-ui.sortable', 'pages')),
		"admin/pages/update" => array('defaults' => 'admin', 'page' => '{2}-{3}'),
		"admin/pages/save" => array('defaults' => 'admin', 'script' => '{2}-{3}'),
		"admin/pages/new" => array('defaults' => 'admin', 'script' => '{2}-{3}'),
		"admin/pages/versions/*" => array('defaults' => 'admin', 'script' => '{2}-{3}', 'id' => '{4}'),
		"admin/pages/rollback/*" => array('defaults' => 'admin', 'script' => '{2}-{3}', 'id' => '{4}'),
		
		"admin/pages/*" => array('defaults' => 'admin', 'page' => '{2}-edit', 'section' => '{2}', 'subsection' => 'content', 'id' => '{3}', 'function' => 'admin_pages_content', 'stylesheets' => array('pages', 'pagination', 'pages-content-list'), 'javascripts' => array('tiny_mce/tiny_mce_gzip', 'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php', 'tiny_mce/jquery-tinymce')),
		//"admin/pages/*/delete" => array('defaults' => 'admin', 'page' => '{2}-{4}', 'id' => '{3}'),
		"admin/pages/*/settings" => array('defaults' => 'admin', 'page' => '{2}-edit', 'section' => '{2}', 'subsection' => '{4}', 'id' => '{3}', 'stylesheets' => array('pages')),
		"admin/pages/*/html" => array('defaults' => 'admin', 'page' => '{2}-edit', 'section' => '{2}', 'subsection' => '{4}', 'id' => '{3}', 'stylesheets' => array('pages')),
		"admin/pages/*/styles" => array('defaults' => 'admin', 'page' => '{2}-edit', 'section' => '{2}', 'subsection' => '{4}', 'id' => '{3}', 'stylesheets' => array('pages')),
		"admin/pages/*/delete" => array('defaults' => 'admin', 'page' => '{2}-edit', 'section' => '{2}', 'subsection' => '{4}', 'id' => '{3}', 'stylesheets' => array('pages')),
		"admin/pages/*/**" => array('defaults' => 'admin', 'page' => '{2}-edit', 'section' => '{2}', 'subsection' => 'content', 'id' => '{3}', 'stylesheets' => array('pages', 'pagination', 'pages-content-list'), 'javascripts' => array('tiny_mce/tiny_mce_gzip', 'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php', 'tiny_mce/jquery-tinymce'), 'params' => '{4}', 'function' => 'admin_pages_content'),
		
		"admin/editor" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_{2}', 'javascripts' => array('tiny_mce/tiny_mce_gzip', 'tiny_mce/plugins/tinybrowser/tb_tinymce.js.php', 'tiny_mce/jquery-tinymce')),
		
		"admin/stats" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'javascripts' => array('jquery-autoheight')),
		"admin/stats/*" => array('defaults' => 'admin', 'script' => '{2}-{3}', 'section' => '{2}'),
		
		"admin/comments" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_comments'),
		"admin/comments/*" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_comments', "id" => '{3}'),
		
		"admin/users" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_users', 'params' => ''),
		"admin/users/*/delete" => array('defaults' => 'admin', 'page' => '{2}-{4}', 'id' => '{3}'),
		"admin/users/**" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_users', 'params' => '{3}', 'stylesheets' => array('users')),
				
		"admin/*/delete" => array('defaults' => 'admin', 'page' => '{2}-{3}'),
		
		"admin/*" => array('defaults' => 'admin', 'page' => '{2}', 'section' => '{2}', 'function' => 'admin_{2}'),
		"admin/*/add" => array('defaults' => 'admin', 'page' => '{2}-edit', 'section' => '{2}'),
		"admin/*/save" => array('defaults' => 'admin', 'page' => '{2}-{3}'),
		"admin/*/*" => array('defaults' => 'admin', 'page' => '{2}-view', 'section' => '{2}', 'id' => '{3}'),
		"admin/*/*/edit" => array('defaults' => 'admin', 'page' => '{2}-{4}', 'section' => '{2}', 'id' => '{3}', "plugin" => "{2}"),
		"admin/*/*/delete" => array('defaults' => 'admin', 'page' => '{2}-{4}', 'section' => '{2}', 'id' => '{3}', "plugin" => "{2}"),
		
		// SITE DIRECTORY
		//"" => array("page" => "index", 'section' => '{1}'),
		/*"login" => array('page' => 'account-login', 'template' => 'account', 'function' => 'check_user_login'),
		"logout" => array('template' => false, 'page' => 'logout'),
		"signup" => array('page' => 'account-signup', 'template' => 'account'),
		"register" => array('template' => false, 'script' => 'register'),
		"cancel" => array('template' => false, 'script' => 'cancel'),*/

		//"*/save" => array('template' => false, 'script' => '{1}-save'),
		//"*/*/save" => array('template' => false, 'script' => '{2}-save'),
		//"*/delete" => array('template' => false, 'page' => '{1}-delete'),

		"messages/**" => array('template' => false, 'message' => '{2}'),
		"includes/**" => array('template' => false, 'include' => '{2}'),
		"scripts/**" => array('template' => false, 'script' => '{2}'),

		// catch all
		"**" => array('params' => '{all}', 'function' => 'site'),
	);
