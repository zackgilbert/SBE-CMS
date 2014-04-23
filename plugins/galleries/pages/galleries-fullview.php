		
	<div id="page-gallery">

		<div id="content-2colL-left">		
	            
            <p class="return-to-section">
                <a href="<?= get_sitemap_section_url(); ?>">Back to <?= section('name'); ?></a>
            </p>
        
            <div id="gallery-info">
            
                <h2 class="gallery-info-name"><?= $gallery->title(); ?> <span class="gallery-info-photocount">(<?= $gallery->photo_count(); ?> Photos)</span></h2>
                <p class="gallery-info-description"><?= $gallery->description(); ?></p>			
                <!--<p class="gallery-info-datepublished">Published on: <?= format_date($gallery->published_at, "F jS, Y"); ?></p>-->
            
            </div>
    
            <div id="gallery-photos">
            <?php foreach ($photos = $gallery->photos() as $photo) : ?> 				
                <div id="gallery-<?= $gallery->id; ?>-photo-<?= $photo->id; ?>" class="gallery-photo-item">					
                    <a href="<?= $gallery->link() . $photo->id; ?>/" id="gallery-photo-<?= $photo->id; ?>">
                        <img src="<?= $photo->photo(150, 150); ?>" alt="<?= $photo->alt(); ?>" title="<?= $photo->alt(); ?>"/>
                    </a>					
                    <?php if ($photo->title()) : ?> 					
                        <h3 class="photo-item-title">
                            <a href="<?= $gallery->link() . $photo->id; ?>/"><?= $photo->title(); ?></a>
                        </h3>						
                    <?php endif; ?>				
                    <?php if ($photo->show_comments()) : ?> 
                    <p class="photo-item-comments">
                        <a href="<?= $gallery->link() . $photo->id; ?>/#comments"><?= pluralization($photo->comment_count, 'Comments'); ?></a>
                    </p>
                    <?php endif; ?> 
                </div>				
            <?php endforeach; ?> 
            <?php if (count($photos) < 1) : ?> 
                <div>
                    <p>Sorry, but this gallery doesn't have any photos yet.</p>
                </div>
            <?php endif; ?> 
            </div>
            
         </div>
         
         <div id="content-2colL-right">
			
            <?= load_include('hours'); ?> 
            
			<?= load_include('testimonial'); ?> 
   			
		</div>
		
	</div>
