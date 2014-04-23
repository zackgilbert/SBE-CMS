		
	<div id="content">
	
		<div id="content-header">
			<p class="header-section">Site Dashboard</p>
			<h2 class="header-title">
				Recent Site Activity and Stats 				
			</h2>
		</div>
		
		<div id="content-2colL-left">
		
			<?php /*?><h3 class="dashboard-stats-title">
				Recent Traffic <span class="stats-viewmore"><a href="<?= LOCATION; ?>admin/stats/">View More Stats &raquo;</a></span>
			</h3>
			
			<?php if (!file_exists(ABSPATH . "piwik/config/config.ini.php")) : ?> 

			<div>

				<h3>Stats Have Not Been Installed Yet!</h3>
				<p>You'll need to install the stats before we can start tracking and displaying them for you.</p>
				<p style="text-align: center;"><input type="button" value="Install Stats" onclick="window.location.href='<?= LOCATION; ?>piwik/index.php';" /></p>

			</div>

			<?php else: ?>
				
			<div id="widgetIframe">
				<script type="text/javascript" src="<?= LOCATION; ?>admin/graphs/amcolumn/swfobject.js"></script>

				<div id="stat-recenttraffic"><strong>You need to upgrade your Flash Player</strong></div>
				<script type="text/javascript">
					// <![CDATA[		
					var so = new SWFObject("<?= LOCATION; ?>admin/graphs/amline/amline.swf", "stats-recenttrafic", "720", "225", "8", "#FFFFFF");
					so.addVariable("path", "graphs/amline/");
					so.addVariable("settings_file", encodeURIComponent("graphs/amline/amline_settings.xml"));
					so.addVariable("data_file", encodeURIComponent("stats/recenttraffic/?color=6cb35d"));		
					so.write("stat-recenttraffic");  // this id must match the div id above
					// ]]>
				</script>
				
			</div>

			<?php endif; ?>*/?> 
			
			<div class="welcome-item">
				<p class="welcome-text">Welcome Back,</p>
				<h3 class="welcome-item-name"><?= user('name'); ?></h3>
				<p> Take a look at the Recently Edited pages or make some Content Edits of your own.</p>
			</div>
			
			<div class="dashboard-item">
				<h3 class="dashboard-item-title">
					Most Recently Edited Pages
				</h3>
				<ul class="recently-edited">
					<?php foreach ($pages = get_recently_edited_pages(5) as $page) : ?> 
						<li>
							<a href="<?= LOCATION; ?>admin/pages/<?= $page['id']; ?>/"><?= $page['name']; ?></a><br /> Edited by <?= $page['creator']; ?> on <?= format_date($page['created_at'], 'M j, Y \a\t g:ia'); ?>
						</li>
					<?php endforeach; ?> 
					<?php if (count($pages) < 1) : ?> 
						<li class="edited-none">There haven't been any Pages edited recently.</li>
					<?php endif; ?> 
				</ul>
			</div>
			
			<div style="clear:both; padding-top: 20px;">
				<h3 class="dashboard-item-title">Jump Club's Hours (Sidebar Widget)</h3>
				<form method="post" action="<?= LOCATION; ?>admin/scripts/includes-save/">
					<input type="hidden" name="file" value="<?= get_site() . '/' . get_theme() . '/includes/hours.php'; ?>"/>
					<span class="toggle-editor" style="float: right;"><a href="javascript:toggleEditorMode('hours-widget');">Toggle: Rich Text or Raw HTML</a></span>
					<textarea id="hours-widget" cols="100" rows="6" name="contents" class="body wysiwyg"><?= htmlentities(get_file(ABSPATH . 'sites/' . get_site() . '/' . get_theme() . '/includes/hours.php')); ?></textarea>
					<input type="submit" value="Save" />
				</form>
			</div>
			
			<div style="clear:both; padding-top: 20px;">
				<h3 class="dashboard-item-title">Testimonial (Sidebar Widget)</h3>
				<form method="post" action="<?= LOCATION; ?>admin/scripts/includes-save/">
					<input type="hidden" name="file" value="<?= get_site() . '/' . get_theme() . '/includes/testimonial.php'; ?>"/>
					<span class="toggle-editor" style="float: right;"><a href="javascript:toggleEditorMode('testimonial-widget');">Toggle: Rich Text or Raw HTML</a></span>
					<textarea id="testimonial-widget" cols="100" rows="6" name="contents" class="body wysiwyg"><?= htmlentities(get_file(ABSPATH . 'sites/' . get_site() . '/' . get_theme() . '/includes/testimonial.php')); ?></textarea>
					<input type="submit" value="Save" />
				</form>
			</div>
			
		
		</div>
		
		<div id="content-2colL-right">
		
		
		</div>
		
	</div>
