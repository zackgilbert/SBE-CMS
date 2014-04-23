<?php

	$section = (is_numeric(get_var('id'))) ? get_sitemap_section(get_var('id')) : get_sitemap_root();

?>
	<div id="content">
	
		<div id="content-header">
			<p class="header-backtoall"><a href="<?= LOCATION; ?>admin/pages/">Back To All Pages</a></p>	
			<h2 class="header-title">
				Edit Your Pages 				
			</h2>
		</div>
		

		<div id="content-2colR-left">

			<h3 class="sitemap-title">Select a Page</h3>

			<ul class="sitemap-sidebar">
				<?php foreach (get_sitemap() as $sect) : ?>
					<?php load_include('pages-listview', array('section' => $sect)); ?> 
				<?php endforeach; ?>
			</ul>
			
			<?php if (user_is_admin()) : ?> 
			<ul class="sitemap-addpage">
				<li><a href="<?= LOCATION; ?>admin/includes/pages-add/" class="add-page"><img src="<?= LOCATION; ?>admin/images/btn-addpage.gif" alt="Add New Page" /></a></li>
			</ul>
			<?php endif; ?> 
			
		</div>

		<div id="content-2colR-right">

			<h2 class="page-title"><?= valid($section['name']); ?></h2>

			<ul class="page-navigation">
				<li<?= (get_var('subsection') == 'content') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/pages/<?= $section['id']; ?>/">Edit Page Content</a></li>
				<li<?= (get_var('subsection') == 'settings') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/pages/<?= $section['id']; ?>/settings/">Edit Page Settings</a></li>
				<?php if (user_is_admin()) : ?> 
				<li<?= (get_var('subsection') == 'html') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/pages/<?= $section['id']; ?>/html/">Edit Page HTML</a></li>
				<li<?= (get_var('subsection') == 'styles') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/pages/<?= $section['id']; ?>/styles/">Edit Page Styles</a></li>
				<li<?= (get_var('subsection') == 'delete') ? ' class="selected"' : ''; ?>><a href="<?= LOCATION; ?>admin/pages/<?= $section['id']; ?>/delete/" class="delete">Hide/Delete Page</a></li>
				<?php endif; ?> 
			</ul>

			<?php 				
				if (get_var('subsection') == 'content') {
					if ($_file = is_plugin_file($section['type'], 'admin/library')) {
						include($_file);
					} else if ($_file = is_plugin_file($section['type'], 'admin/index')) {
						include($_file);
					} else if ($_file = is_plugin_file($section['type'], 'admin/' . $section['type'])) {
						include($_file);
					} else if ($_file = is_plugin_file($section['type'], 'admin')) {
						include($_file);
					} else {
						load_include('pages-' . get_var('subsection'), array('section' => $section));
					}
				} else {
					load_include('pages-' . get_var('subsection'), array('section' => $section));
				}				
			?> 

		</div>
			
	</div>
