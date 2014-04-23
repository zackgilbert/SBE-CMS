	
	<div id="blog-<?= $blog->id; ?>" class="blog-resultview">
	
		<p class="blog-resultview-section">
			<?= valid(section('name', $blog->section())); ?>
		</p>		
		<h3 class="blog-resultview-title">
			<a href="<?= $blog->link(); ?>"><?= $blog->title(); ?></a> 
		</h3>
		<p class="blog-resultview-byline">
			<?= $blog->author('By '); ?> on <?= format_date($blog->published_at, "F jS, Y"); ?> 
		</p>	
		<p class="blog-resultview-body">
			<?= truncate(strip_tags($blog->body), 50, true); ?> 
		</p>
			
	</div>
