	
	<div id="page-content-header">

		<h2 class="page-content-title">Current Events</h2>
	
		<p class="page-content-actions">
			<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/events/add/">
				<img src="<?= LOCATION; ?>admin/images/btn-addnew.gif" alt="Add a New Event" />
			</a>
		</p>
	
	</div>
	
	<?php include is_plugin_file('events', 'admin/events-search'); ?> 
	
	<div id="page-content-list">
		
		<p class="page-content-list-header">
			<?php if (count_search_items() > 0) : ?> 
			<span class="currently-viewing">
				Currently Viewing: <?= pagination_viewing_start(); ?><?php if (count_search_items() > 1) : ?> - <?= pagination_viewing_end(); ?><?php endif; ?> of <?= count_search_items(); ?>  
			</span>
			<?php endif; ?>
			<span class="view-toggle">
				View: &nbsp;&nbsp;&nbsp;&nbsp;Browse &nbsp;|&nbsp; <a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/events/list/">List</a>
			</span>
		</p>
		
		<?php foreach (get_search_items() as $event) : ?>
			<?php include is_plugin_file('events', 'admin/events-resultview'); ?> 
		<?php endforeach; ?> 
	
		<?php if (count_search_items() < 1) : ?> 
		<p class="browse-noresults">No events were found using the criteria you have chosen. Try broadening or changing your search.</p>
		<?php endif; ?> 
	</div>
	
	<?= pagination('&laquo; Newer Events', 'Older Events &raquo;'); ?> 
	