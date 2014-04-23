<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?= title('Site Manager'); ?></title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<?= stylesheets(); ?> 
<?= javascripts(); ?>
</head>

<body style="margin:0px;">
		
	<div class="page-add">
			
		<form method="post" action="<?= LOCATION; ?>admin/pages/new/">

			<h3 class="page-title">
				Add New Page
			</h3>
			
			<div class="page-item">				
				<label for="page-name">Page Name</label>
				<input type="text" name="page[name]" id="page-name" value="<?= value('page[name]'); ?>" class="field-medium" onkeypress="populateUrl();" onblur="populateUrl();"/>
				<p class="page-item-description">
					Used when linking to this page, and as the title in browsers.				
				</p>
			</div>
			
			<div class="page-item">
				<label for="page-insert">Insert</label>
				<select name="page[where]" class="location">
					<option value="after"<?= (value('page[where]', 'after') == 'after') ? ' selected="selected"' : ''; ?>>After</option>
					<option value="before"<?= (value('page[where]') == 'before') ? ' selected="selected"' : ''; ?>>Before</option>
					<option value="child"<?= (value('page[where]') == 'child') ? ' selected="selected"' : ''; ?>>A Subpage of</option>
				</select>
				<select name="page[section]" class="location">
				<?php foreach ($sitemap = get_sitemap() as $sect) : ?> 
					<?php load_include('pages-option', array('section' => $sect, 'i' => 0, 'default' => end($sitemap))); ?> 
				<?php endforeach; ?> 
				</select>
				<p class="page-item-description">
					Specifies where in the sitemap this page is placed.				
				</p>
			</div>
			
			<div class="page-item">
				<label for="page-url">Page URL</label>
				<input type="text" name="page[url]" id="page-url" value="<?= value('page[url]'); ?>" class="field-medium" onchange="cancelAutoPopulate();"/>
				<p class="page-item-description">
					This is the web address for this page.	
				</p>
			</div>
			
			<?php if (($page_types = get_page_type_plugins()) && (count($page_types) > 0)) : ?> 
			<div class="page-item">
				<label>Page Type</label>
				<select name="page[type]" class="location">
					<option value="static">Static HTML</option>
					<?php foreach ($page_types as $type) : ?> 
					<option value="<?= $type; ?>"><?= capitalize($type); ?></option>
					<?php endforeach; ?> 
				</select>
				<p class="page-item-description">Choose what type of content this page will have.</p>
				
			</div>
			<?php endif; ?>
			
		</form>
		
		<div class="page-item-save">						
			<img src="../images/btn-sheet-savepage.gif" alt="Save Page" onclick="$('form').submit();" />
			or <a href="javascript:;" onclick="parent.$.fn.sheet.close();" class="cancel">Cancel</a>
		</div>
		
	</div>
	
<?= display_message(); ?> 

	<script type="text/javascript">
	
		var autopop = true;
		function populateUrl() {
			if (autopop)
				$('#page-url').attr('value', $('#page-name').val().toLowerCase().replace(/[ ]+/g, '-').replace(/[\.?!;:,\"\']+/g, ''));
		} // populateUrl
		
		function cancelAutoPopulate() {
			autopop = false;
		} // cancelAutoPopulate
	
	</script>

</body>
</html>	