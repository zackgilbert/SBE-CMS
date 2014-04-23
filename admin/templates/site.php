<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?= title('Site Manager'); ?></title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<?= stylesheets(); ?> 
<?= javascripts(); ?> 
</head>

<body>

	<div id="header-container">	
		<div id="ssm-tools">
			<p class="ssm-branding">
				<span class="seen">SEEN</span> Site Manager
			</p>
			<p class="user-greeting">
				Hello, <?= user('name'); ?> (<a href="<?= LOCATION; ?>admin/logout/">logout</a>) &nbsp; <a href="<?= LOCATION; ?>admin/includes/user-info/" class="edit-info">Edit User Info</a>
			</p>
		</div>	
		<div id="header">		
			<h1 class="site-name"><?= valid(get_metadata('title')); ?></h1>
			<ul id="site-switcher">
				<li><a href="<?= get_location(); ?>">View Site</a></li>
			</ul>
		</div>
	</div>	
	<div id="nav-container">
		<div id="primary-nav">
			<ul class="primary-nav-left">
				<li<? if (!get_var('section')): ?> class="selected"<?php endif; ?>><a href="<?= LOCATION; ?>admin/">Dashboard</a></li>
				<li<? if (is_section('pages')): ?> class="selected"<?php endif; ?>><a href="<?= LOCATION; ?>admin/pages/">Pages</a></li>
				<?php if (is_plugin('comments') && plugin_is_installed('comments')) : ?> 
				<li<? if (is_section('comments')): ?> class="selected"<?php endif; ?>><a href="<?= LOCATION; ?>admin/comments/">Comments</a></li>
				<?php endif; ?> 
				<?php if (user_is_admin()) : ?> 
				<li<? if (is_section('users')): ?> class="selected"<?php endif; ?>><a href="<?= LOCATION; ?>admin/users/">Users</a></li>
				<li<? if (is_section('editor')): ?> class="selected"<?php endif; ?>><a href="<?= LOCATION; ?>admin/editor/">File Editor</a></li>
				<?php endif; ?> 
				<li<? if (is_section('stats')): ?> class="selected"<?php endif; ?>><a href="<?= LOCATION; ?>admin/stats/">Stats</a></li>
				<?php if (user_is_admin()) : ?> 
				<li<? if (is_section('settings')): ?> class="selected"<?php endif; ?>><a href="<?= LOCATION; ?>admin/settings/">Settings</a></li>
				<?php endif; ?> 
				<li<? if (is_section('support')): ?> class="selected"<?php endif; ?>><a href="<?= LOCATION; ?>admin/support/">Support</a></li>
			</ul>
		</div>
		<!--<div id="secondary-nav"></div>-->
	</div>
		
	<div id="content-container">
		
		<?php load_page(); ?>
		
	</div>
	
	<div id="footer-container">
		<div id="footer">
			<div class="footer-logo">
				<a href="http://www.areyouseen.com"><img src="<?= LOCATION; ?>admin/images/logo-footer-seen.gif" alt="SEEN Creative Consultancy" /></a>
			</div>
			<p class="footer-links">
				<a href="<?= LOCATION; ?>admin/support/">Need Help?</a>  <!-- &nbsp;&#8226;&nbsp; <a href="#">Terms of Service</a>  &nbsp;&#8226;&nbsp;  <a href="#">Privacy Policy</a>-->
			</p>
			<p class="footer-copyright">
				&copy; Copyright <?= year(); ?> - All Rights Reserved.				
			</p>
		</div>		
	</div>
	
<?php if (isset($_GET['debug'])) : ?> 
<div><a href="javascript:;" onclick="$('#vardump').toggle();">Show Vars</a></div>
<div id="vardump" style="display: none; background-color: #fff; padding: 10px; color: #000; font-size: 12px;">
	<?= dump(get_vars()); ?> 
</div>
<?php endif; ?> 

<?= display_message(); ?> 

</body>
</html>