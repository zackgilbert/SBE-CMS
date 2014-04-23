
	<li class="event-shortview">
		<div class="shortview-cal">
			<p class="shortview-cal-month"><?= format_date($event->_date, 'M'); ?></p>
			<p class="shortview-cal-day"><?= format_date($event->_date, 'd'); ?></p>
		</div>
		<h4 class="event-shortview-name"><a href="<?= $event->link(); ?>"><?= $event->name; ?></a></h4>
		<p class="event-shortview-date"><?= format_date($event->_date, 'l, F jS, Y'); ?></p>
	</li>
