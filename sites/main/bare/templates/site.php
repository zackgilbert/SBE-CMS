<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?= title(); ?></title>
<?= meta('description'); ?> 
<?= meta('keywords'); ?> 
<?= stylesheets(); ?> 
<?= javascripts(); ?> 
<?= rss(); ?> 
</head>
<body>

<div id="container">
	
	<div id="header">
		
	</div>
	
	<div id="content">
		
		<?= load_page(); ?> 
		
	</div>
	
	
	<div id="footer">
		
	</div>
	
</div>

<?php load_include('piwik'); ?> 
<?php display_message(); ?> 

</body>
</html>