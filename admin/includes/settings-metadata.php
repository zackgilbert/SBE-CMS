
	<form method="post" action="<?= LOCATION; ?>admin/settings/save/" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>"/>
			<input type="hidden" name="which" value="<?= $settings; ?>"/>
		</div>
	
		<div class="setting-item">
			<h3 class="setting-item-title">Site Information</h3> 

			<div class="item-edit">
				<label>Title</label>
				<input type="text" name="metadata[title]" value="<?= get_metadata("title"); ?>" class="field-medium"/>
			</div>

			<div class="item-edit">
				<label>Description</label>
				<textarea name="metadata[description]" rows="4" cols="40"><?= get_metadata("description"); ?></textarea>
			</div>

			<div class="item-edit">
				<label>Keywords</label>
				<textarea name="metadata[keywords]" rows="4" cols="40"><?= get_metadata("keywords"); ?></textarea>
			</div>

		</div>
		
		<div class="setting-save">
			<input type="submit" value="Save Settings" class="btn-submit"/>
		</div>
	
	</form>
