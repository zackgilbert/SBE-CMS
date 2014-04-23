	
	<div id="page-content-header">

		<h2 class="page-content-title">Current Bands</h2>
	
		<p class="page-content-actions">
			<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/bands/add/">
				<img src="<?= LOCATION; ?>admin/images/btn-addnewband.gif" alt="Add a New Band" />
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
				View: &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/bands/">Browse</a> &nbsp;|&nbsp; List
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
						<th>Tagline</th>
						<th class="tools">Tools</th>
					</tr>
					<?php foreach (get_search_items() as $key => $band) : ?> 
					<tr id="band-<?= $band->id; ?>" class="row row-<?= ($key%2 == 0) ? 'even' : 'odd'; ?>">
	
						<td align="center" class="select"><input type="checkbox" value="<?= $band->id; ?>"/></td>
						<td>
							<?php if (is_string($band->deleted_at)) : ?> 
							Deleted
							<?php elseif (is_string($band->published_at)) : ?> 
							Published
							<?php else : ?> 
							Draft
							<?php endif; ?> 
						</td>
						<td>
							<?php if (is_string($band->deleted_at)) : ?> 
								<?= format_date($band->deleted_at, 'm/d/Y'); ?> 
							<?php elseif (is_string($band->published_at)) : ?> 
								<?= format_date($band->published_at, 'm/d/Y'); ?> 
							<?php else : ?> 
								<?= format_date($band->created_at, 'm/d/Y'); ?> 
							<?php endif; ?> 
						</td>
						<td><a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/bands/<?= $band->id; ?>/?list"><?= valid(truncate($band->name, 30)); ?></a></td> 
						<td><?= $band->tagline(); ?></td>
	
						<td align="center" class="tools">
							<!--<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/bands/<?= $band->id; ?>/" class="editlink">Edit</a>-->
							<a href="<?= $band->link(); ?>" class="viewlink">View</a> 
							<a href="<?= LOCATION; ?>plugins/bands/admin/bands-delete/?id=<?= $band->id; ?>" class="deletelink">Delete</a>
						</td>
	
					</tr>
					<?php endforeach; ?> 
					<?php if (count_search_items() < 1) : ?> 
					<tr>
						<td colspan="6">
							<p class="browse-noresults">No bands were found using the criteria you have chosen. Try broadening or changing your search.</p>
						</td>
					</tr>
					<?php else : ?> 
					<tr class="selected-tools">
						<td colspan="6">
							Select: &nbsp;&nbsp;<a href="javascript:;" onclick="$('.page-content-list-table tr :checkbox').attr('checked', 'checked');">All</a> <a href="javascript:;" onclick="$('.page-content-list-table tr :checkbox').removeAttr('checked');">None</a>
							&nbsp;&nbsp;&nbsp;&nbsp;
							With Selected: &nbsp;&nbsp;<a href="javascript:;" onclick="massAction(this, 'bands', 'publish', 'Are you sure you want to publish all selected bands?');">Publish</a> <a href="javascript:;" onclick="massAction(this, 'bands', 'unpublish', 'Are you sure you want to un-publish all selected bands?');">Un-Publish</a> <?php if (request('status') == 'deleted') : ?><a href="javascript:;" onclick="massAction(this, 'bands', 'undelete', 'Are you sure you want to un-delete all selected bands?');">Un-Delete</a><?php else : ?><a href="javascript:;" onclick="massAction(this, 'bands', 'delete', 'Are you sure you want to delete all selected bands?');">Delete</a><?php endif; ?> 
						</td>
					</tr>
					<?php endif; ?> 
				</table>
				
				<script type="text/javascript">
				
				$(function() {
					$('.page-content-list-table tr:not(.selected-tools) td:not(.select, .tools)').bind('click', function() {
						var id = $(this).parent().attr('id').substr($(this).parent().attr('id').indexOf('-')+1);
						window.location.href = root+'/admin/pages/<?= get_var('id'); ?>/bands/'+id+'/?list';
					});
				});
				
				</script>
				
			</form>
		</div>
		
	</div>
	
	<?= pagination('&laquo; Newer Bands', 'Older Bands &raquo;'); ?> 
	