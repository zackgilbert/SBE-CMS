
	<div id="content">

		<div id="content-header">
			<p class="header-section">Page Structure</p>	
			<h2 class="header-title">
				Manage Your Pages 				
				<?php if (user_is_admin()) : ?> 
				<span style="utilities">
					<!--<a href="<?= LOCATION; ?>admin/includes/pages-add/" class="add-page"><img src="<?= LOCATION; ?>admin/images/btn-addpage.gif" alt="Add New Page" /></a> -->
					<a href="<?= LOCATION;?>admin/pages/"><img src="<?= LOCATION; ?>admin/images/btn-saveorder.gif" alt="Done Reordering Pages" /></a>
				</span>
				<?php endif; ?> 
			</h2>
		</div>
		
		<?php if (user_is_admin()) : ?> 
		
		<div id="sitemap-container">
			<ul id="sitemap">
			<?php foreach (get_sitemap_roots() as $root) : ?> 
				<?php load_include('pages-reorder', array('section' => $root)); ?> 
			<?php endforeach; ?> 
			</ul>			
		</div>
		
		<div id="sitemap-instructions">
			<em>Hint: Use Ctrl+z to undo a mistake!</em>
		</div>
		
		<?php else : ?> 
			
		<div>
			Sorry, but you're not an Admin level user, so you don't have access to this page and it's functionalities.
		</div>
			
		<?php endif; ?>

	</div>