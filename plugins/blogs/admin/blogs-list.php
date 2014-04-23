	
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
				View: &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/blogs/">Browse</a> &nbsp;|&nbsp; List
			</span>
		</p>		
		
		<div>
			<form method="post" action="">
				<table class="page-content-list-table" cellpadding="0" cellspacing="0">
					<tr class="header">
						<th class="select">Select</th>
						<th class="status">Status</th>
						<th class="date">Date</th>
						<th>Title</th>
						<th>Author</th>
						<th class="tools">Tools</th>
					</tr>
					<?php foreach (get_search_items() as $key => $blog) : ?> 
					<tr id="blog-<?= $blog->id; ?>" class="row row-<?= ($key%2 == 0) ? 'even' : 'odd'; ?>">
	
						<td align="center" class="select"><input type="checkbox" value="<?= $blog->id; ?>"/></td>
						<td>
							<?php if (is_string($blog->deleted_at)) : ?> 
							Deleted
							<?php elseif (is_string($blog->published_at)) : ?> 
							Published
							<?php else : ?> 
							Draft
							<?php endif; ?> 
						</td>
						<td>
							<?php if (is_string($blog->deleted_at)) : ?> 
								<?= format_date($blog->deleted_at, 'm/d/Y'); ?> 
							<?php elseif (is_string($blog->published_at)) : ?> 
								<?= format_date($blog->published_at, 'm/d/Y'); ?> 
							<?php else : ?> 
								<?= format_date($blog->created_at, 'm/d/Y'); ?> 
							<?php endif; ?> 
						</td>
						<td><a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/blogs/<?= $blog->id; ?>/?list"><?= valid(truncate($blog->title, 30)); ?></a></td> 
						<td><?= $blog->author('', false); ?></td>
	
						<td align="center" class="tools">
							<!--<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/blogs/<?= $blog->id; ?>/" class="editlink">Edit</a>-->
							<a href="<?= $blog->link(); ?>" class="viewlink">View</a> 
							<a href="<?= LOCATION; ?>plugins/blogs/admin/blogs-delete/?id=<?= $blog->id; ?>" class="deletelink">Delete</a>
						</td>
	
					</tr>
					<?php endforeach; ?> 
					<?php if (count_search_items() < 1) : ?> 
					<tr>
						<td colspan="6">
							<p class="browse-noresults">No blog posts were found using the criteria you have chosen. Try broadening or changing your search.</p>
						</td>
					</tr>
					<?php else : ?> 
					<tr class="selected-tools">
						<td colspan="6">
							Select: &nbsp;&nbsp;<a href="javascript:;" onclick="$('.page-content-list-table tr :checkbox').attr('checked', 'checked');">All</a> <a href="javascript:;" onclick="$('.page-content-list-table tr :checkbox').removeAttr('checked');">None</a>
							&nbsp;&nbsp;&nbsp;&nbsp;
							With Selected: &nbsp;&nbsp;<a href="javascript:;" onclick="massAction(this, 'blogs', 'publish', 'Are you sure you want to publish all selected items?');">Publish</a> <a href="javascript:;" onclick="massAction(this, 'blogs', 'unpublish', 'Are you sure you want to un-publish all selected items?');">Un-Publish</a> <?php if (request('status') == 'deleted') : ?><a href="javascript:;" onclick="massAction(this, 'blogs', 'undelete', 'Are you sure you want to un-delete all selected items?');">Un-Delete</a><?php else : ?><a href="javascript:;" onclick="massAction(this, 'blogs', 'delete', 'Are you sure you want to delete all selected items?');">Delete</a><?php endif; ?> 
						</td>
					</tr>
					<?php endif; ?> 
				</table>
				
				<script type="text/javascript">
				
				$(function() {
					$('.page-content-list-table tr:not(.selected-tools) td:not(.select, .tools)').bind('click', function() {
						var id = $(this).parent().attr('id').substr($(this).parent().attr('id').indexOf('-')+1);
						window.location.href = root+'/admin/pages/<?= get_var('id'); ?>/blogs/'+id+'/?list';
					});
				});
				
				</script>
				
			</form>
		</div>
		
	</div>
	
	<?= pagination('&laquo; Newer Posts', 'Older Posts &raquo;'); ?> 
