<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?= title('Site Manager'); ?></title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<?= stylesheets(); ?> 
<?= javascripts(); ?> 
</head>

<body style="margin:0px;">
		
	<div class="user-info">
			
		<form method="post" action="<?= LOCATION; ?>admin/account/save/">
			<div>
				<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>"/>
			</div>

			<h3 class="info-title">
				Edit Your User Information
			</h3>
			<!--<p class="info-description">
				Update your basic user information, including login credentials and greeting.
			</p>-->
			
			<div class="info-item">			
				<label for="account-fullname">Full Name</label>
				<input type="text" name="user[name]" id="account-fullname" value="<?= value('user[name]', user('name')); ?>" class="field-small"/>
				<p class="info-item-description">
					This is how you will be greeted throughout the site.						
				</p>				
			</div>
			<div class="info-item">	
				<label for="account-email">Email Address</label>
				<input type="text" name="user[email]" id="account-email" value="<?= value('user[email]', user('email')); ?>" class="field-medium"/>
				<p class="info-item-description">
					You will use this to login, so make sure it's valid.
				</p>				
			</div>
			<div class="info-item">						
				<div id="password-container">				
					<label>Password</label>
					<div class="password-link-change">
						<a href="javascript:;" onclick="$('#password-change-container').toggle(); $('#password-container').toggle();" class="change-password">Change Password</a>	
					</div>
				</div>									
				<div id="password-change-container" style="display: none;">							
					<label for="account-password">New Password</label> 
					<input type="password" name="user[manage_password]" id="account-password" class="field-small"/>
					<label for="account-password-confirm" class="inline">Confirm</label>
					<input type="password" name="user[manage_password_confirm]" id="account-password-confirm" class="field-small"/>
					<p class="info-item-description">
						Must be at least 6 characters long.
					</p>
					<div class="password-link-dontchange">
						<a href="javascript:;" id="dont-change-password" onclick="$('#password-change-container').toggle(); $('password').value = ''; $('password-confirmation').value = ''; $('#password-container').toggle(); return false;" class="dontchange-password">Nevermind, Don't Change</a>
					</div>
				</div>																
			</div>	
												
			<div class="info-item-save">				
				<img src="../images/btn-sheet-savechanges.gif" alt="Save Changes" onclick="$('form').submit();" />	
				or <a href="javascript:;" onclick="parent.$.fn.sheet.close();" class="cancel">Cancel</a>
			</div>
		
		</form>
		
	</div>
	
	<?= display_message(); ?> 

</body>
</html>