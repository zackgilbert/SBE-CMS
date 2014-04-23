	
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
				View: &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/galleries/">Browse</a> &nbsp;|&nbsp; List
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
						<th>Description</th>
						<th># Photos</th>
						<th class="tools">Tools</th>
					</tr>
					<?php foreach (get_search_items() as $key => $gallery) : ?> 
					<tr id="gallery-<?= $gallery->id; ?>" class="row row-<?= ($key%2 == 0) ? 'even' : 'odd'; ?>">
	
						<td align="center" class="select"><input type="checkbox" value="<?= $gallery->id; ?>"/></td>
						<td>
							<?php if (is_string($gallery->deleted_at)) : ?> 
							Deleted
							<?php elseif (is_string($gallery->published_at)) : ?> 
							Published
							<?php else : ?> 
							Draft
							<?php endif; ?> 
						</td>
						<td>
							<?php if (is_string($gallery->deleted_at)) : ?> 
								<?= format_date($gallery->deleted_at, 'm/d/Y'); ?> 
							<?php elseif (is_string($gallery->published_at)) : ?> 
								<?= format_date($gallery->published_at, 'm/d/Y'); ?> 
							<?php else : ?> 
								<?= format_date($gallery->created_at, 'm/d/Y'); ?> 
							<?php endif; ?> 
						</td>
						<td><a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/galleries/<?= $gallery->id; ?>/?list"><?= $gallery->title(); ?></a></td> 
						<td><?= truncate($gallery->description(), 30); ?></td>
						<td align="center"><?= $gallery->photo_count(); ?></td>
	
						<td align="center" class="tools">
							<!--<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/galleries/<?= $gallery->id; ?>/" class="editlink">Edit</a>-->
							<a href="<?= $gallery->link(); ?>" class="viewlink">View</a> 
							<a href="<?= LOCATION; ?>plugins/galleries/admin/galleries-delete/?id=<?= $gallery->id; ?>" class="deletelink">Delete</a>
						</td>
	
					</tr>
					<?php endforeach; ?> 
					<?php if (count_search_items() < 1) : ?> 
					<tr>
						<td colspan="7">
							<p class="browse-noresults">No gallery albums were found using the criteria you have chosen. Try broadening or changing your search.</p>
						</td>
					</tr>
					<?php else : ?> 
					<tr class="selected-tools">
						<td colspan="7">
							Select: &nbsp;&nbsp;<a href="javascript:;" onclick="$('.page-content-list-table tr :checkbox').attr('checked', 'checked');">All</a> <a href="javascript:;" onclick="$('.page-content-list-table tr :checkbox').removeAttr('checked');">None</a>
							&nbsp;&nbsp;&nbsp;&nbsp;
							With Selected: &nbsp;&nbsp;<a href="javascript:;" onclick="massAction(this, 'galleries', 'publish', 'Are you sure you want to publish all selected albums?');">Publish</a> <a href="javascript:;" onclick="massAction(this, 'galleries', 'unpublish', 'Are you sure you want to un-publish all selected albums?');">Un-Publish</a> <?php if (request('status') == 'deleted') : ?><a href="javascript:;" onclick="massAction(this, 'galleries', 'undelete', 'Are you sure you want to un-delete all selected albums?');">Un-Delete</a><?php else : ?><a href="javascript:;" onclick="massAction(this, 'galleries', 'delete', 'Are you sure you want to delete all selected albums?');">Delete</a><?php endif; ?> 
						</td>
					</tr>
					<?php endif; ?> 
				</table>
				
				<script type="text/javascript">
				
				$(function() {
					$('.page-content-list-table tr:not(.selected-tools) td:not(.select, .tools)').bind('click', function() {
						var id = $(this).parent().attr('id').substr($(this).parent().attr('id').indexOf('-')+1);
						window.location.href = root+'/admin/pages/<?= get_var('id'); ?>/galleries/'+id+'/?list';
					});
				});
				
				</script>
				
			</form>
		</div>
		
	</div>
	
	<?= pagination('&laquo; Newer Albums', 'Older Albums &raquo;'); ?> 
	