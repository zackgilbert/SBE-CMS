	
	<div id="content-columnleft">
		
		<p class="return-to-section">
			<a href="<?= section_link(); ?>">Back to all Blog Posts</a>
		</p>
		
		<p class="blog-date"><?= format_date($blog->published_at, 'F j, Y \a\t g:ia'); ?></p>
		
		<h2 class="blog-headline"><?= $blog->title(); ?></h2>	
		
		<p class="blog-byline">
			<?= $blog->author('By '); ?> 
			<?php if ($blog->show_comments()) : ?> 
			<span class="byline-comments"><a href="#comments">Comments (<?= count($blog->comments()); ?>)</a></span>
			<?php endif; ?>	
		</p>
		
		<?php if ($blog->banner()) : ?> 
		<div id="blog-mainphoto">
			<img src="<?= $blog->banner('image'); ?>" alt="" />
			<?php if ($blog->banner('caption')) : ?> 
			<p class="blog-mainphoto-caption">
				<?= $blog->banner('caption'); ?>
			</p>
			<?php endif; ?> 
		</div>
		<?php endif; ?>
	
		<div class="blog-content">
		
			<?php /*<div id="blog-content-mediaplayer">
				<?= load_mediaplayer($blog); ?> 
			</div>
		
			<div id="blog-content-sidebar">
				
				<div id="content-sidebar-tools">
					<div class="tools-recommend">
					<?php if (already_voted($blog->table, $blog->id)) : ?> 
						<img src="<?= LOCATION; ?>images/btn-recommended.gif" alt="Recommended" /><br/>
					<?php else : ?> 
						<a href="javascript:;" onclick="recommend(this, '<?= $blog->table; ?>', '<?= $blog->id; ?>');"><img src="<?= LOCATION; ?>images/btn-recommend-post.gif" alt="Recommend Blog Post" /></a><br />
					<?php endif; ?> 
						Total Recommendations (<span><?= vote_count($blog->table, $blog->id); ?></span>)
				 	</div>
					<div class="tools-comments">
						<a href="#comment-form">Leave A Comment</a><br />			 
						<span class="comments-read"><a href="#comments">Read Comments (<?= count($blog->comments()); ?>)</a></span>					
					</div>
					<div class="tools-share">
						<a href="javascript:;" onclick="return clipIt(this, '<?= $blog->table; ?>', '<?= $blog->id; ?>', '<?= user('id'); ?>');">
							<img src="<?= LOCATION; ?>images/icon-article-clip.gif" alt="Clip Article" title="Clip this Blog Post to your Profile" /> 
						</a>
						<a href="<?= rtrim($_SERVER['REQUEST_URI'], '/'); ?>/email/?id=<?= $blog->id; ?>" class="TellAFriend">
							<img src="<?= LOCATION; ?>images/icon-article-email.gif" alt="Email Blog Post" title="Email this Blog Post to a Friend" /> 
						</a>
						<a href="<?= rtrim($_SERVER['REQUEST_URI'], '/'); ?>/print/">
							<img src="<?= LOCATION; ?>images/icon-article-print.gif" alt="Print Blog Post" title="Print this Blog Post" /> 
						</a>
						<a href="javascript:;" onmouseover="share(this, 'http://<?= $_SERVER['SERVER_NAME'] . $blog->link(); ?>', '<?= urlencode($blog->title()); ?>', 2000);">
							<img src="<?= LOCATION; ?>images/icon-article-share.gif" alt="Share Blog Post" title="Share this Blog Post" /> 
						</a>
						
					</div>
				</div>
				
				<?php if ($blog->hasSidebarContent()) : ?> 
				<div id="content-sidebar-media">
					<?php foreach ($blog->media('photo') as $photo) : ?>
					<div class="sidebar-media-item photozoom">
						<p class="media-item-title"><?= $photo['title']; ?></p>
						<a href="<?= $photo['location']; ?>" id="sidebar-media-<?= $photo['id']; ?>" rel="media-item" title="<?= alt($photo['title'], $photo['description']); ?>"><img src="<?= add_photo_info($photo['location'], 195); ?>" alt="<?= $photo['title']; ?>" title="Click to Enlarge"/> </a>
						<?php if (is_string($photo['description']) && !empty($photo['description'])) : ?> 
						<p class="media-item-description">
							<?= valid($photo['description']); ?> 
						</p>
						<?php endif; ?> 
				 	</div>
					<?php endforeach; ?> 
					<?php foreach ($blog->media('file') as $file) : ?> 
					<div class="sidebar-media-item-pdf">
						<p class="media-item-pdf-title"><a href="<?= $file['location']; ?>"><?= $file['title']; ?></a></p>
						<?php if (is_string($file['description']) && !empty($file['description'])) : ?> 
						<p class="media-item-pdf-description">
							<?= valid($file['description']); ?> 
						</p>
						<?php endif; ?>
				 	</div>
					<?php endforeach; ?> 
				</div>
				<?php endif; ?>
			
			</div>*/ ?>
			
			<div class="blog-body">
				<?= $blog->body(); ?>
			</div>
			
		</div>
		
		<div id="blog-archives">
			<h4 class="blog-archives-title">Archives</h4>
			<?php foreach (blog_archive_dates() as $year => $months) : ?> 
			<dl>
				<dt><?= $year; ?></dt>
					<dd><?= join(", ", array_reverse($months)); ?></dd>
			</dl>			
			<?php endforeach; ?> 
		</div>
		
		<?php if ($blog->show_comments()) : ?> 
			<?php if (!$blog->comment_status('closed')) : ?> 

		<div id="comments" class="content-comments">
			<h3 class="comment-title">Comments for "<?= $blog->title(); ?>" (<?= count($blog->comments()); ?>)</h3>
			<p class="comment-disclaimer">
				The owners of this site are not responsible for the content of these comments. They reserve the right to remove comments at their discretion.
			</p>
			
			<?php comments($blog->table, $blog->id); ?>	
			
			<?php comments_form($blog->table, $blog->id, get_var('level_id')); ?>
		</div>		
		
			<?php else : ?> 
				
		<div id="comments" class="content-comments">
			<h3 id="comment-form" class="comment-title">Comments for "<?= $blog->title(); ?>"</h3>
			<p class="comment-disclaimer">
				Comments for this news post are currently closed.
			</p>
		</div>
		
			<?php endif; ?> 
		<?php endif; ?> 
		
	</div>
	
	<div id="content-columnright"> 
	
		<?php foreach (call_plugin_func('bands', 'get_bands') as $b) : ?> 
		<div id="band-<?= $b->id; ?>" class="band-sidebar">			
			<div class="band-logo">
				<a href="<?= $b->link(); ?>"><img src="<?= $b->thumb(175, 105); ?>" alt="<?= $b->name(); ?>"/></a>
			</div>
			<p class="band-tagline"><?= $b->tagline(); ?></p>			
		</div>		
		<?php endforeach; ?> 
		
	</div>