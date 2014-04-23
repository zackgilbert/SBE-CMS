
	<div class="editable-region">
		<h4 class="editable-region-title">
			<label for="editable-<?= $num; ?>"><?= $title; ?></label>
		</h4>
		<img src="<?= str_replace("<?= LOCATION; ?>", LOCATION, $content); ?>" alt="<?= $title; ?>" title="<?= $title; ?>"/>
		<label>Upload New Photo:</label> <input type="file" id="editable-<?= $num; ?>" name="editable[<?= $num; ?>]"/>
	</div>
