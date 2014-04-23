
	<div id="content">
	
		<div id="content-header">
			<p class="header-section">Site Users</p>	
			<h2 class="header-title">
				Search and Manage Users 				
			</h2>
		</div>

		<div id="content-2colR-left">
		
			<?= load_include('users-sidebar.php'); ?> 
			
		</div>
		
		<div id="content-2colR-right">
		
			<?php if (is_numeric($user['id'])) : ?> 
			
				<h2 class="content-title">
					Edit User "<?= $user['name']; ?>" 
					<?php if (is_numeric($user['id'])) : ?> 					
						<span class="user-delete">
							<a href="javascript();" onclick="if (confirm('Are you sure you want to delete this user?')) window.location = '<?= LOCATION; ?>admin/users/member/<?= $user['id']; ?>/delete/';">
								<img src="../images/btn-deleteuser.gif" alt="Delete User" title="Delete This User" />
							</a>
						</span>
					<?php endif; ?>
				</h2>
				
			<?php else : ?> 
				
				<h2 class="content-title">Add New User</h2>
			
			<?php endif; ?> 
	
			<div class="user-editarea">
				
				<!--<div class="user-editarea-photo">
			
					<img src="<?= user_thumb($user['id']); ?>" alt="<?= $user['name']; ?>'s Photo" class="user-pic" />
				
					<h3 class="user-editarea-photo-title">User Photo</h3>
							
					<?php if (user_has_thumb($user['id'])) : ?> 
						<div class="user-editarea-deletephoto">
							<a href="javascript:;" onclick="deleteFile(this, 'uploads/user_profiles/<?= $user['id']; ?>.');">
								<img src="<?= LOCATION; ?>admin/images/btn-deletephoto.gif" alt="Delete Photo" title="Delete this User's Photo"/>
							</a>
							<p class="deletephoto-disclaimer">(a default photo will be displayed instead)</p>
						</div>
					<?php endif; ?> 
					
				</div>-->
	
				<form method="post" action="<?= LOCATION; ?>admin/scripts/users-save/">
	
					<h3 class="user-editarea-title">User Information</h3>
					<div>
						<?php if (is_numeric($user['id'])) : ?> 
						<input type="hidden" name="users[id]" value="<?= $user['id']; ?>"/>
						<?php endif; ?> 
						<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>"/>
					</div>
	
					<dl class="user-editarea-items">				
						<dt class="item-title"><label for="user-name">User Name</label></dt>
							<dd class="item-value">
								<input type="text" id="user-name" name="users[name]" value="<?= value('users[name]', $user['name']); ?>" class="field-medium"/>
							</dd>
	
						<dt class="item-title"><label for="user-email">User Account Email</label></dt>
							<dd class="item-value">
								<input type="text" id="user-email" name="users[email]" value="<?= value('users[email]', $user['email']); ?>" class="field-medium"/>
							</dd>		
						
						<dt class="item-title"><label for="user-url">User URL</label></dt>
							<dd class="item-value">
								<input type="text" id="user-url" name="users[url]" value="<?= value('users[url]', $user['url']); ?>" class="field-medium"/>
							</dd>		
					</dl>
	
					<h3 class="user-editarea-title">User Type</h3>
					<ul class="user-editarea-type">
						<li><label><input type="radio" name="users[types]" value="admins"<?= (value('users[types]', $user['types']) == 'admins') ? ' checked="checked"': ''; ?>/> Admin</label></li>
						<li><label><input type="radio" name="users[types]" value="editors"<?= (value('users[types]', $user['types']) == 'editors') ? ' checked="checked"': ''; ?>/> Editor</label></li>
						<li><label><input type="radio" name="users[types]" value="authors"<?= (value('users[types]', $user['types']) == 'authors') ? ' checked="checked"': ''; ?>/> Author</label></li>
					</ul>
	
					<h3 class="user-editarea-title">User Password</h3>
					<div class="user-editarea-password">			
						<?php if (is_numeric($user['id'])) : ?> 
							<p>
								<a href="<?= LOCATION; ?>scripts/password-reset-request/?id=<?= $user['id']; ?>" onclick="return confirm('Are you sure you want to reset this user\'s password?');" class="resetpassword">Reset Password</a><br />
							This will send an email to this User with unique instructions on how to reset their password. So it's important that they have a valid email address.
							</p>
							<p>
								<a href="javascript:;" onclick="$('#user-password').parent().parent().toggle();" class="changepassword">Change Password</a><br />
								You can also manually change this users password. Just make sure to let them know what the new password is.
							</p>
						<?php endif; ?>				
						<dl class="user-editarea-items"<?= (is_numeric($user['id'])) ? ' style="display: none"': ''; ?>>				
							<dt class="item-title"><label for="user-password">New Password</label></dt>
								<dd class="item-value">
									<input type="password" id="user-password" name="users[password]" class="field-medium"/>
								</dd>
							<dt class="item-title"><label for="user-password-confirm">New Password Confirm</label></dt>
								<dd class="item-value">
									<input type="password" id="user-password-confirm" name="users[password_confirm]" class="field-medium"/>
								</dd>		
						</dl>				
					</div>
	
					<div class="user-editarea-save">
						<input type="submit" value="Save Changes" class="btn-save"/>
						or <a href="<?= LOCATION; ?>admin/users/" class="cancel">Cancel</a>
					</div>
	
				</form>
	
				<?php /*?><?php if (is_numeric($user['id'])) : ?> 
					<div>
						<input type="button" name="delete" value="Delete This User" id="delete" class="delete-button"  />
					</div>
				<?php endif; ?><?php */?>
	
			</div>
		
		</div>

	</div>
	