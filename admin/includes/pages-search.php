<?php $section = get_sitemap_section($item['sitemap_id']); ?>

		<div id="pages-<?= $item['id']; ?>" class="pages">

			<h3><?= $section['name']; ?></h3>
			<a href="<?= LOCATION; ?>admin/manage/pages/<?= $item['id']; ?>/">Edit</a>
		</div>
