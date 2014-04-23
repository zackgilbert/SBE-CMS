	
	<div id="content">
		
		<div id="content-header">
			<p class="header-section">Site Users</p>	
			<h2 class="header-title">
				Browse and Edit Users 				
			</h2>
		</div>

		<div id="content-2colR-left">
		
			<?= load_include('users-sidebar.php'); ?> 
			
		</div>
		
		<div id="content-2colR-right">
		
			<h2 class="content-title">All Users <?= capitalize(get_var('subsection')); ?> (<?= count_search_items(); ?> found)</h2>
	
			<?php pagination(); ?> 
	
			<div class="content-users-addnew">
				<a href="<?= LOCATION; ?>admin/users/add/"><img src="../images/btn-addnew-user.gif" alt="Add New User" title="Add a New User" /></a>
			</div>
	
			<?php foreach ($items as $item) : ?>
				<?php load_include('users-resultview', array('user' => $item)); ?> 
			<?php endforeach; ?> 	
	
			<?php pagination(); ?>

		</div>	
			
	</div>
