	
	<div class="pagination-container">

		<ul class="pagination">
			<li class="previous"><?= pagination_prev($previous); ?></li>
			<?php while (pagination_has_pages()) : ?> 
				<?php if (pagination_page_is_current()) : ?> 
			
					<li class="current"><?= pagination_page_number(); ?></li>
			
				<?php elseif (pagination_page_is_separator()) : ?> 
			
					<li class="separator"> ... </li>
			
				<?php else : ?> 
			
					<li class="page"><a href="<?= pagination_page_link(); ?>"><?= pagination_page_number(); ?></a></li>
			
				<?php endif; ?> 
			<?php endwhile; ?> 			
			<li class="next"><?= pagination_next($next); ?></li>
		</ul>

	</div>
	