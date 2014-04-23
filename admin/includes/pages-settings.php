
	<form method="post" action="<?= LOCATION; ?>admin/sitemap/save/" enctype="multipart/form-data">
	
		<div>
			<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>" />
			<input type="hidden" name="sitemap[id]" value="<?= $section['id']; ?>" /> 
			<input type="hidden" name="sitemap[parent_id]" value="<?= $section['parent_id']; ?>" />
			<input type="hidden" name="sitemap[order]" value="<?= $section['order']; ?>" />
		</div>
		
		<h3 class="page-settings-title">Page Information</h3>
		
		<div class="page-settings-group">

			<div class="setting-item">
				<h4><label for="name">Page Name</label></h4>
				<p>The is how the page will be displayed in the navigation, and the title bar.</p>
				<input type="text" class="sitemap-section-name field-setting" id="name" name="sitemap[name]" value="<?= value('sitemap[name]', valid($section['name'])); ?>" />
			</div>			
			<div class="setting-item">
				<h4><label for="url">Page URL</label></h4>
				<p>This is how this page will be displayed in the address bar, so keep it simple, and don't use spaces or crazy characters.</p>
				<input type="text" class="sitemap-section-url field-setting" id="url" name="sitemap[url]" value="<?= value('sitemap[url]', $section['url']); ?>"/>
				<span class="description">
					Full URL will be: http://<?= $_SERVER['SERVER_NAME']; ?><?= get_sitemap_section_url($section['parent_id']); ?><?= $section['url']; ?>
				</span>
			</div>
			<div class="setting-item">
				<h4><label for="prefix">File Name</label></h4>
				<p>This is used to assign what files are used to display the information for this section. Leave blank to use default file name.</p>
				<input type="text" class="sitemap-section-prefix field-setting" id="prefix" name="sitemap[prefix]" value="<?= value('sitemap[prefix]', $section['prefix']); ?>"/> 
				<span class="description">
					File Location will be: <?= str_replace_once(ABSPATH, "", get_page_file_location($section)); ?> (<?= (file_exists(get_page_file_location($section))) ? 'FOUND' : 'NOT FOUND'; ?>)
				</span>
			</div>
			<div class="setting-item">
				<h4><label for="template">Template</label></h4>
				<p>This is used to assign which template file is used for the page. This allows you to change the overall look and feel of the page.</p>
				<select name="sitemap[template]" id="template" class="setting">
					<option value="DEFAULT"<?= ($section['template'] == 'DEFAULT') ? ' selected="selected"' : ''; ?>>Default Template</option>
					<option value=""<?= ($section['template'] == '') ? ' selected="selected"' : ''; ?>>No Template</option>
					<?php foreach (get_site_templates() as $template) : ?> 
					<option value="<?= $template; ?>"<?= ($section['template'] == $template) ? ' selected="selected"' : ''; ?>><?= $template; ?></option>
					<?php endforeach; ?> 
				</select>
				<!--<input type="text" class="sitemap-section-template field-medium" id="template" name="sitemap[template]" value="<?= value('sitemap[template]', $section['template']); ?>"/>-->
			</div>	
			
			<?php if (($page_types = get_page_type_plugins()) && (count($page_types) > 0)) : ?> 
			<div class="setting-item">
				<h4><label>Page Type</label></h4>
				<p>Choose what type of content this page will have.</p>

				<select name="sitemap[type]" class="type">
					<option value="static"<?= ($section['type'] == 'static') ? ' selected="selected"' : ''; ?>> Static Html</option>
					<?php foreach ($page_types as $type) : ?> 
					<option value="<?= $type; ?>"<?= ($section['type'] == $type) ? ' selected="selected"' : ""; ?>><?= capitalize($type); ?></option>
					<?php endforeach; ?> 
				</select>
				
				<?php if (is_plugin($section['type']) && !plugin_is_installed($section['type'])) : ?> 
				<div>
					There was an error trying to install this plugin. Please try manually installing.
				</div>
				<?php endif; ?>
				
				<?php /*?><ul>
					<li>
						<label><input type="radio" name="sitemap[type]" value="static"<?= ($section['type'] == 'static') ? ' checked="checked"' : ''; ?>/> Static Html Page</label>
					</li>
					<?php foreach ($page_types as $type) : ?> 
					<li>
						<label><input type="radio" name="sitemap[type]" value="<?= $type; ?>"<?= ($section['type'] == $type) ? ' checked="checked"' : ""; ?><?= (!plugin_is_installed($type)) ? ' disabled="disabled"' : ''; ?>/> <?= capitalize($type); ?></label>
						<?php if (!plugin_is_installed($type)) : ?> 
						<span class="install-btn"><input type="button" value="Install" onclick="installPlugin(this, '<?= $type; ?>', 'li');"/></span>
						<?php endif; ?> 
					</li>
					<?php endforeach; ?> 
				</ul>*/?>
			</div>
			<?php endif; ?> 
				
		</div>
		
		<h3 class="page-settings-title">Additional Options</h3>
		
		<div class="page-settings-group">
			
			<div class="setting-item">				
				<h4>Include In Navigation</h4>	
				<label><input type="radio" name="sitemap[inNav]" value="1"<?= checked('sitemap[inNav]', '1', ($section['inNav']=='1')); ?>/> Yes</label>
				<label><input type="radio" name="sitemap[inNav]" value="0"<?= checked('sitemap[inNav]', '0', ($section['inNav']=='0')); ?>/> No</label>
			</div>
			<?php if ($section['type'] != 'static') : ?> 
			<div class="setting-item">				
				<h4>Comments</h4>	
				<?php if (!plugin_is_installed('comments')) : ?> 
				<p class="install-btn">The comments plugin is not yet installed. <span><input type="button" value="Install" onclick="installPlugin(this, 'comments', 'div');"/></span></p>
				<?php endif; ?> 
				<label><input type="radio" name="sitemap[comments]" value="enable"<?= checked('sitemap[comments]', 'enable', ($section['comments']=='enable')); ?><?= (!plugin_is_installed('comments')) ? ' disabled="disabled"' : ''; ?>/> Enabled</label>
				<label><input type="radio" name="sitemap[comments]" value="disable"<?= checked('sitemap[comments]', 'disable', ($section['comments']=='disable')); ?><?= (!plugin_is_installed('comments')) ? ' disabled="disabled"' : ''; ?>/> Disabled</label>
			</div>
			<?php endif; ?> 
			<div class="setting-item">	
				<h4><label for="stylesheets">Additional Stylesheets</label></h4>
				<p>This is used to load additional stylesheets for this page. Comma delimited, 255 character limit.</p>
				<textarea name="sitemap[stylesheets]" id="stylesheets" rows="4" cols="40"><?= value('sitemap[stylesheets]', $section['stylesheets']); ?></textarea>
			</div>
			<div class="setting-item">	
				<h4><label for="javascripts">Additional Javascripts</label></h4>
				<p>This is used to load additional javascripts for this page. Comma delimited, 255 character limit.</p>
				<textarea name="sitemap[javascripts]" id="javascripts" rows="4" cols="40"><?= value('sitemap[javascripts]', $section['javascripts']); ?></textarea>
			</div>
			<div class="setting-item">	
				<h4><label for="keywords">Additional Keywords</label></h4>
				<p>This is used to load additional keywords for this page's metadata. They will be prepended to the already existent site keywords. Comma delimited, 255 character limit.</p>
				<textarea name="sitemap[keywords]" id="keywords" rows="4" cols="40"><?= value('sitemap[keywords]', stripslashes($section['keywords'])); ?></textarea>
			</div>
			<div class="setting-item">	
				<h4><label for="description">Additional Description</label></h4>
				<p>This is used to load an additional description for this page. It will be prepended to the already existent site description. 255 character limit.</p>
				<textarea name="sitemap[description]" id="description" rows="4" cols="40"><?= value('sitemap[description]', stripslashes($section['description'])); ?></textarea>
			</div>				
							
		</div>
		
		<div id="content-editable-save">
			<input type="submit" value="Save Page Settings" class="btn-save"/>
		</div>

	</form>
