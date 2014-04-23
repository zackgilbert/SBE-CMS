		
	<div id="content-columnleft">
				
		<?php if (get_var('category')) : ?> 
			
			<h2 class="browse-title">
				Posts made in: '<?= get_var('category'); ?>' (<?= count_search_items(); ?>)
				<?php if (count_search_items() > 0) : ?> 
				<span class="browse-viewing">
					Currently Viewing: <?= pagination_viewing_start(); ?> - <?= pagination_viewing_end(); ?> of <?= count_search_items(); ?> 
				</span>
				<?php endif; ?>
			</h2>			 
			
		<?php elseif (get_var('date')) : ?> 

			<h2 class="browse-title">
				Posts made in: <?= format_date(get_var('date'), 'F, Y'); ?>	(<?= count_search_items(); ?>)		
				<?php if (count_search_items() > 0) : ?> 
				<span class="browse-viewing">
					Currently Viewing: <?= pagination_viewing_start(); ?> - <?= pagination_viewing_end(); ?> of <?= count_search_items(); ?> 
				</span>
				<?php endif; ?>
			</h2> 
			
		<?php elseif (get('keywords')) : ?> 

			<h2 class="browse-title">
				Search Results for: "<?= get('keywords'); ?>" (<?= count_search_items(); ?>)
				<?php if (count_search_items() > 0) : ?> 
				<span class="browse-viewing">
					Currently Viewing: <?= pagination_viewing_start(); ?> - <?= pagination_viewing_end(); ?> of <?= count_search_items(); ?> 
				</span>
				<?php endif; ?>
			</h2> 
			
		<?php endif; ?> 
		
		<?php foreach (get_search_items() as $blog) : ?>
			<?php load_include('blogs-indexview', array('blog' => $blog)); ?>
		<?php endforeach; ?> 
		
		<?php if (count_search_items() < 1) : ?> 
			<p class="browse-noresults">No blog posts were found using the criteria you have chosen. Try broadening or changing your search.</p>
		<?php endif; ?> 
		
		<?php if (pagination_page_count() > 1) : ?> 
		<div class="pagination-container">
			<ul class="pagination">
				<li class="previous"><?= pagination_prev('&laquo; Newer Posts'); ?></li>
				<?php while (pagination_has_pages()) : ?> 
					<?php if (pagination_page_is_current()) : ?> 

						<li class="current"><?= pagination_page_number(); ?></li>

					<?php elseif (pagination_page_is_separator()) : ?> 

						<li class="separator"> ... </li>

					<?php else : ?> 

						<li class="page"><a href="<?= pagination_page_link(); ?>"><?= pagination_page_number(); ?></a></li>

					<?php endif; ?> 
				<?php endwhile; ?> 			
				<li class="next"><?= pagination_next('Older Posts &raquo;'); ?></li>
			</ul>
		</div>
		<?php endif; ?> 
		
		<?php if (count_search_items() > 0) : ?> 
		<div id="blog-archives">
			<h4 class="blog-archives-title">Archives</h4>
			<?php foreach (blog_archive_dates() as $year => $months) : ?> 
			<dl>
				<dt><?= $year; ?></dt>
					<dd><?= join(", ", array_reverse($months)); ?></dd>
			</dl>			
			<?php endforeach; ?> 
		</div>
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
		
		<?php /*?><?php load_include('blogs-browse-sidebar'); ?> <?php */?>
	
		<?php /*?><?php if (blog_setting('about')) : ?> 
		<div id="blog-about">
			<h4 class="blog-about-title">About this blog</h4>
			<p><?= blog_setting('about'); ?></p>
		</div>	
		<?php endif; ?> <?php */?>
		
		<?php /*?><?php if ($authors = blog_authors()) : ?> 
		<div id="blog-authors">
			<h4 class="blog-authors-title">Author(s)</h4>
			<?php foreach ($authors as $author) : ?> 
			<div class="blog-author-item">
				<img src="<?= $author->thumb(70, 70); ?>" class="author-pic" alt="" />
				<h5 class="author-name"><?= $author->name(); ?></h5>
				<p class="author-description"><?= $author->bio(); ?></p>
				<p class="author-contact"><?= $author->contact(); ?></p>	
			</div>
			<?php endforeach; ?> 
		</div>	
		<?php endif; ?><?php */?> 
		
		<?php /*?><!-- Are topics the same as categories? Have we figured out what these are going to be? -->
		<?php if (blog_has_categories()) : ?> 
		<div id="blog-topics">
			<h4 class="blog-topics-title">Topics</h4>
			<p class="topics-list"><?= link_to_blog_categories(); ?></p>
		</div>
		<?php endif; ?> <?php */?>		
		
		<?php /*<div id="blog-recentcomments">
			<h4 class="blog-recentcomments-title">Recent Comments</h4>
			<?php foreach (section_comments(5) as $comment) : ?> 
			<div class="recentcomments-item">
				<!-- SHOULD AUTHOR HAVE JUST NAME? OR THEIR WEBSITE AS WELL? -->
				<h5 class="recentcomments-author"><?= $comment->authorLink(); //$comment->name; ?> said:</h5>
				<p class="recentcomments-body">
					<?= valid(truncate($comment->comment, 100)); ?> 
				</p>
				<p class="recentcomments-post">
			  		about <a href="<?= $comment->parent('link'); ?>"><?= $comment->parent('title'); ?></a>
				</p>	
			</div>	
			<?php endforeach; ?> 
 		</div>*/ ?>
		
	</div>
