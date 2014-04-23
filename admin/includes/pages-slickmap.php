<?php if (!isset($section)) $section = get_sitemap_section(request('id')); ?>

		<li id="page-<?= $section['id']; ?>" class="<?php if (empty($section['url'])) echo 'home '; ?><?php if (is_string($section['disabled_at'])) echo 'disabled'; ?>">
			
			<div class="select-container" onclick="if (!$('.sitemap ul:first').hasClass('sortable')) window.location = '<?= LOCATION; ?>admin/pages/<?= $section['id']; ?>/';">
				<h4 class="section-location"><?= str_replace_once(LOCATION, "/", get_sitemap_section_url($section['id'])); ?></h4>
				<h3 class="section-name"><?= valid($section['name']); ?></h3>
			</div>
						
			<?php if (count($section['subsections']) > 0) : ?> 
				<ul>
				<?php foreach ($section['subsections'] as $subsection) : ?> 
					<?php load_include('pages-slickmap', array('section' => $subsection)); ?> 
				<?php endforeach; ?> 
				</ul>
			<?php endif; ?> 
	
		</li>
