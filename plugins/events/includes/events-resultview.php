	
	<div class="event-resultview">
		<div class="event-resultview-info">
			<div class="event-resultview-cal<?= ($event->_date == TODAY) ? ' today' : ''; ?>">
				<p class="resultview-cal-month"><?= format_date($event->_date, 'M'); ?></p>
				<p class="resultview-cal-day"><?= format_date($event->_date, 'd'); ?></p>
			</div>
			<h4 class="resultview-info-name"><a href="<?= $event->link(); ?>"><?= $event->name(); ?></a></h4>
			<h5 class="resultview-info-venue">
			<?php if (is_numeric($event->location)) : ?>
				<a href="<?= $event->venue('link'); ?>"><?= $event->venue('name'); ?></a>
			<?php else : ?> 
				<?= valid($event->location); ?> 
			<?php endif; ?> 
			</h5>
			<p class="resultview-info-date">
				<?= format_date($event->_date, 'l, F jS, Y'); ?><br />
				<?php if (empty($event->hours)) : ?> 
					<?= $event->time(); ?> 
				<?php endif; ?> 
			</p>
			<?php /*?><p class="resultview-info-type">
				Category: <a href="<?= $event->section_url() . 'types/' . $event->type('url'); ?>/"><?= $event->type('name'); ?></a>
				<?php if (!empty($event->subcategory)) : ?> 
				/ <?= valid($event->subcategory); ?> 
				<?php endif; ?> 
			</p>*/?>
		</div>					
	</div>
