<?php if ($section['type'] == 'static') : ?> 
	
	<form method="post" action="<?= LOCATION; ?>admin/pages/save/" enctype="multipart/form-data">
	
		<div>
			<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>" />
			<input type="hidden" name="file" value="<?= $css = str_replace(array(".php", 'pages'), array(".css", 'stylesheets'), get_page_file_location($section)); ?>" /> 
			<input type="hidden" name="page" value="<?= $section['id']; ?>" /> 
		</div>
		
		<h3 class="page-settings-title">Page Styles <span class="path">In File: <?= str_replace(ABSPATH, "", $css); ?></span></h3>

		<div class="page-settings-group">
			
			<?php if (file_exists($css) || isset($_GET['create'])) : ?>
				<textarea name="content" id="editable-styles" class="styles codepress css autocomplete-off" rows="10" cols="60"><?= (($css) && file_exists($css)) ? htmlentities(file_get_contents($css)) : ''; ?></textarea>
			<?php else : ?> 
				<div class="setting-item-nostyles">
					
					<h4>This page doesn't have it's own stylesheet.</h4>
					<h5>To apply styles to this page directly, you need to have a stylesheet named <em><strong>'pagename'.css</strong></em>.</h5>
					
					<p>
					You can create this in the editor of your choice and upload it to the <em><strong>/stylesheets/</strong></em> directory, or you can create the stylesheet directly here in the admin.<br />
					<a href="?create"><img src="<?= LOCATION; ?>admin/images/btn-createstylesheet.gif" alt="Create Stylesheet" title="Create a Stylesheet for this Page" /></a>
					</p>
					
				</div>
			<?php endif; ?> 
			
		</div>
		
		<?php if (file_exists($css) || isset($_GET['create'])) : ?> 
			<div id="content-editable-save">
				<input type="submit" value="Save Page Styles" class="btn-save"/>
			</div>
		<?php endif; ?> 
		
	</form>

<?php else : // plugin ?> 
	
	<div id="editable-nofile">
		<h4>The CSS for this page type is not editable.</h4>
		<h5>This page type has specific stylesheets. So the CSS is not directly editable CSS. If you need to make changes to this page, please <a href="<?= LOCATION; ?>admin/support/">submit a support ticket</a>.</h5>
	</div>

<?php endif; ?>
	
	<script type="text/javascript" src="<?= LOCATION; ?>admin/javascripts/codepress/codepress.js"></script>
	