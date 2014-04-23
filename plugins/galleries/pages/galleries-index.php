	
	<div id="page-gallery">

		<div id="content-2colL-left">		
			
			<h1 class="page-title"><?= section('name'); ?></h1>

			<?php foreach ($galleries = get_galleries_by_section() as $gallery) : ?>
				<?php load_include('galleries-resultview', array('gallery' => $gallery)); ?>
			<?php endforeach; ?> 
			<?php if (count($galleries) < 1) : ?> 
				<div>
					<p>No galleries have been created yet.</p>
				</div>
			<?php endif; ?>
			
		</div>
		
        <div id="content-2colL-right">
			
            <?= load_include('hours'); ?> 
            
            <?= load_include('testimonial'); ?> 
   			
		</div>
        
   </div>
   