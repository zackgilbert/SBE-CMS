
	<div class="editable-region">
		<h4 class="editable-region-title">
			<label for="editable-<?= $num; ?>"><?= $title; ?></label>
		</h4>
		<input type="text" name="editable[<?= $num; ?>]" id="editable-<?= $num; ?>" value="<?= value('editable-' . $num, htmlentities($content)); ?>" class="field-title"/>
	</div>
