	
	<div id="comments-sidebar">
		
		<h3 class="comments-sidebar-title">Search Comments</h3>
		
		<form method="get" action="<?= LOCATION; ?>admin/comments/">
			
			<div class="comments-sidebar-search">
				<label for="keywords">Search by Keyword: </label>
				<input type="text" id="keywords" class="field-search" name="keywords" value="<?= request('keywords'); ?>" /><br/>
			</div>
							
			<div class="comments-sidebar-search">
				<label>Comments in Section: </label>
				<select name="section" class="filter">
					<option value="">&nbsp;</option>
					<?php foreach (get_sections() as $section) : ?>
						<option value="<?= $section['id']; ?>"<?= (request('section') == $section['id']) ? ' selected="selected"' : ''; ?>><?= valid($section['display']); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<div class="comments-sidebar-search">
				<label>Filter by Status: </label>
				<select name="status" class="filter">
					<option value="">&nbsp;</option>
					<option value="approved"<?= (request('status') == 'approved') ? ' selected="selected"' : ''; ?>>Approved</option>
					<option value="moderated"<?= (request('status') == 'moderated') ? ' selected="selected"' : ''; ?>>Moderated</option>
					<option value="unapproved"<?= (request('status') == 'unapproved') ? ' selected="selected"' : ''; ?>>Unapproved</option>
				</select>
			</div>
			
			<div class="comments-sidebar-search">			
				<label>Filter by Date: </label>
				<select name="date" class="filter">
					<option value="">&nbsp;</option>
					<option value="<?= TODAY; ?>"<?= (request('date') == TODAY) ? ' selected="selected"' : ''; ?>>Today</option>
					<option value="<?= format_date(daysFromNow(-1), 'Y-m-d'); ?>"<?= (request('date') == format_date(daysFromNow(-1), 'Y-m-d')) ? ' selected="selected"' : ''; ?>>Yesterday</option>
					<option value="<?= format_date(TODAY, 'Y-m-'); ?>"<?= (request('date') == format_date(TODAY, 'Y-m-')) ? ' selected="selected"' : ''; ?>>Current Month</option>
					<option value="<?= format_date(timestamp(year(), month()-1), 'Y-m-'); ?>"<?= (request('date') == format_date(timestamp(year(), month()-1), 'Y-m-')) ? ' selected="selected"' : ''; ?>>Last Month</option>
					<option value="<?= year(); ?>-"<?= (request('date') == (year() . '-')) ? ' selected="selected"' : ''; ?>>Current Year</option>
					<option value="<?= year()-1; ?>-"<?= (request('date') == (year()-1 . '-')) ? ' selected="selected"' : ''; ?>>Last Year</option>
				</select>
			</div>
			
			<div class="comments-sidebar-submit">
				<input type="submit" value="Search" class="btn-submit"/>
			</div>
			
		</form>		
		
	</div>