
	<div class="editable-region">
		<h4 class="editable-region-title">
			<label for="editable-<?= $num; ?>"><?= $title; ?></label>
			<span class="toggle-editor"><a href="javascript:toggleEditorMode('editable-<?= $num; ?>');">Toggle: Rich Text or Raw HTML</a></span>
		</h4>
		<textarea name="editable[<?= $num; ?>]" id="editable-<?= $num; ?>" class="wysiwyg" rows="10" cols="60"><?= htmlentities(str_replace("<?= LOCATION; ?>", LOCATION, $content)); ?></textarea>
	</div>
	