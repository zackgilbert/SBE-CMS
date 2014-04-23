	
	<div id="users-sidebar">
		
		<h3 class="users-sidebar-title">Search Users</h3>
		
		<form method="get" action="<?= LOCATION; ?>admin/users/<?= get_var('subsection'); ?>/">
			
			<div class="users-sidebar-search">
				<label for="keywords">Search by Keyword: </label>
				<input type="text" id="keywords" class="field-search" name="keywords" value="<?= request('keywords'); ?>" /><br/>
				<!--<label><input type="checkbox" name="exact-match" /> Match Exactly</label>-->
			</div>
			
			<div class="users-sidebar-search">
				<label for="status">Filter by Status: </label>
				<select name="status" id="status" class="filter">
					<option value="">&nbsp;</option>
					<option value="deleted"<?= (request('status') == 'deleted') ? ' selected="selected"' : ''; ?>>Deleted</option>
				</select>
			</div>
						
			<div class="users-sidebar-submit">
				<input type="submit" value="Search" class="btn-submit"/>
			</div>
			
		</form>
		
	</div>
			