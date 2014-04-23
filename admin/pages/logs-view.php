
	<div id="content">
	
		<div id="content-header">
			<p class="header-section">Site Settings</p>	
			<h2 class="header-title">Log Manager</h2>
			<p><a href="<?= LOCATION; ?>admin/settings/tools/">Back</a></p>			
		</div>
		
		<div>

			<h4>Viewing Log File: <?= get_var('type'); ?>/<?= get_var('file'); ?> (<?= filesize(LOGS . get_var('type') . '/' . get_var('file')); ?> bytes)</h4>

			<p>
				<input type="button" value="DELETE THIS LOG" onclick="window.location.href = '<?= LOCATION; ?>admin/logs/<?= get_var('type'); ?>/<?= get_var('file'); ?>/delete/';" style="width: 100%; text-align: center; background-color: #ff9999;"/>
			</p>

			<div>
			
<?php

	$filename = LOGS . get_var('type') . "/" . get_var('file');
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	
	echo nl2br($contents);

?>
		
			</div>
			
		</div>
	
	</div>
