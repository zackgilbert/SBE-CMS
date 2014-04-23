	
	<div id="page-content-header">

		<h2 class="page-content-title">Current Blog Posts</h2>
	
		<p class="page-content-actions">
			<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/blogs/add/">
				<img src="<?= LOCATION; ?>admin/images/btn-addnewpost.gif" alt="Add a New Post" />
			</a>
		</p>
	
	</div>
	
	<?php include is_plugin_file('blogs', 'admin/blogs-search'); ?> 
	
	<div id="page-content-list">
		
		<p class="page-content-list-header">
			<?php if (count_search_items() > 0) : ?> 
			<span class="currently-viewing">
				Currently Viewing: <?= pagination_viewing_start(); ?><?php if (count_search_items() > 1) : ?> - <?= pagination_viewing_end(); ?><?php endif; ?> of <?= count_search_items(); ?>  
			</span>
			<?php endif; ?>
			<span class="view-toggle">
				View: &nbsp;&nbsp;&nbsp;&nbsp;Browse &nbsp;|&nbsp; <a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/blogs/list/">List</a>
			</span>
		</p>
		
		<?php foreach (get_search_items() as $blog) : ?>
			<?php include is_plugin_file('blogs', 'admin/blogs-resultview'); ?> 
		<?php endforeach; ?> 
	
		<?php if (count_search_items() < 1) : ?> 
		<p class="browse-noresults">No blog posts were found using the criteria you have chosen. Try broadening or changing your search.</p>
		<?php endif; ?> 
	</div>
	
	<?= pagination('&laquo; Newer Posts', 'Older Posts &raquo;'); ?> 
	