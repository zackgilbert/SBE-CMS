
	<h3>Log Manager</h3>

	<div>

		<ul>

<?php

	if ($handle = opendir(LOGS)) {
		$dirs = array();
		while (false !== ($dir = readdir($handle))) {
			if (substr($dir, 0, 1) != ".") {
				$dirs[] = $dir;
			}
		}
		sort($dirs);

		foreach ($dirs as $dir) { 
			
?> 

			<li>
				<h4><?= $dir; ?></h4>

				<ul>

<?php
			
			if ($handle2 = opendir(LOGS . $dir)) {
				$files = array();
				while (false !== ($file = readdir($handle2))) {
					if (substr($file, 0, 1) != ".") {
						$files[] = $file;
					}
				}
				sort($files);

				foreach ($files as $file) {

					?>

					<li><a href="<?= LOCATION; ?>admin/logs/<?= $dir; ?>/<?= $file; ?>/"><?= $file; ?></a> - <?= filesize('./logs/' . $dir . '/' . $file); ?> bytes - <a href="<?= LOCATION; ?>admin/logs/<?= $dir; ?>/<?= $file; ?>/edit/">EDIT</a> - <a href="<?= LOCATION; ?>admin/logs/<?= $dir; ?>/<?= $file; ?>/delete/">DELETE</a></li>

<?php

				}

?>

				</ul>

			</li>

<?php

				closedir($handle2);
			}
		}
		closedir($handle);
	}

?>

		</ul>

	</div>
