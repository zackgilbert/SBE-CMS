
	<h3>Log Manager</h3>
	<p><a href="<?= LOCATION; ?>admin/logs/">Back</a></p>
		
	<div>
	
		<h4>Editing Log File: /logs/<?= get_var('type'); ?>/<?= get_var('file'); ?> (<?= filesize(LOGS . get_var('type') . '/' . get_var('file')); ?> bytes)</h4>

		<p>
			<input type="button" value="DELETE THIS LOG" onclick="window.location.href = '<?= LOCATION; ?>admin/logs/<?= get_var('type'); ?>/<?= get_var('file'); ?>/delete/';" style="width: 100%; text-align: center; background-color: #ff9999;"/>
		</p>

		<div>
	
			<form method="post" action="<?= LOCATION; ?>admin/logs/<?= get_var('type'); ?>/<?= get_var('file'); ?>/save/" enctype="multipart/form-data">
				<input type="hidden" name="redirect[success]" value="<?= LOCATION; ?>admin/account/tools/logs/"/>
				<input type="hidden" name="redirect[failure]" value="<?= $_SERVER['REQUEST_URI']; ?>"/>
			
				<p>
					<textarea name="file" style="width: 100%; height: 300px;">
<?php

	$filename = LOGS . get_var('type') . "/" . get_var('file');
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	
	echo $contents;

?>
					</textarea>
				</p>
				
				<p>
					<input type="submit" name="continue" value="Save and Continue Editing" /> <input type="submit" name="save" id="submit" value="Save" />
				</p>
				
			</form>
			
		</div>
	
	</div>
	