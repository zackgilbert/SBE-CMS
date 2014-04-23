
	<div id="page-events">

		<div id="content-2colL-left">
		
            <p class="return-to-section">
                <a href="<?= get_sitemap_section_url(); ?>">Back to <?= section('name'); ?></a>
            </p>
            
            <div id="event-info">
                <h2 class="event-info-name"><?= $event->link_to_name(); ?></h2>
                <div id="event-info-typespecific">
                    <img src="<?= $event->photo(180); ?>" class="event-info-pic" alt="Event Photo" />
                    <dl>
                        <?php /*?><dt>Event Type</dt>
                            <dd><a href="<?= $event->section_url() . 'types/' . $event->type('url'); ?>/"><?= $event->type('name'); ?></a></dd>
                        <?php if (!empty($event->subcategory)) : ?> 
                        <dt>Event Category</dt>
                            <dd><?= $event->subcategory; ?></dd>
                        <?php endif; ?> */?>
                        <?php if ($event->hours()) : ?> 
                        <dt>Event Hours</dt>
                            <dd><?= $event->hours(); ?></dd>
                        <?php endif; ?> 
                        <?php if (!empty($event->price)) : ?> 
                        <dt>Event Price</dt>
                            <dd><?= $event->price; ?></dd>			
                        <?php endif; ?> 
                    </dl>
                </div>			
                <dl class="event-info-general">
                    <dt>When</dt>
                        <dd>
                            <?= $event->when(); ?><br/>
                            <?php if (!empty($event->ends_on)) : ?>
                                (continues until <?= format_date($event->ends_on, 'M j, Y'); ?>)<br/>
                            <?php endif; ?>
                        </dd>
                    <?php if ($event->location()) : ?> 
                    <dt>Where</dt>
                        <dd><?= $event->location(); ?></dd>
                    <?php endif; ?> 
                    <dt>Contact</dt>
                        <dd><?= format_links($event->contact); ?></dd>
                    <?php if ($event->description()) : ?> 
                        <dt>Description</dt>
                            <dd><?= $event->description(); ?></dd>
                    <?php endif; ?>
                </dl>
            </div>		
    
            <div id="event-upcoming">
                <h3 class="event-upcoming-title">Upcoming Dates</h3>
    
                <?php if (count($event->getDates()) > 0) : ?> 
                <ul>
                    <?php foreach ($event->getDates() as $date) : ?> 
                        <li class="event-upcoming-item">	
                            <div class="event-upcoming-info">
                                <div class="event-upcoming-cal<?= ($date == TODAY) ? ' today' : ''; ?>">
                                    <p class="upcoming-cal-month"><?= format_date($date, 'M'); ?></p>
                                    <p class="upcoming-cal-day"><?= format_date($date, 'd'); ?></p>
                                </div>
                                <h4 class="upcoming-info-name"><?= $event->name(); ?></h4>
                                <p class="upcoming-info-date">
                                    <?= format_date($date, 'l, F jS, Y'); ?><br />
                                </p>
                            </div>					
                        </li>
                    <?php endforeach; ?> 
                </ul>
                <?php else: ?> 
    
                <div>There are no upcoming dates for this event. Most likely, it is because this event has either already past, or is too far in the future.</div>
    
                <?php endif; ?> 
            </div>
    
            <?php if ($event->show_comments()) : ?> 
            
                <?php if (!$event->comment_status('closed')) : ?> 
            
                <div id="comments" class="content-comments">
                    <h3 class="comment-title">Comments for "<?= $event->name(); ?>" (<?= count($event->comments()); ?>)</h3>
                    <p class="comment-disclaimer">
                        This site is not responsible for the content of these comments. Both the author of this event and owners of the site reserve the right to remove comments at their discretion.
                    </p>				
                    
                    <?php comments($event->table, $event->id); ?>	
                    
                    <?php comments_form($event->table, $event->id, get_var('level_id')); ?>
                    
        
                </div>
            
                <?php else : ?> 
        
                <div id="comments" class="content-comments">
                    <h3 id="comment-form" class="comment-title">Comments for "<?= $event->title(); ?>"</h3>
                    <p class="comment-disclaimer">
                        Comments for this event are currently closed.
                    </p>
                </div>
    
                <?php endif; ?>
                 
            <?php endif; ?>
    
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
