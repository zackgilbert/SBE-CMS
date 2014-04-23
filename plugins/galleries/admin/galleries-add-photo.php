	
	<div id="photo-<?= request('num'); ?>" class="item-add photo-item">		
	
		<div class="item-add-field">
			<label for="add-photo-title-<?= request('num'); ?>">Photo Title</label>
			<input type="text" id="add-photo-title-<?= request('num'); ?>" name="photos[<?= request('num'); ?>][title]" class="field-medium"/>
		</div>
		
		<div class="item-add-field">	
			<label for="add-photo-description-<?= request('num'); ?>">Photo Description:</label>
			<textarea id="add-photo-description-<?= request('num'); ?>" name="photos[<?= request('num'); ?>][description]" class="media-description"></textarea>
		</div>
		
		<div class="item-add-field">	
			<label for="add-photo-file-<?= request('num'); ?>">Image File:</label>
			<input type="file" id="add-photo-file-<?= request('num'); ?>" name="photos[<?= request('num'); ?>]" size="25" class="field-medium"/>
		</div>	
			
		<div class="item-add-field">	
			<label><input type="checkbox" name="photos[default]" value="<?= request('num'); ?>"/>Use this photo as thumbnail for the Album</label>
		</div>		
			
		<div class="tools">
			<a href="javascript:;" onclick="$(this.parentNode.parentNode).fadeOut(500, function(){ $(this).remove(); });" class="deletelink">Remove Photo</a>
		</div>
	</div>
