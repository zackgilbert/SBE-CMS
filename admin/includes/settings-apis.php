
	<form method="post" action="<?= LOCATION; ?>admin/settings/save/" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>"/>
			<input type="hidden" name="which" value="<?= $settings; ?>"/>
		</div>

		<div class="setting-item">
			<h3 class="setting-item-title">API Keys</h3> 

			<?php if ($apis = get_api_key('__ALL__')) : ?> 
			<?php foreach ($apis as $key => $value) : ?> 

				<?php if (is_string($value)) : ?> 
			<div class="item-edit">
				<select name="apikeys[<?= $key; ?>][site_id]">
					<option value="0">All Sites</option>
					<?php foreach (get_sites() as $site) : ?> 
					<option value="<?= $site['id']; ?>"><?= $site['name']; ?></option>
					<?php endforeach; ?> 
				</select>
				<label for="apikeys-<?= $key; ?>"><?= capitalize($key); ?></label>
				<input type="hidden" name="apikeys[<?= $key; ?>][name]" value="<?= $key; ?>" />
				<input type="text" name="apikeys[<?= $key; ?>][value]" id="apikeys-<?= $key; ?>" value="<?= str_replace('"', '&quot;', $value); ?>" class="field-medium" />
			</div>
				<?php else : ?> 
					<?php foreach ($value as $key2 => $value2) : ?> 
			<div class="item-edit">
				<select name="apikeys[<?= $key; ?>][<?= $key2; ?>][site_id]">
					<option value="0">All Sites</option>
					<?php foreach (get_sites() as $site) : ?> 
					<option value="<?= $site['id']; ?>"<?= ($key == $site['name']) ? ' selected="selected"' : ''; ?>><?= $site['name']; ?></option>
					<?php endforeach; ?> 
				</select>
				<label for="apikeys-<?= $key; ?>-<?= $key2; ?>"><?= capitalize($key2); ?></label>
				<input type="hidden" name="apikeys[<?= $key; ?>][<?= $key2; ?>][name]" value="<?= $key2; ?>" />
				<input type="text" name="apikeys[<?= $key; ?>][<?= $key2; ?>][value]" id="apikeys-<?= $key; ?>-<?= $key2; ?>" value="<?= str_replace('"', '&quot;', $value2); ?>" class="field-medium" />
			</div>
					<?php endforeach; ?> 
				<?php endif; ?> 
			<?php endforeach; ?> 
			<?php endif; ?> 

			<div class="item-edit new-api-key" style="display: none;">
				<p>
					<select name="apikeys[new][site_id]">
						<option value="0">All Sites</option>
						<?php foreach (get_sites() as $site) : ?> 
						<option value="<?= $site['id']; ?>"><?= $site['name']; ?></option>
						<?php endforeach; ?> 
					</select>
				</p>
				<p>
					<label for="apikeys-new-name">New Key Name</label>
					<input type="text" name="apikeys[new][name]" id="apikeys-new-name" class="field-medium" />
				</p>
				<p>
					<label for="apikeys-new-value">New Key Value</label>
					<input type="text" name="apikeys[new][value]" id="apikeys-new-value" class="field-medium" />
				</p>
				<p>or <a href="javascript:;" onclick="toggleAPIKey(this);">Cancel</a></p>
			</div>

			<div class="new-api-btn">
				<input type="button" value="Add New API Key" onclick="toggleAPIKey(this);"/>
			</div>

		</div>
		
		<div class="setting-save">
			<input type="submit" value="Save Settings" class="btn-submit"/>
		</div>
	
	</form>
	
	<script type="text/javascript">
		
		function toggleAPIKey(btn) {
			$('.new-api-key').toggle();
			$('.new-api-btn').toggle();
		} // addAPIKey
		
	</script>
