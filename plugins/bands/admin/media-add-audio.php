
	<div class="mediaplayer-item-add media-item">
	
		<div class="item-add-field">
			<h5 class="add-field-title"><label for="audio-file">Audio File</label></h5>
			<input type="file" name="media[audio][<?= post('count', '0'); ?>][file]" id="audio-file"/>
		</div>	
		
		<div class="item-add-field">
			<h5 class="add-field-title"><label for="audio-title">Audio Title (Artist)</label></h5>
			<input type="text" name="media[audio][<?= post('count', '0'); ?>][title]" id="audio-title" class="field-media-title"/>
		</div>
		
		<div class="item-add-field">
			<h5 class="add-field-title"><label for="audio-description">Audio Description (Track Name)</label></h5>
			<textarea name="media[audio][<?= post('count', '0'); ?>][description]" id="audio-description" class="media-description"></textarea>
		</div>			
		
		<div class="item-add-cancel">
			<a href="javascript:;" onclick="cancelNewBandMedia(this);" class="cancel-upload">Cancel</a>
		</div>

	</div>
