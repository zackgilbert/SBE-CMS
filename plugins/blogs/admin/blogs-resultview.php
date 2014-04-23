	
	<div id="blog-<?= $blog->id; ?>" class="page-content-resultview blogposts-resultview">
		
		<h3 class="resultview-title">
			<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/blogs/<?= $blog->id; ?>/"><?= valid($blog->title); ?></a> 
		</h3>
		<p class="resultview-byline">
			<?= $blog->author('By '); ?> 
		</p>	
		<p class="resultview-body">
			<?= truncate(strip_tags($blog->body), 50, true); ?> 
		</p>
		<p class="resultview-stats">
			Created on: <?= format_date($blog->created_at, 'm/d/Y \a\t g:ia'); ?> 
			<?php if ($blog->published_at) : ?> 
			&nbsp;&nbsp; Published on: <?= format_date($blog->published_at, 'm/d/Y \a\t g:ia'); ?> 
			<?php else : ?> 
			&nbsp;&nbsp; Not Yet Published
			<?php endif; ?> 
		</p>
		
		<div class="resultview-tools">
			<a href="<?= $blog->link(); ?>" class="viewlink">View</a> <a href="<?= LOCATION; ?>plugins/blogs/admin/blogs-delete/?id=<?= $blog->id; ?>" class="deletelink">Delete</a>
		</div>
		
	</div>
	