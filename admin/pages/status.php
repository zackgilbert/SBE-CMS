
	<h2>Current System Status</h2>
	
	<div id="installation-requirements">
		<?php if (server_meets_minimum_requirements() && server_meets_preferred_requirements()) : ?>
		<h2 class="success">Your Server Meets the Preferred Flint Requirements</h2>
		<p>You will have the optimal settings for your Flint installation.</p>
		<?php elseif (server_meets_preferred_requirements()) : ?> 
		<h2 class="failure">ERROR: Your Server DOES NOT Meets the Minimum Flint Requirements</h2>
		<p>Flint can not be installed because necessary tools it needs to function properly are not available.</p>	
		<?php else : ?> 
		<h2 class="warning">Warning: Your Server Does Not Meet the Preferred Flint Requirements</h2>
		<p>Flint can be installed, but it will not function as optimally as intended.</p>	
		<?php endif; ?> 
	</div>
	
	<h3>Minimum Requirements</h3>
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
	
	<h3>Preferred Requirements</h3>
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

	<div id="installation-requirements">
		<?php if (count($notWritable) <= 0) : ?>
			<h2 class="success">Your File and Folder Permissions are Correct</h2>
		<?php else : ?> 
			<h2 class="failure">Your File and Folder Permissions are Not Correct</h2>	
		<?php endif; ?> 
	</div>
	
	<?php if (count($notWritable) > 0) : ?> 
		<p class="permissions-disclaimer">
			The Flint Installation needs to be able to write certain files. So in order to continue you must manually change the permissions (CHMOD to 0766) for the files below marked in red. 
		</p>
		<p class="permissions-disclaimer">	
			If you are unsure how to do this, contact your IT person, or <a href="http://codex.wordpress.org/Changing_File_Permissions">read this article for instructions</a>.
		</p>
	<?php endif; ?> 
	
	<ul class="requirements-list">
		<?php foreach ($toCheck as $file) : ?> 
			<li class="<?= (in_array(PATH . $file, $notWritable)) ? 'fail' : 'success'; ?>"><?= $file; ?> 
			<span class="status"><?= ((in_array(PATH . $file, $notWritable))) ? 'NO' : 'YES'; ?></span>
			</li>
		<?php endforeach; ?> 
	</ul>
