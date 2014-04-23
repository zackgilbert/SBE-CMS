	
	<div id="page-events">

		<div id="content-2colL-left">
		
			<h1 class="page-title">Special Events</h1>
		
			<div class="page-body editable">
            
               <p><img src="<?= LOCATION; ?>images/pic-tugofwar.jpg" width="530" height="339" alt="Tug of War" /></p>
               
              <p>Check out the Jump Club Calendar for details on upcoming Special Events. </p>
                <p>And don't forget, if you'd like to plan your own private event at Jump Club,<br /> <a href="<?= LOCATION; ?>parties/">get in touch with us</a>.</p>    
                <p><em>Click a date on the calendar to the right to check for events.</em></p>
                	
			</div>
            
			<?php /*?><?php load_include('events-submitnew'); ?> <?php */?>

			<div id="content-upcoming-events">

				<h2 class="upcoming-events-title">
					Upcoming Events 
					<?php /*?><span class="recent-events-browseall">&nbsp; - &nbsp;<a href="<?= get_sitemap_section_url(); ?>browse/">Browse All</a></span><?php */?>
				</h2>

				<!-- Insert Result view for the next 5 upcoming events -->
				<?php if ($upcoming_events = upcoming_events(5)) : ?> 
					<?php foreach ($upcoming_events as $event) : ?> 
						<?php load_include('events-resultview', array('event' => $event)); ?> 
					<?php endforeach; ?> 
				<?php else : ?>
					There are no upcoming events.
				<?php endif; ?> 

			</div>
		
		</div>
		
		<div id="content-2colL-right">
		
			<div id="content-events-calendar">

				<div class="events-calendar-view">

					<?php if ((calendar('year') > year()) || ((calendar('year') == year()) && (calendar('month') > month()))) : ?>
						<div class="events-calendar-prev">
							<a href="<?= get_sitemap_section_url() . format_date(timestamp(calendar('year'), calendar('month')-1), 'Y/m/'); ?>">
								<img src="../plugins/events/images/icon-eventcal-prev.gif" alt="Previous" title="Previous Month" />
							</a>
						</div>
					<?php endif; ?>

					<div class="events-calendar-next">
						<a href="<?= get_sitemap_section_url() . format_date(timestamp(calendar('year'), calendar('month')+1, calendar('day')), 'Y/m/'); ?>">
							<img src="../plugins/events/images/icon-eventcal-next.gif" alt="Next" title="Next Month" />
						</a>
					</div>

					<table cellspacing="0">					
						<?php load_include('events-calendar'); ?> 				
					</table>

				</div>

			</div>
			
            <?= load_include('facebook'); ?>
            
		</div>

	</div>
		