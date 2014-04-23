<?php check_user_login('admins,editors,contributors'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Login - <?= title(); ?></title>
	<?= stylesheets(); ?> 
	<?= javascripts(); ?> 
</head>
<body>

	<div id="header-container">	
		<div id="ssm-tools">
			<p class="ssm-branding">
				<span class="seen">SEEN</span> Site Manager
			</p>
			<p class="user-greeting">
				Hello, Please Login
			</p>
		</div>
	</div>
	
	<div id="content-container">
	
		<div id="company-logo">
			<h1><img src="<?= get_logo(); ?>" alt="<?= get_metadata('title'); ?>" /></h1>
		</div>
		
		<div id="login-container">
		
			<div id="login-form">
			
				<h2 class="login-form-title">Please Login<!-- <span class="forgot-password"><a href="#">Forget Your Password?</a></span>--></h2>
	
				<form method="post" action="<?= LOCATION; ?>admin/login/">
					<div class="login-form-row">
						<div class="row-item">						
							<label for="email">Email:</label>
							<?= text('email', '', 'tabindex="1" class="field-medium"'); ?> 
						</div>
						<div class="row-item">	
							<label for="password">Password:</label>
							<?= password('password', '', 'tabindex="2" class="field-medium"'); ?> 
						</div>
					</div>
					<div class="login-form-submit">
						<?= submit('Login', 'class="btn-submit" tabindex="3"'); ?> 
						<span id="rememberme"><?= checkbox('remember', 'value="true"'); ?> <label for="remember" class="rememberme">Keep Me Logged In</label></span>	
					</div>
				</form>
			
			</div>
			
		</div>
		
		<div id="content-footer">
			<p class="footer-links">
				<a href="mailto:support@areyouseen.com">Need Help? Email Us</a>  <!--  &nbsp;&#8226;&nbsp; <a href="#">Terms of Service</a>  &nbsp;&#8226;&nbsp;  <a href="#">Privacy Policy</a>-->
			</p>
			<p class="footer-copyright">
				&copy; Copyright <?= year(); ?> - All Rights Reserved.				
			</p>
			<div class="footer-poweredby">
				<a href="http://www.areyouseen.com"><img src="<?= LOCATION; ?>admin/images/gr-poweredbyseen.gif" alt="powered by SEEN" /></a>
			</div>			
		</div>
	
	</div>
	
	<script type="text/javascript">
		$(function() {
			$('#email').focus();
		});
	</script>
	
<?= display_message(); ?> 

</body>
</html>