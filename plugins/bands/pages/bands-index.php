
	<div id="content-column">
	
		<?php foreach ($bands = get_bands() as $band) : ?> 		
		<div id="band-<?= $band->id; ?>" class="band-resultview">			
			<div class="band-logo">
				<a href="<?= $band->link(); ?>"><img src="<?= $band->thumb(175, 105); ?>" alt="<?= $band->name(); ?>"/></a>
			</div>
			<p class="band-bio"><?= $band->biography(); ?></p>			
		</div>		
		<?php endforeach; ?> 
		
		<?php if (count($bands) < 1) : ?> 
			<div>
				<h3>No Bands Found.</h3>
			</div>			
		<?php endif; ?> 
	
	</div>
