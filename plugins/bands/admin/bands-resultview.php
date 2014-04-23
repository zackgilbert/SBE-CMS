	
	<div id="band-<?= $band->id; ?>" class="page-content-resultview bands-resultview">
		
		<div class="resultview-thumb">
			<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/bands/<?= $band->id; ?>/"><img src="<?= $band->thumb(75, 75, '1:1'); ?>" alt="" /></a>
		</div>
		
		<h3 class="resultview-title">
			<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/bands/<?= $band->id; ?>/"><?= $band->name(); ?></a> 
		</h3>
		<p class="resultview-body">
			<?= truncate(strip_tags($band->biography()), 50, true); ?> 
		</p>
		<p class="resultview-stats">
			Created on: <?= format_date($band->created_at, 'm/d/Y \a\t g:ia'); ?> 
			<?php if ($band->published_at) : ?> 
			&nbsp;&nbsp; Published on: <?= format_date($band->published_at, 'm/d/Y \a\t g:ia'); ?> 
			<?php else : ?> 
			&nbsp;&nbsp; Not Yet Published
			<?php endif; ?> 
		</p>
		
		<div class="resultview-tools">
			<a href="<?= $band->link(); ?>" class="viewlink">View</a> <a href="<?= LOCATION; ?>plugins/bands/admin/bands-delete/?id=<?= $band->id; ?>" class="deletelink">Delete</a>
		</div>
		
	</div>
	