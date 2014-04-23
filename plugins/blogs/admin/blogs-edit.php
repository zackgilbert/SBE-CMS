	
	<div id="edit-header">
		
		<p class="section-path"><a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/">Back to All Blog Posts</a></p>
		
		<?php if (is_numeric($blog->id)) : ?> 
		<h2 class="edit-title">Edit "<?= $blog->title ?>"</h2>
		<?php else : ?> 
		<h2 class="edit-title">Publish A New Blog Post</h2>
		<?php endif; ?> 
		
		<?php if ($blog->wasFound() && !is_string($blog->deleted_at)) : ?> 	
			<div class="edit-delete">
				<a href="<?= LOCATION; ?>plugins/blogs/admin/blogs-delete/?id=<?= $blog->id; ?>" onclick="return confirm('Are you sure you want to delete this blog post?');" title="Delete This Blog Post" id="delete" class="delete-button" >
					<img src="<?= LOCATION; ?>admin/images/btn-deletepost.gif" alt="Delete This Blog Post" />
				</a>
			</div>
		<?php endif; ?>
	
	</div>	

	<?php if (is_string($blog->deleted_at)) : ?> 
			
		<div class="deleted-content">
			
			<p>This Blog Post Has Been Deleted!</p>
			
		</div>
		
	<?php endif; ?> 
	
	<form method="post" action="<?= LOCATION; ?>plugins/blogs/admin/blogs-save/" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="required" value="blogs[body]" />
			<input type="hidden" name="redirect[success]" value="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/blogs/<?= (isset($_GET['list'])) ? 'list/' : ''; ?>" />
			<input type="hidden" name="redirect[failure]" value="<?= $_SERVER['REQUEST_URI']; ?>" />
			<input type="hidden" name="blog_sitemap[sitemap_id]" value="<?= get_var('id'); ?>"/>
			<input type="hidden" name="blogs[user_id]" value="<?= user('id'); ?>"/>
			<?php if ($blog->id > 0) :	?> 
			<input type="hidden" name="blogs[id]" value="<?= $blog->id; ?>"/>
			<?php endif; ?> 
		</div>
		
		<div class="edit-item">
			<label>Blog Location</label>
			<select name="blog_sitemap[sitemap_id]" class="author">
			<?php foreach (get_sitemap_sections_by_content('blogs') as $section) : ?> 
				<option value="<?= $section['id']; ?>"<?= ($section['id'] == $blog->section()) ? ' selected="selected"' : ''; ?>><?= build_page_location($section['id']); ?></option>
			<?php endforeach; ?> 
			</select>
		</div>
		
		<div class="edit-item">
			<label>Author</label>
			<div class="author-container">
				<select name="blogs[user_id]" style="clear: left;" class="author">
					<option value="">&nbsp;</option>
					<?php foreach (get_blog_authors() as $author) : ?> 
					<option value="<?= $author['id']; ?>"<?= ((is_numeric($blog->user_id) && ($author['id'] == $blog->user_id)) || (!is_numeric($blog->user_id) && ($author['id'] == user('id')))) ? ' selected="selected"': ''; ?>><?= valid($author['name']); ?></option>
					<?php endforeach; ?> 
				</select>
			</div>
		</div>
		
		<div class="edit-item">
			<label for="title">Title</label>
			<input type="text" id="title" name="blogs[title]" value="<?= value('blog[title]', str_replace('"', '&quot;', $blog->title)); ?>" class="field-headline" />
		</div>

		<div class="edit-item">
			<label for="body">Body</label>
			<textarea id="body" name="blogs[body]" cols="10" rows="4" class="body wysiwyg"><?= htmlentities2(value('blogs[body]', format_text($blog->body))); ?></textarea>
		</div>
		
		<?php if (count(blog_categories(get_var('id'))) > 0) : ?> 
		<div class="edit-item">
			<label for="categories">Category</label>
			<select name="blogs[categories][]" id="categories">
				<option value="">&nbsp;</option>
			<?php foreach (blog_categories(get_var('id')) as $category) : ?> 
				<option value="<?= $category['url']; ?>"<?= (in_array($category['url'], $blog->categories())) ? ' selected="selected"': ''; ?>><?= $category['name']; ?></option>
			<?php endforeach; ?> 
			</select>
		</div>
		<?php endif; ?> 

		<div class="edit-item">
				
			<label for="url">Blog Post URL
			<?php if (!is_string($blog->url)) : ?>
				<label class="auto-generate">
					<input type="checkbox" checked="checked" onchange="$('#url').get(0).disabled = this.checked;" /> Auto-generate
				</label>
			<?php endif; ?>
			</label>
			<p class="edit-item-description">
				Use only simple keywords separated by dashes (ex: 'no-progress-land-development-debate'). TIP: Think of how someone would search for this blog, and optimize for those keywords. 
			</p>
			<input type="text" id="url" class="field-url" name="blogs[url]" value="<?= value('blogs[url]', $blog->url); ?>"<?= (!is_string($blog->url)) ? ' disabled="disabled"' : ''; ?>/>
			
		</div>
		
		<div class="edit-item">

			<label for="excerpt">Excerpt
			<?php if (!is_string($blog->excerpt)) : ?>
				<label class="auto-generate">
					<input type="checkbox" checked="checked" onchange="$('#excerpt').get(0).disabled = this.checked;" /> Auto-generate
				</label>
			<?php endif; ?>
			</label> 
			<p class="edit-item-description">
				This excerpt is displayed on main pages and in search results. It should be 15-25 words. If you choose to auto-generate, it will contain the first 100 characters of the body content.
			</p>
			<textarea id="excerpt" class="excerpt" rows="5" cols="40" name="blogs[excerpt]"<?= (!is_string($blog->excerpt)) ? ' disabled="disabled"' : ''; ?>><?= htmlentities2(value('blogs[excerpt]', $blog->excerpt())); ?></textarea>
			
		</div>
		
		<!--<div class="edit-item">
		
			<label for="status">Publish Status</label>
			<select id="status" name="blogs[status]" class="status">
			<?php foreach ($blog->_status_options as $key => $value) : ?> 
				<option value="<?= $key; ?>"<?= ($key == $blog->status) ? ' selected="selected"': ''; ?>><?= $value; ?></option>
			<?php endforeach; ?> 
			</select>
			
		</div>-->
		
		<?php if (is_string($blog->published_at)) : ?> 
		<div class="edit-item">

			<label for="published_at">Publish Date/Time</label>
			<input type="text" id="published_at" name="blogs[published_at]" value="<?= $blog->published_at; ?>" class="field-medium"/>

		</div>
		<?php endif; ?> 
		
		<?php if (is_string($blog->deleted_at)) : ?> 
		<div class="edit-item">

			<label for="delete_at">Deletion Date/Time</label>
			<input type="text" id="delete_at" name="blogs[delete_at]" value="<?= $blog->deleted_at; ?>" class="field-medium"/>

		</div>
		<?php endif; ?>

		<div class="edit-item">
		
			<label for="comment_status">Comment Settings</label>
			<select id="comment_status" name="blogs[comment_status]" class="status">
			<?php foreach ($blog->_comment_status_options as $key => $value) : ?> 
				<option value="<?= $key; ?>"<?= ($key == $blog->comment_status) ? ' selected="selected"': ''; ?>><?= $value; ?></option>
			<?php endforeach; ?> 
			</select>		
				
		</div>
		
		<?php /*?><div class="edit-item collapsable">
			
			<div class="edit-item-media edit-item-title" onclick="toggleContainer(this);">
				<h3 class="edit-item-media-title">
					<img src="<?= LOCATION; ?>admin/images/icon-arrow-closed.gif" alt=""/> Blog Post Images
				</h3>
			</div>
			
			<div class="articleimages-container edit-item-content" style="display: none;">
				
				<h4 class="articleimages-title">Banner Image</h4>
				<p class="articleimages-description">This image appears below the headline the blog. It should have a caption. (Width should be 606px.)</p>								
				<div class="articleimages-item">
					
					<?php if ($blog->banner()) : ?> 
					<div class="articleimages-item-add">
						<img src="<?= $blog->banner('image'); ?>" alt="banner image"/>
						<?php if ($blog->banner('caption')) : ?> 
						<p><?= $blog->banner('caption'); ?></p>
						<?php endif; ?> 
						<div><a href="javascript:;" onclick="deleteArticleBanner(this, '<?= $blog->banner('id'); ?>');">Delete</a></div>
					</div> 
					<?php endif; ?>
					
					<div class="articleimages-item-add media-add-container"<?= ($blog->banner()) ? ' style="display:none;"' : ''; ?>>
						<div class="item-add-field">
							<h5 class="add-field-title"><label for="banner-file">Image File:</label></h5>
							<input type="file" id="banner-file" name="media[banner][file]" />
						</div>
						<div class="item-add-field">
							<h5 class="add-field-title"><label for="caption">Caption: (limit 255 characters)</label></h5>
							<textarea name="media[banner][description]" id="caption" cols="20" rows="1" class="caption"></textarea>
						</div>
					</div>
					
				</div>

				<h4 class="articleimages-title">Sidebar Items</h4>
				<p class="articleimages-description">
					Sidebar items can be images (ex: JPG's) or files (ex: PDF's). Each item should have a title and description.
				</p>
				<div class="articleimages-item">

					<?php foreach ($blog->sidebarContent() as $sidebarItem) : ?> 
					<div class="articleimages-item-add media-item">
						<?php if ($sidebarItem['type'] == 'photo') : ?> 
						<div class="photozoom">
							<p class="media-item-title"><?= $sidebarItem['title']; ?></p>
							<a href="<?= LOCATION; ?><?= $sidebarItem['location']; ?>" id="sidebar-media-<?= $sidebarItem['id']; ?>" rel="media-item" title="<?= $sidebarItem['title'] . (($sidebarItem['description']) ? ' - ' . $sidebarItem['description'] : ''); ?>"><img src="<?= add_photo_info(LOCATION . $sidebarItem['location'], 195); ?>" alt="<?= $sidebarItem['title']; ?>" title="Click to Enlarge"/></a>
							<?php if ($sidebarItem['description']) : ?> 
								<p class="media-item-description">
									<?= $sidebarItem['description']; ?> 
								</p>
							<?php endif; ?> 
						</div>
						<?php else : ?> 
							<p class="media-item-pdf-title"><a href="<?= LOCATION; ?><?= $sidebarItem['location']; ?>"><?= $sidebarItem['title']; ?></a></p>
							<?php if ($sidebarItem['description']) : ?> 
								<p class="media-item-pdf-description">
									<?= $sidebarItem['description']; ?> 
								</p>
							<?php endif; ?> 
						<?php endif; ?> 
						<div><a href="javascript:;" onclick="deleteMedia(this, '<?= $sidebarItem['id']; ?>');">Delete</a></div>
					</div>
					<?php endforeach; ?> 				

					<div class="articleimages-item-add-btn media-add-container">
						<input type="button" value="Add New Sidebar Item" onclick="addNewMedia(this, 'sidebar');" />
					</div>

				</div>

			</div>
		
		</div>
		
		<div class="edit-item collapsable">
			
			<div class="edit-item-media edit-item-title" onclick="toggleContainer(this);">
				<h3 class="edit-item-media-title">
					<img src="<?= LOCATION; ?>admin/images/icon-arrow-closed.gif" alt=""/> Media Player
				</h3>
			</div>
			
			<div class="mediaplayer-container edit-item-content" style="display: none;">
									
				<h4 class="mediaplayer-title">Video</h4>
				<p class="mediaplayer-description">
					Video content can be loaded into the media player by direct link or using the embed code generated by the video site (i.e. YouTube or Vimeo).
				</p>
				<div class="mediaplayer-item">
				
					<?php foreach ($blog->media('video') as $video): ?> 
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
						<div class="utilities"><a href="javascript:;" onclick="deleteMedia(this, '<?= $video['id']; ?>');">delete</a></div>
					</div>
					<?php endforeach; ?> 
					
					<div class="mediaplayer-item-add-btn media-add-container"<?= (count($blog->media('video')) > 0) ? ' style="display: none;"' : ''; ?>>
						<input type="button" value="Add New Video" onclick="addNewMedia(this, 'video');" />
					</div>
					
				</div>
				
				<h4 class="mediaplayer-title">Photo Slideshow</h4>
				<p class="mediaplayer-description">
					If you have a large number of related photos that pertain to the article, you can create a slideshow which will be loaded into the media player.
				</p>
				<div class="mediaplayer-item">

					<?php foreach ($blog->media('slideshow') as $slideshow) : ?> 
					<div id="slideshow-<?= $slideshow['id']; ?>">
						
						<h4><?= valid($slideshow['title']); ?></h4>
						<?php if ($slideshow['description']) : ?> 
						<p>
							<?= valid($slideshow['description']); ?> 
						</p>
						<?php endif; ?> 
						
						<?php $photos = get_slideshow_photos($slideshow['id']); ?> 
						<h5>Photos In Slideshow (<?= count($photos); ?>)</h5>
						
						<div>
							<input type="hidden" name="media[player][slideshow][<?= $slideshow['id']; ?>][id]" value="<?= $slideshow['id']; ?>"/>
							
							<?php foreach ($photos as $photo) : ?> 
							<div id="slideshow-photo-<?= $photo['id']; ?>">
								
								<div class="photozoom">
									<a href="<?= $photo['location']; ?>" id="photo-<?= $photo['id']; ?>" rel="slideshow" title="<?= alt(valid($photo['title']), valid($photo['description'])); ?>"><img src="<?= add_photo_info($photo['location'], 200, 200); ?>" alt="photo"/></a>
								</div>
								
								<h4><?= valid($photo['title']); ?></h4>
								<?php if ($photo['description']) : ?> 
								<p>
									<?= valid($photo['description']); ?> 
								</p>
								<?php endif; ?>
								
								<div class="utilities"><a href="javascript:;" onclick="deleteMedia(this, '<?= $photo['id']; ?>');">delete photo</a></div>
								
							</div>
							<?php endforeach; ?> 
							
							<div class="mediaplayer-item-add-btn media-add-container">
								<input type="button" value="Add New Slideshow Photo" onclick="addNewMedia(this, 'slideshow-photo');" />
							</div>
							
						</div>
						
						<div class="utilities"><a href="javascript:;" onclick="deleteMedia(this, '<?= $slideshow['id']; ?>');">delete entire slideshow</a></div>
						
					</div>
					<?php endforeach; ?> 
					
					<div class="mediaplayer-item-add-btn media-add-container"<?= (count($blog->media('slideshow')) > 0) ? ' style="display: none;"' : ''; ?>>
						<input type="button" value="Add New Slideshow" onclick="addNewMedia(this, 'slideshow');" />
					</div>
					
				</div>

				<h4 class="mediaplayer-title">Audio</h4>
				<p class="mediaplayer-description">
					If you have audio related to the article (such as audio of an interview), you can load it into the media player (.mp3 files with a file size less than 5mb only).
				</p>
				<div class="mediaplayer-item">		
                    <?php foreach($blog->media('audio') as $audio) : ?> 
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
						<div class="utilities"><a href="javascript:;" onclick="deleteMedia(this, '<?= $audio['id']; ?>');">delete</a></div>
					</div>
					<?php endforeach; ?> 

					<div class="mediaplayer-item-add-btn media-add-container"<?= (count($blog->media('audio')) > 0) ? ' style="display: none;"' : ''; ?>>
                        <input type="button" value="Add New Audio" onclick="addNewMedia(this, 'audio');" />
                    </div>
                </div>
				
			</div>
			
		</div><?php */?>

		<div class="edit-save">
			<?php if (!is_string($blog->published_at)) : ?> 
				<input type="submit" name="publish-continue" value="Publish and Continue Editing" class="btn-submit" /> 
				<input type="submit" name="publish" value="Publish" class="btn-submit" /> 
			<?php endif; ?> 
			<input type="submit" name="continue" value="Save and Continue Editing" class="btn-submit"/> 
			<input type="submit" name="save" id="submit" value="Save" class="btn-submit" /> 
			or <a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/" class="cancel">Cancel</a>
		</div>
	
	</form>
	
	<?php if ($blog->wasFound() && !is_string($blog->deleted_at)) : ?> 
	<div id="live-preview-container" style="margin-top: 30px;">
		
		<a href="javascript:;" onclick="$('#live-preview').toggle();">Toggle Live Preview</a>

		<object id="live-preview" type="text/html" data="<?= $blog->link(); ?>" style="height: 400px; width: 100%; display: none; margin-top: 10px">
			<a href="<?= $blog->link(); ?>">View actual blog page.</a>	
		</object>
		
	</div>		
	<?php endif; ?> 
