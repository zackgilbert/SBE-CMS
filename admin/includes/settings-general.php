
	<form method="post" action="<?= LOCATION; ?>admin/settings/save/" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>"/>
			<input type="hidden" name="which" value="<?= $settings; ?>"/>
		</div>
	
		<div class="setting-item">
			<h3 class="setting-item-title">General Site Settings</h3> 

			<div class="item-edit">
				<label for="settings-logo">Logo: </label>
				<img src="<?= get_logo(); ?>" alt="logo"/><br/>
				<input type="file" id="settings-logo" name="settings[logo]" />
			</div>

			<div class="item-edit">
				<label for="settings-theme">Site Theme: </label> 
				<select id="settings-theme" name="settings[theme]" class="default-template"> 
				<?php foreach (get_site_themes() as $theme) : ?> 
					<option value="<?= $theme; ?>"<?= ($theme == get_theme()) ? ' selected="selected"' : ''; ?>><?= $theme; ?></option>
				<?php endforeach; ?> 
				</select>
			</div>

			<?php /*?><div class="item-edit">
				<input type="hidden" name="settings[FORCE_SLASH]" value="false" />
				<label class="checkbox"> 
				<input type="checkbox" name="settings[FORCE_SLASH]" value="true"<?= (FORCE_SLASH) ? ' checked="checked"': ''; ?>/>
				Force URLs to Append a Trailing Slash ('/')
				</label>
			</div>*/ ?>

		</div>
		
		<div class="setting-save">
			<input type="submit" value="Save Settings" class="btn-submit"/>
		</div>
	
	</form>
