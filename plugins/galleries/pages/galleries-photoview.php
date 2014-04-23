		
	<div id="page-gallery">

		<div id="content-2colL-left">
		
            <p class="return-to-section">
                <a href="<?= $gallery->link(); ?>">View Full Album</a>
            </p>
            
            <div id="gallery-info">
                
                <h2 class="gallery-info-name"><?= $gallery->title(); ?> <span class="gallery-info-photocount">(<?= $gallery->photo_count(); ?> Photos)</span></h2>
                <p class="gallery-info-description"><?= $gallery->description(); ?></p>			
                <!--<p class="gallery-info-datepublished">Published on: <?= format_date($gallery->published_at, "F jS, Y"); ?></p>-->
                
                <div class="gallery-photo-nav">
                    <div class="photo-nav-prev">
                        <?php if ($previous = $photo->previous()) : ?> 
                            <a href="<?= $previous->link(); ?>">
                                <img src="<?= $previous->thumb(60, 60); ?>" alt="Previous Photo" title="View Previous Photo"/>
                            </a>
                        <?php else : ?> 
                            <img src="../plugins/galleries/images/gr-photonav-first.gif" alt="First Photo" title="You're at the first photo" />
                        <?php endif; ?> 
                    </div>
        
                    <div class="photo-nav-next">
                        <?php if ($next = $photo->next()) : ?> 
                            <a href="<?= $next->link(); ?>">
                                <img src="<?= $next->thumb(60, 60); ?>" alt="Next Photo" title="View Next Photo"/>
                            </a>
                        <?php else : ?> 
                            <img src="../plugins/galleries/images/gr-photonav-last.gif" alt="Last Photo" title="You're at the last photo" />
                        <?php endif; ?>
                    </div>
                </div>
            
            </div>
            
            <div id="gallery-viewphoto">
                <h2 class="photo-title"><?= $photo->title(); ?></h2>
                
                <div class="photo-item">
                    <img src="<?= $photo->photo(608); ?>" alt="<?= $photo->alt(); ?>"/>
                </div>
                
                <?php if ($photo->description()) : ?> 
                    <p class="photo-description"><?= $photo->description(); ?></p>
                <?php endif; ?> 
            </div>
            
            <?php if ($photo->show_comments()) : ?> 
                <?php if (!$photo->comment_status('closed')) : ?> 				
                    <div id="comments" class="content-comments">
                        <h3 class="comment-title">Comments for "<?= $photo->title(); ?>" (<?= count($photo->comments()); ?>)</h3>
                        <p class="comment-disclaimer">
                            The owners of this site are not responsible for the content of these comments. They reserve the right to remove comments at their discretion.
                        </p>
                        
                        <?php comments($photo->table, $photo->id); ?>
            
                        <?php comments_form($photo->table, $photo->id, get_var('level_id')); ?> 
            
                    </div>				
                <?php else : ?> 				
                <div id="comments" class="content-comments">
                    <h3 id="comment-form" class="comment-title">Comments for "<?= $photo->title(); ?>"</h3>
                    <p class="comment-disclaimer">
                        Comments for this photo are currently closed.
                    </p>
                </div>				
                <?php endif; ?> 
            <?php endif; ?>
         
         </div>
         
         <div id="content-2colL-right">
			
            <?= load_include('hours'); ?> 
            
            <?= load_include('testimonial'); ?> 
   			
		</div>
		
	</div>

