<?php if ($section['type'] == 'static') : ?> 

	<form method="post" action="<?= LOCATION; ?>admin/pages/save/" enctype="multipart/form-data">
	
		<div>
			<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>" />
			<input type="hidden" name="file" value="<?= $html = get_page_file_location($section); ?>" /> 
			<input type="hidden" name="page" value="<?= $section['id']; ?>" /> 
		</div>
		
		<h3 class="page-settings-title">Page HTML <span class="path">In File: <?= str_replace(ABSPATH, "", $html); ?></span></h3>
		
		<div class="page-settings-group">
			
			<textarea name="content" id="editable-html" class="html codepress autocomplete-off" rows="10" cols="60"><?= (file_exists($html)) ? htmlentities(file_get_contents($html)) : ""; ?></textarea>
			
		</div>
				
		<div id="content-editable-save">
			<input type="submit" value="Save Page HTML" class="btn-save"/>
			<div class="save-noversion">
				<label><input type="checkbox" name="dontVersion" value="true"/> I only made small changes, don't save a new version.</label>
			</div>
		</div>
		
	</form>

<?php else : // plugin ?> 
	
	<div id="editable-nofile">
		<h4>The HTML for this page type is not editable.</h4>
		<h5>This page type has specific functionality that contains dynamic, database-driven content. So there is no editable HTML. If you need to make changes to this page, please <a href="<?= LOCATION; ?>admin/support/">submit a support ticket</a>.</h5>
	</div>

<?php endif; ?> 
	
	<script type="text/javascript" src="<?= LOCATION; ?>admin/javascripts/codepress/codepress.js"></script>
