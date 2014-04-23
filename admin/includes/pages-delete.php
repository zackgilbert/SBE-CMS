	
	<h2 class="page-status-title <?= (is_string($section['disabled_at'])) ? 'hidden' : 'visible'; ?>">This page is currently <?= (is_string($section['disabled_at'])) ? '<strong>Hidden</strong>' : '<strong>Visible</strong>'; ?></h2>
	
	<?php if (is_string($section['disabled_at'])) : ?> 
		<h3 class="page-settings-title">Make Page Visible</h3>
		<div class="page-settings-group">
			<p class="page-hide-instructions">This page is currently hidden to anyone visiting your site. You can make it Visible again so everyone can view it.</p>
			<label><input type="checkbox" onchange="(this.checked) ? $('#enable').removeAttr('disabled') : $('#enable').attr('disabled', 'disabled');"/>I'm sure I want to make this page visible.</label><br/><br/>
			<input type="button" id="enable" value="Make Visible" disabled="disabled" onclick="pageStatus('<?= $section['id']; ?>', 'enable');"/>
		</div>
	<?php else : ?> 
		<h3 class="page-settings-title">Hide Page</h3>
		<div class="page-settings-group">
			<p class="page-hide-instructions">You can Hide your page so that no one can access it. It will still exist here in the admin for you to edit, but no one visiting your site will be able to view it. (You can make this page visible again whenever you'd like.)</p>
			<label><input type="checkbox" onchange="(this.checked) ? $('#disable').removeAttr('disabled') : $('#disable').attr('disabled', 'disabled');"/>I'm sure I want to hide this page.</label><br/><br/>
			<input type="button" id="disable" value="Hide Page" disabled="disabled" onclick="pageStatus('<?= $section['id']; ?>', 'disable');"/>
		</div>
	<?php endif; ?> 
	
	<h3 class="page-settings-title">Delete Page</h3>
	<div class="page-settings-group">
		<p class="page-hide-instructions">You can completely Delete this page and all it's content from your site. Be aware that this is immediate and permanent. You wont be able to recover lost data. If you're unsure, we advise you to just make this page Hidden.</p>
		<label><input type="checkbox" onchange="(this.checked) ? $('#delete').removeAttr('disabled') : $('#delete').attr('disabled', 'disabled');"/>I understand, and I want to completely Delete this Page from my site.</label><br/><br/>
		<input type="button" id="delete" value="Delete Page" disabled="disabled" onclick="pageDelete('<?= $section['id']; ?>');"/>
	</div>
	
	<script type="text/javascript">
	
		function pageStatus(id, status) {
			$.post(root + 'admin/scripts/pages-status/?ajax', { id : id , status : status }, function(data, textStatus) {
				if (data == 'true') {
					window.location = window.location;
				} else {
					ajaxError(data);
				}
			});
		} // pageStatus
	
		function pageDelete(id) {
			$.post(root + 'admin/scripts/pages-delete/?ajax', { id : id }, function(data, textStatus) {
				if (data == 'true') {
					window.location = root + 'admin/pages/';
				} else {
					ajaxError(data);
				}
			});
		} // pageDelete
	
	</script>
	