	
	<div id="edit-header">
		
		<p class="section-path"><a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/">Back to All Bands</a></p>
		
		<?php if (is_numeric($band->id)) : ?> 
		<h2 class="edit-title">Edit "<?= $band->name(); ?>"</h2>
		<?php else : ?> 
		<h2 class="edit-title">Publish A New Band</h2>
		<?php endif; ?> 
		
		<?php if ($band->wasFound() && !is_string($band->deleted_at)) : ?> 	
			<div class="edit-delete">
				<a href="<?= LOCATION; ?>plugins/bands/admin/bands-delete/?id=<?= $band->id; ?>" onclick="return confirm('Are you sure you want to delete this band?');" title="Delete This Band" id="delete" class="delete-button" >
					<img src="<?= LOCATION; ?>admin/images/btn-deleteband.gif" alt="Delete This Band" />
				</a>
			</div>
		<?php endif; ?> 
	
	</div>	

	<?php if (is_string($band->deleted_at)) : ?> 
			
		<div class="deleted-content">
			
			<p>This Band Has Been Deleted!</p>
			
		</div>
		
	<?php endif; ?> 
	
	<form method="post" action="<?= LOCATION; ?>plugins/bands/admin/bands-save/" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="required" value="bands[name]" />
			<input type="hidden" name="redirect[success]" value="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/bands/<?= (isset($_GET['list'])) ? 'list/' : ''; ?>" />
			<input type="hidden" name="redirect[failure]" value="<?= $_SERVER['REQUEST_URI']; ?>" />
			<input type="hidden" name="bands[sitemap_id]" value="<?= get_var('id'); ?>"/>
			<?php if ($band->id > 0) :	?> 
			<input type="hidden" name="bands[id]" value="<?= $band->id; ?>"/>
			<?php endif; ?> 
		</div>
		
		<h3 class="item-title">Band Logo</h3>
		<dl>
			<dt>
				<img src="<?= $band->thumb(150, 150); ?>" alt="Band Logo" class="photo-thumb"/>
			</dt>
			<dd>
				<label for="default_photo" class="upload-title">Upload Image</label>
				<p class="thumb-instructions">
					You can upload a GIF, JPEG, or PNG. Images will be cropped to 180px by 180px.
				</p>
				<input type="file" id="default_photo" name="photo" size="25"/>
				
				<?php if ($band->hasPhoto()) : ?> 
					<div class="delete-photo">
						<a href="javascript:;" onclick="deletePhoto(this, '<?= $band->id; ?>');">
							<img src="<?= LOCATION; ?>admin/images/btn-deletephoto.gif" alt="Delete Photo"/>
						</a>
						<p>(a default photo will be displayed instead)</p>
					</div>
				<?php endif; ?> 
			</dd>
		</dl>
		
		<script type="text/javascript">
		
			function deletePhoto(link, band_id) {
				if (confirm("Are you sure you want to delete this band's photo?")) {
					$.post(root + 'admin/file/delete/?ajax', 
						{ file : 'bands/' + band_id, thumb : 'bands:'+band_id },
						function(data, textStatus) {
							if (data == 'true') {
								$(link.parentNode).fadeOut(500);
								$('.photo-thumb').parent().fadeOut(500);
							} else {
								ajaxError(textStatus);
							}
						}
					);
				}
			} // deletePhoto
		
		</script>
	
		<h3 class="item-title">Band Information</h3>
		
		<div class="edit-item">
			<label for="name">Name</label>
			<input type="text" id="name" name="bands[name]" value="<?= value('band[name]', $band->name); ?>" class="field-headline" />
		</div>
		
		<div class="edit-item">
			<label for="tagline">Tagline</label>
			<input type="text" id="tagline" name="bands[tagline]" value="<?= value('band[tagline]', $band->tagline); ?>" class="field-medium" />
		</div>
		
		<div class="edit-item">
			<label for="website">Website</label>
			<input type="text" id="website" name="bands[website_url]" value="<?= value('band[website_url]',  $band->website_url); ?>" class="field-medium" />
		</div>
		
		<div class="edit-item">
			<label for="biography">Biography (255 character limit)</label>
			<textarea id="biography" name="bands[biography]" cols="10" rows="4" class="body"><?= htmlentities2(value('bands[biography]', $band->biography)); ?></textarea>
		</div>
		
		<div class="edit-item">
				
			<label for="url">Band URL
			<?php if (!is_string($band->url)) : ?>
				<label class="auto-generate">
					<input type="checkbox" checked="checked" onchange="$('#url').get(0).disabled = this.checked;" /> Auto-generate
				</label>
			<?php endif; ?>
			</label>
			
			<input type="text" id="url" class="field-url" name="bands[url]" value="<?= value('bands[url]', $band->url); ?>"<?= (!is_string($band->url)) ? ' disabled="disabled"' : ''; ?>/>
			
		</div>
		
		<?php if (is_string($band->published_at)) : ?> 
		<div class="edit-item">

			<label for="published_at">Publish Date/Time</label>
			<input type="text" id="published_at" name="bands[published_at]" value="<?= $band->published_at; ?>" class="field-medium"/>

		</div>
		<?php endif; ?> 
		
		<?php if (is_string($band->deleted_at)) : ?> 
		<div class="edit-item">

			<label for="delete_at">Deletion Date/Time</label>
			<input type="text" id="delete_at" name="bands[delete_at]" value="<?= $band->deleted_at; ?>" class="field-medium"/>

		</div>
		<?php endif; ?>
		
		<div class="edit-item collapsable">
			
			<div class="edit-item-media edit-item-title" onclick="toggleContainer(this);">
				<h3 class="edit-item-media-title">
					<img src="<?= LOCATION; ?>admin/images/icon-arrow-closed.gif" alt=""/> Band Media
				</h3>
			</div>
			
			<div class="mediaplayer-container edit-item-content" style="display: none;">
				<h4 class="mediaplayer-title">Photo</h4>
				<p class="mediaplayer-description">
					If you have photos for this band, you can upload it here to have it appear on their page (.gif, .jpg, or .png files with a file size less than 5mb only).
				</p>
				<div class="mediaplayer-item">
				
					<?php foreach ($band->media('photo') as $photo): ?> 
					<div id="photo-<?= $photo['id']; ?>">
						<div>&nbsp;</div>
						<a href="<?= $photo['location']; ?>"><img src="<?= add_photo_info($photo['location'], 100, 100, '1:1'); ?>"/></a>
						<h4><?= valid($photo['title']); ?></h4>
						<?php if ($photo['description']) : ?> 
						<p>
							<?= valid($photo['description']); ?> 
						</p>
						<?php endif; ?> 
						<div class="utilities"><a href="javascript:;" onclick="deleteBandMedia(this, '<?= $photo['id']; ?>');">delete</a></div>
					</div>
					<?php endforeach; ?> 
					
					<div class="mediaplayer-item-add-btn media-add-container">
						<input type="button" value="Add New Photo" onclick="addNewBandMedia(this, 'photo');" />
					</div>
					
				</div>
				
				<script type="text/javascript" src="<?= LOCATION; ?>plugins/bands/javascripts/flashembed.js"></script>					
				<h4 class="mediaplayer-title">Video</h4>
				<p class="mediaplayer-description">
					Video content can be loaded on your page by direct link or using the embed code generated by the video site (i.e. YouTube or Vimeo).
				</p>
				<div class="mediaplayer-item">
				
					<?php foreach ($band->media('video') as $video): ?> 
					<div id="video-<?= $video['id']; ?>">
						<div>&nbsp;</div>
						<script type="text/javascript">
							$("#video-<?= $video['id']; ?> div:first").flashembed({
						    	src:'<?= valid(get_video_url($video["location"])); ?>',
						    	height:250,
								width:300
							});
						</script>
						<h4><?= valid($video['title']); ?></h4>
						<?php if ($video['description']) : ?> 
						<p>
							<?= valid($video['description']); ?> 
						</p>
						<?php endif; ?> 
						<div class="utilities"><a href="javascript:;" onclick="deleteBandMedia(this, '<?= $video['id']; ?>');">delete</a></div>
					</div>
					<?php endforeach; ?> 
					
					<div class="mediaplayer-item-add-btn media-add-container">
						<input type="button" value="Add New Video" onclick="addNewBandMedia(this, 'video');" />
					</div>
					
				</div>

				<script type="text/javascript" src="<?= LOCATION; ?>plugins/bands/javascripts/audioplayer.js"></script>
				
				<h4 class="mediaplayer-title">Audio</h4>
				<p class="mediaplayer-description">
					If you have audio for this band, you can upload it here to have it appear on their page (.mp3 files with a file size less than 5mb only).
				</p>
				<div class="mediaplayer-item">		
                    <?php foreach($band->media('audio') as $audio) : ?> 
					<div id="audio-<?= $audio['id']; ?>">
						<div id="audio-<?= $audio['id']; ?>-player">
							<script type="text/javascript">
								AudioPlayer.embed("audio-<?= $audio['id']; ?>-player", {  
									soundFile: "<?= $audio['location']; ?>",
									titles: "<?= $audio['description']; ?>",  
									artists: "<?= $audio['title']; ?>",  
									autostart: "no"  
								});  
							</script>						
						</div>
						<h4 class="audio-content-title"><?= valid($audio['title']); ?></h4>
						<p class="audio-content-description">
							<?= valid($audio['description']); ?> 
						</p>
						<div class="utilities"><a href="javascript:;" onclick="deleteBandMedia(this, '<?= $audio['id']; ?>');">delete</a></div>
					</div>
					<?php endforeach; ?> 

					<div class="mediaplayer-item-add-btn media-add-container">
                        <input type="button" value="Add New Audio" onclick="addNewBandMedia(this, 'audio');" />
                    </div>
                </div>
				
			</div>
			
		</div>

		<div class="edit-save">
			<?php if (!is_string($band->published_at)) : ?> 
				<input type="submit" name="publish-continue" value="Publish and Continue Editing" class="btn-submit" /> 
				<input type="submit" name="publish" value="Publish" class="btn-submit" /> 
			<?php endif; ?> 
			<input type="submit" name="continue" value="Save and Continue Editing" class="btn-submit"/> 
			<input type="submit" name="save" id="submit" value="Save" class="btn-submit" /> 
			or <a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/" class="cancel">Cancel</a>
		</div>
	
	</form>
	
	<?php if ($band->wasFound() && !is_string($band->deleted_at)) : ?> 
	<div id="live-preview-container" style="margin-top: 30px;">
		
		<a href="javascript:;" onclick="$('#live-preview').toggle();">Toggle Live Preview</a>

		<object id="live-preview" type="text/html" data="<?= $band->link(); ?>" style="height: 400px; width: 100%; display: none; margin-top: 10px">
			<a href="<?= $band->link(); ?>">View actual band page.</a>	
		</object>
		
	</div>		
	<?php endif; ?> 
	