
	<div id="gallery-<?= $gallery->id; ?>" class="page-content-resultview galleries-resultview">	
		
		<div class="resultview-thumbs">
			<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/galleries/<?= $gallery->id; ?>/">
				<img src="<?= $gallery->thumb(100, 100); ?>" alt="<?= $gallery->title(); ?>" class="content-resultview-pic" />
			</a>
		</div>
		<h3 class="resultview-name">
			<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/galleries/<?= $gallery->id; ?>/"><?= $gallery->title(); ?></a> 
			<span class="resultview-photos">
				<?= pluralization($gallery->photo_count(), 'Photos'); ?> 
			</span>
		</h3>
		<p class="resultview-description">
			<?= $gallery->description(); ?> 
		</p>
		<p class="resultview-stats">	
		Created on: <?= format_date($gallery->created_at, "m/d/Y \a\\t g:ia"); ?> 
		<?php if ($gallery->published_at) : ?> 
			&nbsp;&nbsp; Published: <?= format_date($gallery->published_at, "m/d/Y \a\\t g:ia"); ?>
		<?php endif; ?> 
		</p>
		
		<div class="resultview-tools">
			<a href="<?= $gallery->link(); ?>" class="viewlink">View</a> <a href="<?= LOCATION; ?>plugins/galleries/admin/galleries-delete/?id=<?= $gallery->id; ?>" class="deletelink">Delete</a>
		</div>
		
	</div>
	