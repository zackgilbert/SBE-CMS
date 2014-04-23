	
	<div class="mediaplayer-item-add media-item">
	
		<div class="item-add-field">
			<h5 class="add-field-title"><label for="video-location">Video URL or Embed Code</label></h5>
			<textarea name="media[video][<?= post('count', '0'); ?>][location]" id="video-location" class="media-description"></textarea>
		</div>	
		
		<div class="item-add-field">
			<h5 class="add-field-title"><label for="video-location">Video Title</label></h5>
			<input type="text" name="media[video][<?= post('count', '0'); ?>][title]" id="video-title" class="field-media-title"/>
		</div>
		
		<div class="item-add-field">
			<h5 class="add-field-title"><label for="video-description">Video Description</label></h5>
			<textarea name="media[video][<?= post('count', '0'); ?>][description]" id="video-description" class="media-description"></textarea>
		</div>			
		
		<div class="item-add-cancel">
			<a href="javascript:;" onclick="cancelNewBandMedia(this);" class="cancel-upload">Cancel</a>
		</div>
						
	</div>
