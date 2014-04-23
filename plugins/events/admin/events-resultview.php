	
	<div id="event-<?= $event->id; ?>" class="page-content-resultview events-resultview">
		
		<div class="event-resultview-cal<?= ($event->_date == TODAY) ? ' today' : ''; ?>">
			<p class="resultview-cal-month"><?= format_date($event->_date, 'M'); ?></p>
			<p class="resultview-cal-day"><?= format_date($event->_date, 'd'); ?></p>
		</div>
		<h4 class="resultview-info-name"><a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/events/<?= $event->id; ?>/"><?= $event->name(); ?></a></h4>
		<!--<h5 class="resultview-info-venue">
			<?= $event->venue('name'); ?> 
		</h5>-->
		<p class="resultview-info-date">
			<?= format_date($event->_date, 'l, F jS, Y'); ?><br />
			<?php if (empty($event->hours)) : ?> 
				<?= $event->time(); ?> 
			<?php endif; ?> 
		</p>
		<?php /*?><p class="resultview-info-type">
			Category: <a href="<?= $event->section_url() . 'types/' . $event->type('url'); ?>/"><?= $event->type('name'); ?></a>
		</p>*/ ?>
		
		<p class="resultview-stats">
			Created on: <?= format_date($event->created_at, 'm/d/Y \a\t g:ia'); ?> 
			<?php if ($event->published_at) : ?> 
			&nbsp;&nbsp; Published on: <?= format_date($event->published_at, 'm/d/Y \a\t g:ia'); ?> 
			<?php else : ?> 
			&nbsp;&nbsp; Not Yet Published
			<?php endif; ?> 
		</p>
		
		<div class="resultview-tools">
			<a href="<?= $event->link(); ?>" class="viewlink">View</a> <a href="<?= LOCATION; ?>plugins/events/admin/events-delete/?id=<?= $event->id; ?>" class="deletelink" onclick="return confirm('Are you sure you want to delete this?');">Delete</a>
		</div>
		
	</div>
	