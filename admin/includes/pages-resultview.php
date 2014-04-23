<?php if (!isset($section)) $section = get_sitemap_section($section_id); ?>

		<li id="page-<?= $section['id']; ?>">
			
			<div class="select-container" onmouseover="selectSection(this);">
				<h3 class="section-name"><?= valid($section['name']); ?></h3>
				<dl>
					<dt>URL:</dt> 
					<dd><a href="<?= get_sitemap_section_url($section['id']); ?>"><?= get_sitemap_section_url($section['id']); ?></a></dd>
					<dt>File:</dt> 
					<dd><?= str_replace_once(ABSPATH, "", get_page_file_location($section)); ?></dd>
				</dl>
			</div>
			
			<div class="section-utilities">
				<a href="<?= LOCATION; ?>admin/pages/<?= $section['id']; ?>/">
					<img src="<?= LOCATION; ?>admin/images/btn-editpage.gif" alt="Edit Page" title="Click to Edit Page Content &amp; Settings"/>
				</a>
				<?php if (user_is_admin()) : ?> 
					<a href="javascript:;" onclick="deleteSitemapSection(this, '<?= $section['id']; ?>');">
						<img src="<?= LOCATION; ?>admin/images/btn-deletepage.gif" alt="Delete Page" title="Click to Delete This Page" />
					</a> 
				<?php endif; ?> 
			</div>
			
			<?php if (count($section['subsections']) > 0) : ?> 
				<ul class="sitemap-subsection">
				<?php foreach ($section['subsections'] as $subsection) : ?> 
					<?php load_include('pages-resultview', array('section' => $subsection)); ?> 
				<?php endforeach; ?> 
				</ul>
			<?php endif; ?> 
	
		</li>
