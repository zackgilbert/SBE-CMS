	
	<div id="content">

		<div id="content-header">
			<p class="header-section">Site Comments</p>	
			<h2 class="header-title">
				Browse and Edit Comments 				
			</h2>
		</div>
		
		<div id="content-2colR-left">
			
			<?= load_include('comments-sidebar.php'); ?>
			
		</div>
		
		<div id="content-2colR-right">

			<?php			
				if (get_page_var('item')) :					
					load_include("comments-edit.php", array('comment' => get_page_var('item')));						
				else :					
					load_include('comments-search.php');				
				endif;				
			?> 
		</div>

	</div>
	