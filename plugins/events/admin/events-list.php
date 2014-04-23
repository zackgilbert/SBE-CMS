	
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
				View: &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/events/">Browse</a> &nbsp;|&nbsp; List
			</span>
		</p>		
		
		<div>
			<form method="post" action="">
				<table class="page-content-list-table" cellpadding="0" cellspacing="0">
					<tr class="header">
						<th class="select">Select</th>
						<th class="status">Status</th>
						<th class="date">Date</th>
						<th>Name</th>
						<?php /*?><th>Category</th>*/?>
						<th class="tools">Tools</th>
					</tr>
					<?php foreach (get_search_items() as $key => $event) : ?> 
					<tr id="event-<?= $event->id; ?>" class="row row-<?= ($key%2 == 0) ? 'even' : 'odd'; ?>">
	
						<td align="center" class="select"><input type="checkbox" value="<?= $event->id; ?>"/></td>
						<td>
							<?php if (is_string($event->deleted_at)) : ?> 
							Deleted
							<?php elseif (is_string($event->published_at)) : ?> 
							Published
							<?php else : ?> 
							Draft
							<?php endif; ?> 
						</td>
						<td>
							<?php if (is_string($event->deleted_at)) : ?> 
								<?= format_date($event->deleted_at, 'm/d/Y'); ?> 
							<?php elseif (is_string($event->published_at)) : ?> 
								<?= format_date($event->published_at, 'm/d/Y'); ?> 
							<?php else : ?> 
								<?= format_date($event->created_at, 'm/d/Y'); ?> 
							<?php endif; ?> 
						</td>
						<td><a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/events/<?= $event->id; ?>/?list"><?= $event->name(); ?></a></td> 
						<?php /*?><td><?= $event->type('name'); ?></td>*/?>
	
						<td align="center" class="tools">
							<a href="<?= $event->link(); ?>" class="viewlink">View</a> 
							<a href="<?= LOCATION; ?>plugins/events/admin/events-delete/?id=<?= $event->id; ?>" class="deletelink">Delete</a>
						</td>
	
					</tr>
					<?php endforeach; ?> 
					<?php if (count_search_items() < 1) : ?> 
					<tr>
						<td colspan="5">
							<p class="browse-noresults">No events were found using the criteria you have chosen. Try broadening or changing your search.</p>
						</td>
					</tr>
					<?php else : ?> 
					<tr class="selected-tools">
						<td colspan="5">
							Select: &nbsp;&nbsp;<a href="javascript:;" onclick="$('.page-content-list-table tr :checkbox').attr('checked', 'checked');">All</a> <a href="javascript:;" onclick="$('.page-content-list-table tr :checkbox').removeAttr('checked');">None</a>
							&nbsp;&nbsp;&nbsp;&nbsp;
							With Selected: &nbsp;&nbsp;<a href="javascript:;" onclick="massAction(this, 'events', 'publish', 'Are you sure you want to publish all selected items?');">Publish</a> <a href="javascript:;" onclick="massAction(this, 'events', 'unpublish', 'Are you sure you want to un-publish all selected items?');">Un-Publish</a> <?php if (request('status') == 'deleted') : ?><a href="javascript:;" onclick="massAction(this, 'events', 'undelete', 'Are you sure you want to un-delete all selected items?');">Un-Delete</a><?php else : ?><a href="javascript:;" onclick="massAction(this, 'events', 'delete', 'Are you sure you want to delete all selected items?');">Delete</a><?php endif; ?> 
						</td>
					</tr>
					<?php endif; ?> 
				</table>
				
				<script type="text/javascript">
				
				$(function() {
					$('.page-content-list-table tr:not(.selected-tools) td:not(.select, .tools)').bind('click', function() {
						var id = $(this).parent().attr('id').substr($(this).parent().attr('id').indexOf('-')+1);
						window.location.href = root+'admin/pages/<?= get_var('id'); ?>/events/'+id+'/?list';
					});
				});
				
				</script>
				
			</form>
		</div>
		
	</div>
	
	<?= pagination('&laquo; Newer Events', 'Older Events &raquo;'); ?> 
