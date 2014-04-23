	
	<div class="blog-browse-sidebar">
		
		<form method="get" action="<?= section_link(); ?>">
		
			<div>
				<label for="browse-blog-keyword">Search this Blog</label>
				<input type="text" name="keywords" id="browse-blog-keyword" value="<?= get('keywords'); ?>" class="field-small" />
				<input type="submit" value="Search" class="submit"/>
			</div>
			
		</form>
		
	</div>
	