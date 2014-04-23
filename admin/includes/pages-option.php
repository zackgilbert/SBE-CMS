	<option value="<?= $section['id']; ?>"<?= (value('page[section]', $default['id']) == $section['id']) ? ' selected="selected"' : ''; ?>><?= str_repeat("&nbsp;&nbsp;", $i); ?><?= $section['name']; ?></option>
	<?php foreach ($section['subsections'] as $sect) : ?> 
		<?php load_include('pages-option', array('section' => $sect, 'i' => $i+1, 'default' => $default)); ?> 
	<?php endforeach; ?>