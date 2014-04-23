	
	<div id="page-content-header">

		<h2 class="page-content-title">Current Albums</h2>
	
		<p class="page-content-actions">
			<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/galleries/add/">
				<img src="<?= LOCATION; ?>admin/images/btn-addnewalbum.gif" alt="Add a New Album" />
			</a>
		</p>
	
	</div>
	
	<?php load_include('pages-content-search'); ?> 
	
	<div id="page-content-list">
		
		<p class="page-content-list-header">
			<?php if (count_search_items() > 0) : ?> 
			<span class="currently-viewing">
				Currently Viewing: <?= pagination_viewing_start(); ?><?php if (count_search_items() > 1) : ?> - <?= pagination_viewing_end(); ?><?php endif; ?> of <?= count_search_items(); ?>  
			</span>
			<?php endif; ?>
			<span class="view-toggle">
				View: &nbsp;&nbsp;&nbsp;&nbsp;Browse &nbsp;|&nbsp; <a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/galleries/list/">List</a>
			</span>
		</p>
		
		<?php foreach (get_search_items() as $gallery) : ?>
			<?php include is_plugin_file('galleries', 'admin/galleries-resultview'); ?> 
		<?php endforeach; ?> 
	
		<?php if (count_search_items() < 1) : ?> 
		<p class="browse-noresults">No gallery albums were found using the criteria you have chosen. Try broadening or changing your search.</p>
		<?php endif; ?> 
	</div>
	
	<?= pagination('&laquo; Newer Albums', 'Older Albums &raquo;'); ?> 
	