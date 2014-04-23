	
	<div id="galleries-header">
	
		<p class="section-path"><a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/">Back to All Albums</a></p>
		
		<?php if ($gallery->id > 0) : ?> 
		<h2 class="galleries-edit-title">Manage Album "<?= $gallery->title(); ?>"</h2>
		<?php else : ?> 
		<h2 class="galleries-edit-title">Publish New Album</h2>
		<?php endif; ?> 
		
		<?php if ($gallery->wasFound() && !is_string($gallery->deleted_at)) : ?> 	
			<div class="edit-delete">
				<a href="<?= LOCATION; ?>plugins/galleries/admin/gallery-delete/?id=<?= $gallery->id; ?>" onclick="return confirm('Are you sure you want to delete this Album?');" title="Delete This Gallery" id="delete" class="delete-button" >
					<img src="<?= LOCATION; ?>admin/images/btn-deletealbum.gif" alt="Delete This Album" />
				</a>
			</div>
		<?php endif; ?>
	
	</div>

	<form method="post" action="<?= LOCATION; ?>plugins/galleries/admin/galleries-save/" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="required" value="galleries[name]" />
			<input type="hidden" name="redirect[success]" value="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/galleries/<?= (isset($_GET['list'])) ? 'list/' : ''; ?>" />
			<input type="hidden" name="redirect[failure]" value="<?= $_SERVER['REQUEST_URI']; ?>" />
			<?php if ($gallery->id > 0) : ?> 
			<input type="hidden" name="galleries[id]" value="<?= $gallery->id; ?>"/>
			<?php endif; ?> 
			<input type="hidden" name="galleries[sitemap_id]" value="<?= (is_numeric($gallery->sitemap_id)) ? $gallery->sitemap_id : get_var('id'); ?>"/>
		</div>

		<h3 class="gallery-item-title">Album Thumbnail</h3>
		<dl>
			<dt>
				<img src="<?= $gallery->thumb(150, 150); ?>" alt="Gallery Image" class="gallery-item-thumb"/>
			</dt>
			<dd>
				<label for="default_photo" class="upload-title">Upload Image</label>
				<p class="gallery-thumb-instructions">
					You can upload a GIF, JPEG, or PNG. Images will be cropped to 180px by 180px.
					<br/>(You can also set a photo to be used as the thumbnail for this album when adding one below.)
				</p>
				<input type="file" id="default_photo" name="photo" size="25"/>
				
				<?php if ($gallery->hasPhoto()) : ?> 
					<div class="delete-photo">
						<a href="javascript:;" onclick="deletePhoto(this, '<?= $gallery->id; ?>');">
							<img src="<?= LOCATION; ?>admin/images/btn-deletephoto.gif" alt="Delete Photo"/>
						</a>
						<p>(a default photo will be displayed instead)</p>
					</div>
				<?php endif; ?> 
			</dd>
		</dl>
			
		<h3 class="gallery-item-title">Album Information</h3>
			
		<dl>			
			<dt><label for="name">Name</label></dt>
			<dd><input type="text" id="name" name="galleries[name]" value="<?= value('galleries[name]', $gallery->name); ?>" class="field-medium" /></dd>
	
			<dt><label for="description">Description</label></dt>
			<dd><textarea id="description" name="galleries[description]" class="description" rows="4" cols="40"><?= htmlentities2(value('galleries[description]', $gallery->description)); ?></textarea></dd>
		</dl>
				
		<h3 class="gallery-item-title">Photos in this Album (<?= $gallery->photo_count(); ?>)</h3>
		<div>
			<?php if ($gallery->id > 0) : ?> 
			<input type="hidden" name="photos[gallery_id]" value="<?= $gallery->id; ?>"/>
			<?php endif; ?>
			<input type="hidden" name="photos[sitemap_id]" value="<?= ($gallery->sitemap_id) ? $gallery->sitemap_id : get_var('level_id'); ?>"/>
		</div>
		
		<div class="photos-container">
			<?php foreach ($gallery->photos() as $photo) : ?> 					
				<div class="photo-item">
					<a href="<?= $photo->photo(); ?>" id="gallery-photo-<?= $photo->id; ?>" rel="gallery-photos" title="<?= $photo->title(); ?>" class="photozoom">
						<img src="<?= $photo->thumb(75, 75); ?>" alt="Photo: <?= $photo->title(); ?>" class="photo-thumb" />
					</a>
					<h4><?= $photo->title(); ?></h4>
					<p><?= $photo->description(); ?></p>
					
					<div class="tools">
						<a href="javascript:;" onclick="deleteGalleryPhoto(this, '<?= $photo->id; ?>');" class="deletelink">Delete</a>
					</div>
				</div>	
			<?php endforeach; ?> 
		</div>
		
		<div class="photos-add-btn">
			<input type="button" value="Add New Photo" onclick="addGalleryPhoto(this);"/>
		</div>
							
		<h3 class="gallery-item-title">Additional Album Settings</h3>
		<div class="edit-item">
		
			<label for="url">Album URL
			<?php if (!is_string($gallery->url)) : ?>
				<label class="auto-generate">
					<input type="checkbox" checked="checked" onchange="$('#url').get(0).disabled = this.checked;" /> Auto-generate
				</label>
			<?php endif; ?>
			</label>
			<p class="edit-item-description">
				Use only simple keywords separated by dashes (ex: 'this-album-name').
			</p>
			<input type="text" id="url" class="field-url" name="galleries[url]" value="<?= value('galleries[url]', $gallery->url); ?>"<?= (!is_string($gallery->url)) ? ' disabled="disabled"' : ''; ?>/>
						
		</div>
		
		<?php if (is_string($gallery->published_at)) : ?>
		<div class="edit-item"> 		
			<label for="published_at">Published On</label>
			<input type="text" id="published_at" name="galleries[published_at]" value="<?= $gallery->published_at; ?>" class="field-medium"/>			
		</div>
		<?php endif; ?>
		
		<?php if (is_string($gallery->deleted_at)) : ?> 
		<div class="edit-item">		
			<label for="deleted_at">Deleted On</label>
			<input type="text" id="deleted_at" name="galleries[deleted_at]" value="<?= $gallery->deleted_at; ?>" class="field-medium"/>			
		</div>
		<?php endif; ?>

		<div class="edit-item">
		
			<label for="comment_status">Comments on Photos</label>
			<?php if (is_plugin('comments') && plugin_is_installed('comments') && (section('comments', get_var('id')) == 'enable')) : ?>
			<select id="comment_status" name="galleries[comment_status]" class="status">
			<?php foreach ($gallery->_comment_status_options as $key => $value) : ?> 
				<option value="<?= $key; ?>"<?= ($key == $gallery->comment_status) ? ' selected="selected"': ''; ?>><?= $value; ?></option>
			<?php endforeach; ?>
			</select>
			<?php else : ?> 
			<p class="edit-item-description">Commented are currently disabled. Either comments have not been installed on the site or they are not enabled for this section. To enable them, go to this section's <a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/settings/">Page Settings</a> and choose to turn them on.</p>
			<?php endif; ?> 
			
		</div>
		
		<div class="edit-save">
			<?php if (!is_string($gallery->published_at)) : ?> 
				<input type="submit" name="publish-continue" value="Publish and Continue Editing" class="btn-submit" /> 
				<input type="submit" name="publish" value="Publish" class="btn-submit" /> 
			<?php endif; ?> 
			<input type="submit" name="continue" value="Save and Continue Editing" class="btn-submit"/> 
			<input type="submit" name="save" id="submit" value="Save" class="btn-submit" /> 
			or <a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/" class="cancel">Cancel</a>
		</div>
	
	</form>
	
	<script type="text/javascript" src="<?= LOCATION; ?>plugins/galleries/admin/jquery.fancybox-1.2.6.js"></script>
	<script type="text/javascript">
	
		function deletePhoto(link, gallery_id) {
			if (confirm("Are you sure you want to delete this gallery's default photo?")) {
				$.post(root + 'admin/file/delete/?ajax', 
					{ file : 'galleries/' + gallery_id, thumb : 'galleries:'+gallery_id },
					function(data, textStatus) {
						if (data == 'true') {
							$('.delete-photo').fadeOut(500);
							$('.gallery-item-thumb').parent().fadeOut(500);
						} else {
							ajaxError(textStatus);
						}
					}
				);
			}
		} // deletePhoto
		
		function deleteGalleryPhoto(link, photo_id) {
			$.post(root + 'plugins/galleries/admin/galleries-deletephoto/?ajax', 
				{ photo_id : photo_id },
				function(data, textStatus) {
					if (data == 'true') {
						$(link.parentNode.parentNode).fadeOut(500, function(){ $(this).remove(); });
					} else {
						ajaxError(data);
					}
				}
			);
		} // deleteGalleryPhoto
	
		function addGalleryPhoto(link) {
			var id = $('.photos-container .item-add:last').attr('id');
			if (!id) id = "--1";
			$.post(root + 'plugins/galleries/admin/galleries-add-photo/', 
				{ num : 1+parseInt(id.substr(id.indexOf('-')+1)) }, 
				function(data, textStatus) {
					$('.photos-container').append(data);
				}
			);
		} // addGalleryPhoto
		
		$(function() {
			$("a.photozoom").fancybox({
				'fancyIn'	: false,
				'fancyOut'	: false
			});	
		});
		
	</script>