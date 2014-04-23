
	<div id="gallery-gallery-<?= $gallery->id; ?>" class="gallery-resultview">	
		<div class="gallery-resultview-thumbs">
			<a href="<?= $gallery->link(); ?>">
				<img src="<?= $gallery->thumb(200, 200); ?>" alt="<?= $gallery->title(); ?>" class="gallery-resultview-pic" />
			</a>
		</div>
		<h3 class="gallery-resultview-name">
			<a href="<?= $gallery->link(); ?>"><?= $gallery->title(); ?></a> 
			<span class="gallery-resultview-stats">
				<?= pluralization($gallery->photo_count(), 'Photos'); ?> 
			</span>
		</h3>
		<p class="gallery-resultview-description">
			<?= $gallery->description(); ?> 
		</p>
		<!--<p class="gallery-resultview-datepublished">
			Published on: <?= format_date($gallery->published_at, "F jS, Y"); ?> 
		</p>-->		
	</div>
