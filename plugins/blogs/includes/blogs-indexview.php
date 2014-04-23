
	<p class="blog-date"><?= format_date($blog->published_at, 'F j, Y'); ?></p>
	
	<h2 class="blog-headline"><a href="<?= $blog->link(); ?>"><?= $blog->title(); ?></a></h2>	
	
	<p class="blog-byline">
		<?= $blog->author('By '); ?> 
		<?php if ($blog->show_comments()) : ?> 
		<span class="byline-comments"><a href="<?= $blog->link(); ?>#comments">Comments (<?= count($blog->comments()); ?>)</a></span>
		<?php endif; ?> 
	</p>

	<div class="blog-content">
		
		<div class="blog-body">
			<?= format_text($blog->excerpt(100, true)); ?> 
			<span class="readmore"><a href="<?= $blog->link(); ?>">Read more</a></span>
		</div>
		
	</div>
