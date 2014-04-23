	
	<div id="content">

		<div id="content-header">
			<p class="header-section">Page Structure</p>	
			<h2 class="header-title">
				View And Edit Your Pages 				
				<?php if (user_is_admin()) : ?> 
				<span style="utilities">
					<a href="<?= LOCATION; ?>admin/includes/pages-add/" class="add-page"><img src="<?= LOCATION; ?>admin/images/btn-addpage.gif" alt="Add New Page" /></a> 
					<img src="<?= LOCATION; ?>admin/images/btn-reorderpages.gif" alt="Reorder Pages" onclick="toggleSortable(this);" />
				</span>
				<?php endif; ?> 
			</h2>
		</div>
		
		<div id="content-1col">

			<div id="page-0">
				<ul class="sitemap">
					<?php foreach (get_sitemap_roots() as $root) : ?> 
						<?php load_include('pages-resultview', array('section' => $root)); ?> 
					<?php endforeach; ?> 
				</ul>
			</div>
			
		</div>

	</div>
	
	<script type="text/javascript">
		$(function() {
			$('ul.sitemap li:even').addClass('row-even');
			$('ul.sitemap li:odd').addClass('row-odd');
		});
	</script>
