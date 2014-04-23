<?php if ((count_search_items() > 0) || (isset($_GET['keywords']))) : ?> 
	<div class="search-container">
		<form method="get" action="">
			<div>
				<input type="hidden" name="section" value="<?= get_var('id'); ?>"/>
			</div>

			<div class="search-keyword">	
				<label for="keywords">Search by Keyword: </label>
				<input type="text" id="keywords" name="keywords" value="<?= valid(request('keywords')); ?>" class="search-field" /><br/>
			</div>
			
			<div class="search-filters">
			
				<label for="content-author">Filter by Author: </label>
				<select id="content-author" name="author" class="search-filter">
					<option value="">&nbsp;</option>
<?php foreach (get_blog_authors() as $author) : ?>
					<option value="<?= $author['id']; ?>"<?= (request('author') == $author['id']) ? ' selected="selected"' : ''; ?>><?= valid($author['name']); ?></option>
<?php endforeach; ?>
				</select>
			
				<label>Filter by Date: </label>
				<select name="date" class="search-filter">
					<option value="">&nbsp;</option>
					<option value="<?= TODAY; ?>"<?= (request('date') == TODAY) ? ' selected="selected"' : ''; ?>>Today</option>
					<option value="<?= format_date(daysFromNow(-1), 'Y-m-d'); ?>"<?= (request('date') == format_date(daysFromNow(-1), 'Y-m-d')) ? ' selected="selected"' : ''; ?>>Yesterday</option>
					<option value="<?= format_date(TODAY, 'Y-m-'); ?>"<?= (request('date') == format_date(TODAY, 'Y-m-')) ? ' selected="selected"' : ''; ?>>Current Month</option>
					<option value="<?= format_date(timestamp(year(), month()-1), 'Y-m-'); ?>"<?= (request('date') == format_date(timestamp(year(), month()-1), 'Y-m-')) ? ' selected="selected"' : ''; ?>>Last Month</option>
					<option value="<?= year(); ?>-"<?= (request('date') == (year() . '-')) ? ' selected="selected"' : ''; ?>>Current Year</option>
					<option value="<?= year()-1; ?>-"<?= (request('date') == (year()-1 . '-')) ? ' selected="selected"' : ''; ?>>Last Year</option>
				</select>
				<!--<input type="text" name="date" value="<?= valid(request('date')); ?>" class="date-field"/>-->
			
				<label for="content-status">Filter by Status: </label>
				<select id="content-status" name="status" class="search-filter">
					<option value="">&nbsp;</option>
<?php foreach (array(/*"draft" => "Draft", "private" => "Publish Privately", "public" => "Publish Publicly", "registered_only" => "Publish For Registered Users Only"*/"draft" => "Draft", "public" => "Published") as $key => $value) : ?>
					<option value="<?= $key; ?>"<?= (request('status') == $key) ? ' selected="selected"' : ''; ?>><?= $value; ?></option>
<?php endforeach; ?>
					<option value="deleted"<?= (request('status') == 'deleted') ? ' selected="selected"' : ''; ?>>Deleted</option>
				</select>
			</div>
							
			<div class="search-submit">
				<input type="submit" value="Search" class="submit"/>
			</div>
			
		</form>
	</div>
<?php endif; ?>	