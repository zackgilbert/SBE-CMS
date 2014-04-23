
	<div class="editor-resultview">
		
		<!--><a href="<?= LOCATION; ?>admin/users/<?= $user['id']; ?>/">
			<img src="<?= user_thumb($user['id']); ?>" alt="User Thumbnail" class="staff-resultview-pic" />
		</a>-->		
		<h3 class="editor-resultview-title">
			<a href="<?= LOCATION; ?>admin/users/<?= $user['id']; ?>/"><?= $user['name']; ?></a>
		</h3>
		<p class="editor-resultview-info">
			Email: <?= $user['email']; ?><br/>
			Type: <?= capitalize(singularize($user['types'])); ?><br/>
		</p>
		<p class="editor-resultview-stats">
			Joined: <?= format_date($user['created_at'], 'm/d/Y'); ?> &nbsp;&nbsp; Last Login: <?= format_date($user['last_seen_at'], 'm/d/Y'); ?> 
		</p>
		
		<div class="editor-resultview-tools">
			<a href="<?= LOCATION; ?>admin/users/<?= $user['id']; ?>/" class="editlink">Edit</a>	
			<a href="<?= LOCATION; ?>admin/users/<?= $user['id']; ?>/delete/" class="deletelink">Delete</a>		
			<!--<a href="<?= LOCATION; ?>scripts/password-reset-request/?id=<?= $user['id']; ?>" onclick="return confirm('Are you sure you want to reset this user\s password? Doing so will send them an email requiring them to create a new password.');" class="resetpasslink">Reset Password</a>-->
		</div>
		
	</div>
