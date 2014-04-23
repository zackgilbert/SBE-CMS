
	<div class="editable-region">
		<h4 class="editable-region-title">
			<label for="editable-<?= $num; ?>"><?= $title; ?></label>
		</h4>
		<textarea name="editable[<?= $num; ?>]" id="editable-<?= $num; ?>"><?= strip_tags($content); ?></textarea>
	</div>
