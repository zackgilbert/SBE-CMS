
	<div id="content">

		<div id="content-header">
			<p class="header-section">Site Statistics</p>	
			<h2 class="header-title">
				View The Latest Stats 				
			</h2>
		</div>
		
		<div>
			<h3>You're using Google Analytics.</h3>
			<p>We haven't integrated Google Analytics into our system yet, but it's coming. Until then, you can <a href="http://www.google.com/analytics/">go here to view your stats</a>.</p>
		</div>
		
		<?php /*?>
		<?php if (!file_exists(ABSPATH . "piwik/config/config.ini.php")) : ?> 
			
		<div>
			
			<h3>Stats Have Not Been Installed Yet!</h3>
			<p>You'll need to install the stats before they can show up on this page.</p>
			<p style="text-align: center;"><input type="button" value="Install Stats" onclick="window.location.href='<?= LOCATION; ?>piwik/index.php';" /></p>
			
		</div>
			
		<?php else: ?> 
		
		<script type="text/javascript" src="<?= LOCATION; ?>admin/graphs/amcolumn/swfobject.js"></script>
	
		<div id="content-2col-header">
		
			<div class="stat-container">
				<h3 class="stat-title">Visitors Over the Last Year</h3>
				<!--<iframe width="100%" height="405" src="<?= LOCATION; ?>piwik/index.php?module=Widgetize&amp;action=iframe&amp;moduleToWidgetize=VisitsSummary&amp;actionToWidgetize=index&amp;idSite=1&amp;period=month&amp;date=<?= TODAY; ?>&amp;disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>-->
				<div id="stat-visitoroverview"><strong>You need to upgrade your Flash Player</strong></div>
				<script type="text/javascript">
					// <![CDATA[		
					var so = new SWFObject("<?= LOCATION; ?>admin/graphs/amline/amline.swf", "stats-visitoroverview", "920", "300", "8", "#FFFFFF");
					so.addVariable("path", "../graphs/amline/");
					so.addVariable("settings_file", encodeURIComponent("../graphs/amline/amline_settings.xml"));
					so.addVariable("data_file", encodeURIComponent("visitoroverview/?color=6cb35d"));		
					so.write("stat-visitoroverview");  // this id must match the div id above
					// ]]>
				</script>
				
				<?php $stats = get_stats('VisitsSummary.get', '&period=year&date=last2'); ?> 
				<?php if (is_array($stats) && $latest_year = end($stats)) : ?> 
				<div class="stat-totals-container">
					
					<h3 class="stat-totals-title">Total Stats for Current Year (<?= year(); ?>)</h3>
					<div class="stat-totals-item">
						<h4>Total Visits</h4>
						<?= $latest_year['nb_visits']; ?>
					</div>	
					<div class="stat-totals-item">
						<h4>Unique Visits</h4>
						<?= $latest_year['nb_uniq_visitors']; ?>
					</div>	
					<div class="stat-totals-item">
						<h4>Page Views</h4>
						<?= $latest_year['nb_actions']; ?>
					</div>	
					<div class="stat-totals-item">
						<h4>Max Pages / Visit</h4>
						<?= $latest_year['max_actions']; ?>
					</div>	
					<div class="stat-totals-item">
						<h4>Bounce Rate</h4>
						<?= number_format($latest_year['bounce_count']/$latest_year['nb_visits']*100, 2); ?>%
					</div>	
					<div class="stat-totals-item">
						<h4>Total Time on Site</h4>
						<?= format_time($latest_year['sum_visit_length'], 'seconds', 'hours'); ?>
					</div>								
										
				</div>
				<?php endif; ?> 
				
			</div>	
		
		</div>
		
		<div id="content-2col-left">
		
			<div class="stat-container">
				<h3 class="stat-title">Visitors by Hour of Day</h3>
				<!--<iframe width="100%" height="350" src="<?= LOCATION; ?>piwik/index.php?module=Widgetize&amp;action=iframe&amp;moduleToWidgetize=VisitTime&amp;actionToWidgetize=getVisitInformationPerLocalTime&amp;idSite=1&amp;period=month&amp;date=<?= TODAY; ?>&amp;disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>-->
				<div id="stat-visitorbytime"><strong>You need to upgrade your Flash Player</strong></div>
				<script type="text/javascript">
					// <![CDATA[		
					var so = new SWFObject("<?= LOCATION; ?>admin/graphs/amcolumn/amcolumn.swf", "stat-visitorbytime", "450", "350", "8", "#FFFFFF");
					so.addVariable("path", "../graphs/amcolumn/");
					so.addVariable("settings_file", encodeURIComponent("../graphs/amcolumn/amcolumn_settings.xml"));
					so.addVariable("data_file", encodeURIComponent("visitorsbytime/?color=6cb35d"));
					so.write("stat-visitorbytime");   // this id must match the div id above
					// ]]>
				</script>
			</div>
			
			<div class="stat-container">
				<h3 class="stat-title">Returning Visitors</h3>
				<!--<iframe width="100%" height="350" src="<?= LOCATION; ?>piwik/index.php?module=Widgetize&amp;action=iframe&amp;columns[]=nb_visits_returning&amp;moduleToWidgetize=VisitFrequency&amp;actionToWidgetize=getEvolutionGraph&amp;idSite=1&amp;period=month&amp;date=<?= TODAY; ?>&amp;disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>-->
				<div id="stat-returningvisitors"><strong>You need to upgrade your Flash Player</strong></div>
				<script type="text/javascript">
					// <![CDATA[		
					var so = new SWFObject("<?= LOCATION; ?>admin/graphs/amline/amline.swf", "stats-visitoroverview", "450", "350", "8", "#FFFFFF");
					so.addVariable("path", "../graphs/amline/");
					so.addVariable("settings_file", encodeURIComponent("../graphs/amline/amline_settings.xml"));
					so.addVariable("data_file", encodeURIComponent("returningvisitors/?color=6cb35d"));		
					so.write("stat-returningvisitors");  // this id must match the div id above
					// ]]>
				</script>
			</div>
			
			<div class="stat-container">
				<h3 class="stat-title">Visitors by Page</h3>
				<iframe width="100%" height="350" src="<?= LOCATION; ?>piwik/index.php?module=Widgetize&amp;action=iframe&amp;moduleToWidgetize=Actions&amp;actionToWidgetize=getActions&amp;idSite=1&amp;period=month&amp;date=<?= TODAY; ?>&amp;disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>
			</div>
		
		</div>
		
		<div id="content-2col-right">
		
			<div class="stat-container">
				<h3 class="stat-title">Visitors by Referring Websites</h3>
				<iframe width="100%" height="350" src="<?= LOCATION; ?>piwik/index.php?module=Widgetize&amp;action=iframe&amp;moduleToWidgetize=Referers&amp;actionToWidgetize=getWebsites&amp;idSite=1&amp;period=month&amp;date=<?= TODAY; ?>&amp;disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>
			</div>
			
			<div class="stat-container">				
				<h3 class="stat-title">Visitors by Search Keyword</h3>
				<iframe width="100%" height="350" src="<?= LOCATION; ?>piwik/index.php?module=Widgetize&amp;action=iframe&amp;moduleToWidgetize=Referers&amp;actionToWidgetize=getKeywords&amp;idSite=1&amp;period=month&amp;date=<?= TODAY; ?>&amp;disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>
			</div>
			
			<div class="stat-container">
				<h3 class="stat-title">Visitors By Search Engine</h3>
				<iframe width="100%" height="350" src="<?= LOCATION; ?>piwik/index.php?module=Widgetize&amp;action=iframe&amp;moduleToWidgetize=Referers&amp;actionToWidgetize=getSearchEngines&amp;idSite=1&amp;period=month&amp;date=<?= TODAY; ?>&amp;disableLink=1" scrolling="no" frameborder="0" marginheight="0" marginwidth="0"></iframe>
			</div>
		
		</div>
		
		<?php endif; ?> */?>
	
	</div>
				