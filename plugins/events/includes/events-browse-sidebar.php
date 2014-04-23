	
	<div class="browse-sidebar">
		
		<form method="get" action="<?= get_sitemap_section_url(); ?>browse/">
			
			<h3 class="browse-sidebar-title">Browse Events</h3>
			
			<div class="browse-sidebar-keyword">
				<label for="browse-event-keyword">By Keyword</label>
				<input type="text" name="keywords" value="<?= get('keywords'); ?>" id="browse-event-keyword" class="field-medium" />
			</div>
			
			<h4 class="browse-sidebar-restrict-title">Restrict Browse To:</h4>
			
			<div class="browse-sidebar-restrict">
				<dl class="restrict-terms">
					<?php /*?><dt><label for="browse-event-category" class="restrict-title">Category</label></dt>
					<dd>
						<select id="browse-event-category" name="categories[]">
							<option value="">All Categories</option>
							<?php foreach (get_event_categories() as $category) : ?> 
							<option value="<?= $category['id']; ?>"<?= (is_array(get('categories')) && in_array($category['id'], get('categories'))) ? ' selected="selected"' : ''; ?>><?= valid($category['name']); ?></option> 
							<?php endforeach; ?> 
						</select>
					</dd>*/?>
					<dt><label for="browse-event-date" class="restrict-title">Date</label></dt>
					<dd>
						<select id="browse-event-date" name="date">
							<option value="today"<?= (get('date') == 'today') ? ' selected="selected"' : ''; ?>>Today</option>
							<option value="tomorrow"<?= (get('date') == 'tomorrow') ? ' selected="selected"' : ''; ?>>Tomorrow</option>
							<option value="7-days"<?= (get('date') == '7-days') ? ' selected="selected"' : ''; ?>>Next 7 Days</option>
							<option value="30-days"<?= ((!get('date') || (get('date') == '30-days'))) ? ' selected="selected"' : ''; ?>>Next 30 Days</option>
							<option value="90-days"<?= ((get('date') == '90-days')) ? ' selected="selected"' : ''; ?>>Next 90 Days</option>
							<?php if (strpos(get('date'), "-") == 4) : ?> 
								<option value="<?= get('date'); ?>" selected="selected"><?= format_date(get_var('calendar'), ((get_var('calendar_day')) ? 'F jS, Y' : 'F, Y')); ?></option>
							<?php endif; ?> 
						</select>
					</dd>
					<?php /*?><dt><label for="browse-event-location" class="restrict-title">Location</label></dt>
					<dd>
						<select id="browse-event-location" name="location">
							<option value="">All Venues</option>
							<?php foreach (get_directories_by_type('venues') as $venue) : ?> 
							<option value="<?= $venue['id']; ?>"<?= (get('location') == $venue['id']) ? ' selected="selected"' : ''; ?>><?= valid($venue['name']); ?></option>
							<?php endforeach; ?> 
						</select>
					</dd>
					<?php */?>
				</dl>
			</div>
			
			<div class="browse-sidebar-submit">
				<input type="submit" value="Browse Events" class="submit"/>
			</div>
			
		</form>
		
	</div>
	