	
	<h2 class="content-title">Manage Comments (<?= count_search_items(); ?> found)</h2>
	
	<?php pagination(); ?> 

	<?php foreach (get_search_items() as $item) : ?> 
					
		<?php load_include("comments-resultview.php", array("comment" => $item)); ?> 
			
	<?php endforeach; ?> 
	
	<?php if (count_search_items() == 0) : ?> 
	
			<p>No comments results found.</p>
	
	<?php endif; ?> 
	
	<?php pagination(); ?> 
	