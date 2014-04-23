	
	<div class="print-header">
		<img src="<?= LOCATION; ?>images/logo-city-printversion.gif" alt="City Newspaper" />	
	</div>
	
	<h2 class="blog-headline"><?= $blog->title(); ?></h2>
	
	<p class="blog-byline">
		<?= link_to_authors($blog->author_ids, "By "); ?> on <?= format_date($blog->published_at, 'F j, Y'); ?> 
	</p>

	<div id="blog-content">
			
		<div id="blog-body">
	
			<?= $blog->body(); ?> 
		
		</div>
		
	</div>
	