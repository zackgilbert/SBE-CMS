	
	<div id="content">
	
		<div id="content-header">
			<p class="header-section">Site Settings</p>	
			<h2 class="header-title">
				Manage Your Settings &amp; Preferences 				
			</h2>
		</div>
		

		<div id="content-2colR-left">
	
			<div id="settings-sidebar">
			
				<h3 class="settings-sidebar-title">Select a Setting</h3>
				
				<ul class="settings-items">
					<li<?= ($settings == 'general') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/settings/">General Settings</a></li>
					<li<?= ($settings == 'metadata') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/settings/metadata/">Site Information</a></li>
					<li<?= ($settings == 'debug') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/settings/debug/">Debug Settings</a></li>
					<li<?= ($settings == 'apis') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/settings/apis/">API Keys</a></li>
					<li<?= ($settings == 'tools') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/settings/tools/">Developer Tools</a></li>
				</ul> 
				
			</div>
			
		</div>
	
		<div id="content-2colR-right">
				
			<?php load_include('settings-' . $settings); ?> 
			
		</div>
		
	</div>
