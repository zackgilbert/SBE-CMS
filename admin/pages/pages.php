	
	<div id="content">

		<div id="content-header">
			<p class="header-section">Page Structure</p>	
			<h2 class="header-title">
				Manage Your Pages 				
				<?php if (user_is_admin()) : ?> 
				<span style="utilities">
					<a href="<?= LOCATION; ?>admin/includes/pages-add/" class="add-page"><img src="<?= LOCATION; ?>admin/images/btn-addpage.gif" alt="Add New Page" /></a> 
					<img src="<?= LOCATION; ?>admin/images/btn-reorderpages.gif" alt="Reorder Pages" onclick="toggleSortable(this);" />
				</span>
				<?php endif; ?> 
			</h2>
		</div>
		
		<div id="sitemap-container" class="sitemap">

			<div id="page-0">
				<ul class="col<?= count(get_sitemap_roots())-1; ?>">
					<?php foreach (get_sitemap_roots() as $root) : ?> 
						<?php load_include('pages-slickmap', array('section' => $root)); ?> 
					<?php endforeach; ?> 
				</ul>
			</div>
			
		</div>
		
		<p class="legend">
			* Pages in your sitemap in gray are hidden from your visitors, but you can still access and edit them here in the admin. If you'd like to make a page visible, click on the page and go to the <em>Hide/Delete Page</em> tab.
		</p>

	</div>
