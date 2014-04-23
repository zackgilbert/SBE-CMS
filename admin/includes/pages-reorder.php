<?php if (!isset($section)) $section = get_sitemap_section($section_id); ?>

		<li id="page-<?= $section['id']; ?>" class="<?= (count($section['subsections']) > 0) ? ' sm2_liOpen' : ''; ?>">
			<dl class="sm2_s_published">
				<dt><?= valid($section['name']); ?></dt>
				<!--<dd class="sm2_actions"></dd>-->
			</dl>
		
			<?php if (count($section['subsections']) > 0) : ?> 
			<ul>
			<?php foreach ($section['subsections'] as $subsection) : ?> 
				<?php load_include('pages-reorder', array('section' => $subsection)); ?> 
			<?php endforeach; ?> 
			</ul>
			<?php endif; ?> 
	
		</li>
		