
	<div id="page-events">

		<div id="content-2colL-left">
		
            <p class="return-to-section">
                <a href="<?= get_sitemap_section_url(); ?>">Back to <?= section('name'); ?></a>
            </p>
            
            <h2 class="browse-title">Events Calendar</h2>
    
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


            <div class="event-results">
    
                <div class="event-results-header">
                    <h3 class="results-header-title">
                    <?= events_browse_title(); ?> 
    
                    (<?= count_search_items(); ?>)
                    <!-- "on x date", "at x venue", "for x category", "from next x date range" -->
                    <?php if (count_search_items() > 0) : ?> 
                        <span class="results-viewing">
                            Currently Viewing: <?= pagination_viewing_start(); ?> - <?= pagination_viewing_end(); ?> of <?= count_search_items(); ?> 
                        </span>
                    <?php endif; ?>
                    </h3>
                </div>
    
                <?php pagination(); ?> 
    
                <?php
                    foreach (get_search_items() as $item) :
                        load_include('events-resultview', array('event' => $item));
                    endforeach;
                ?> 
                <?php if (count_search_items() < 1) : ?> 
                    <p class="results-nonefound">No events were found using the criteria you have chosen. Try broadening your search.</p>
                <?php endif; ?> 
    
                <?php pagination(); ?> 
    
            </div>
         
        </div>
        
        <div id="content-2colL-right">

			<?= load_include('facebook'); ?>

		</div>
        
    </div>
			