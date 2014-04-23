<?php require_once LIBRARY . "requirements.php"; ?>

	<h3 class="tool-item-title">Current System Status</h3>
	
	<div>
	
		<div class="status-message">
			<?php if (server_meets_minimum_requirements() && server_meets_preferred_requirements()) : ?>
			<h2 class="success">Server Meets the Preferred Requirements</h2>
			<p>Your site has the optimal settings enabled.</p>
			<?php elseif (server_meets_preferred_requirements()) : ?> 
			<h2 class="failure">ERROR: Server DOES NOT Meets the Minimum Requirements</h2>
			<p>Site unavailable. Please install necessary tools to meet minimum requirements.</p>	
			<?php else : ?> 
			<h2 class="warning">Warning: Your Server Does Not Meet the Preferred Requirements</h2>
			<p>Your site does not have all optimal settings and may not function as intended.</p>	
			<?php endif; ?> 
		</div>
		
		<h3 class="requirements-title">Minimum Requirements</h3>
		<ul class="requirements-list">
			<li class="<?= (is_apache()) ? 'success' : 'fail'; ?>">
				Apache Server <span class="status"><?= (is_apache()) ? 'YES' : 'NO'; ?></span>
			</li>
			<li class="<?= (version_compare(PHP_VERSION, MINIMUM_PHP_VERSION, ">=")) ? 'success' : 'fail'; ?>">
				PHP Version <?= MINIMUM_PHP_VERSION; ?> <span class="status"><?= (version_compare(PHP_VERSION, MINIMUM_PHP_VERSION, ">=")) ? 'YES' : 'NO'; ?></span>
			</li>
			<li class="extensions">
				PHP Extensions:
				<ul>
					<?php foreach (required_extensions() as $ext) : ?> 
						<li class="<?= (in_array($ext, loaded_extensions())) ? 'success' : 'fail'; ?>"><?= $ext; ?> <span class="status"><?= (in_array($ext, loaded_extensions())) ? 'YES' : 'NO'; ?></span></li>
					<?php endforeach; ?> 
					<?php if (count(required_extensions()) < 1) : ?> 
						<li>Flint Currently Has No Required Extensions.</li>
					<?php endif; ?> 
				</ul>
			</li>
		</ul>
		
		<h3 class="requirements-title">Preferred Requirements</h3>
		<ul class="requirements-list">
			<li class="<?= (is_apache()) ? 'success' : 'fail'; ?>">
				Apache Server <span class="status"><?= (is_apache()) ? 'YES' : 'NO'; ?></span>
			</li>
			<li class="<?= (version_compare(PHP_VERSION, PREFERRED_PHP_VERSION, ">=")) ? 'success' : 'fail'; ?>">
				PHP Version <?= PREFERRED_PHP_VERSION; ?> <span class="status"><?= (version_compare(PHP_VERSION, PREFERRED_PHP_VERSION, ">=")) ? 'YES' : 'NO'; ?></span>
			</li>
			<li class="extensions">
				PHP Extensions:
				<ul>
					<?php foreach (preferred_extensions() as $ext) : ?> 
						<li class="<?= (in_array($ext, loaded_extensions())) ? 'success' : 'fail'; ?>"><?= $ext; ?> <span class="status"><?= (in_array($ext, loaded_extensions())) ? 'YES' : 'NO'; ?></span></li>
					<?php endforeach; ?> 
					<?php if (count(preferred_extensions()) < 1) : ?> 
						<li>Flint Currently Has No Preferred Extensions.</li>
					<?php endif; ?> 
				</ul>
			</li>
		</ul>
	
	<?php
	
		$toCheck = array('/cache/', '/config/', '/config/apikeys.php', '/config/database.php', '/config/metadata.php', '/config/sites.php', '/logs/', '/uploads/');
		$notWritable = array();
	
		foreach ($toCheck as $dir) : 
			$chmod = false;
			$writable = is_writable(PATH . $dir);
			
			if (!file_exists(PATH . $dir))
				continue;
			
			if (!is_writable(PATH . $dir))
				$chmod = @(chmod(PATH . $dir, 0766));
				
			if (!$writable && !$chmod)
				$notWritable[] = PATH . $dir;
	
			if (is_dir(PATH . $dir) && ($handle = opendir(PATH . $dir))) :
				while (false !== ($file = readdir($handle))) :
					if (is_dir(PATH . $dir . $file) && (substr($file, 0, 1) != ".")) :
					
						$chmod = false;
						$writable = is_writable(PATH . $dir . $file);
	
						if (!is_writable(PATH . $dir . $file))
							$chmod = @(chmod(PATH . $dir . $file, 0766));
							
						if (!$writable && !$chmod)
							$notWritable[] = PATH . $dir . $file;
	
					endif;
				endwhile;
				closedir($handle);
			endif;
		endforeach;
		
	?> 
	
		<div class="status-message">
			<?php if (count($notWritable) <= 0) : ?>
				<h2 class="success">File and Folder Permissions are Correct</h2>
			<?php else : ?> 
				<h2 class="failure">File and Folder Permissions are Not Correct</h2>	
			<?php endif; ?> 
			<?php if (count($notWritable) > 0) : ?> 
				<p class="permissions-disclaimer">
					Certain files need to be writable. Change the permissions (CHMOD to 0766) for the files marked in red below. If you are unsure how to do this, contact your IT person or <a href="http://codex.wordpress.org/Changing_File_Permissions">read this article for instructions</a>.
				</p>
			<?php endif; ?>
		</div>
		
		<ul class="requirements-list">
			<?php foreach ($toCheck as $file) : ?> 
				<li class="<?= (in_array(PATH . $file, $notWritable)) ? 'fail' : 'success'; ?>">
					<?= $file; ?> 
					<span class="status"><?= ((in_array(PATH . $file, $notWritable))) ? 'NO' : 'YES'; ?></span>
				</li>
			<?php endforeach; ?> 
		</ul>
		
	</div>

	<h3 class="tool-item-title">Error Logs</h3>

	<?php
	
		$files = array();
		if ($handle = opendir(LOGS . "errors")) {
			while (false !== ($file = readdir($handle))) {
				if (substr($file, 0, 1) != ".") {
					$files[] = $file;
				}
			}
			closedir($handle);
			sort($files);
		}
		
	?>

	<div>
 
		<ul class="errorlogs-list">
		<?php foreach ($files as $file) : ?> 
			<li><a href="<?= LOCATION; ?>admin/logs/errors/<?= $file; ?>/"><?= $file; ?></a> (<?= filesize('./logs/errors/' . $file); ?> bytes) &nbsp;&nbsp; <a href="<?= LOCATION; ?>admin/logs/errors/<?= $file; ?>/delete/" class="deletelink">Delete Log File</a></li>
		<?php endforeach; ?> 
		<?php if (count($files) < 1) : ?> 
			<li>No error logs found. That's a good thing.</li>
		<?php endif; ?> 
		</ul>

	</div>
