	
	<div class="browse-sidebar">
		
		<form method="get" action="<?= get_sitemap_section_url(); ?>browse/">
			
			<h3 class="browse-sidebar-title">Browse Galleries</h3>
			
			<div class="browse-sidebar-keyword">
				<label for="browse-gallery-keyword">By Keyword</label>
				<input type="text" name="keywords" value="<?= get('keywords'); ?>" id="browse-gallery-keyword" class="field-medium" />
			</div>
								
			<div class="browse-sidebar-submit">
				<input type="submit" value="Browse Galleries" class="submit"/>
			</div>
			
		</form>
		
	</div>
	