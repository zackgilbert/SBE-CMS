	
	<div class="mediaplayer-item-add media-item">
	
		<div class="item-add-field">
			<h5 class="add-field-title"><label for="photo-file">Photo File</label></h5>
			<input type="file" name="media[photo][<?= post('count', '0'); ?>][file]" id="photo-file"/>
		</div>	
		
		<div class="item-add-field">
			<h5 class="add-field-title"><label for="photo-title">Title</label></h5>
			<input type="text" name="media[photo][<?= post('count', '0'); ?>][title]" id="photo-title" class="field-media-title"/>
		</div>
		
		<div class="item-add-field">
			<h5 class="add-field-title"><label for="photo-description">Description</label></h5>
			<textarea name="media[photo][<?= post('count', '0'); ?>][description]" id="photo-description" class="media-description"></textarea>
		</div>			
		
		<div class="item-add-cancel">
			<a href="javascript:;" onclick="cancelNewBandMedia(this);" class="cancel-upload">Cancel</a>
		</div>

	</div>
