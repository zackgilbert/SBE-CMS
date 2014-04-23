
	<li class="<?php if ($section['id'] == get_var('id')) echo 'selected '; ?><?php if (is_string($section['disabled_at'])) echo 'disabled'; ?>">
		<img src="<?= LOCATION; ?>admin/images/icon-sitemap-sidebar-page.gif" alt="" />
		<a href="<?= LOCATION; ?>admin/pages/<?= $section['id']; ?>/<?= (get_var('subsection') != 'content') ? get_var('subsection') . '/' : ''; ?>"><?= $section['name']; ?></a>
		<?php if (count($section['subsections']) > 0) : ?> 
		<ul>
		<?php foreach ($section['subsections'] as $sub) : ?> 
			<?php load_include('pages-listview', array('section' => $sub)); ?> 
		<?php endforeach; ?> 
		</ul>
		<?php endif; ?> 
	</li>
