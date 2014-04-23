
	<form method="post" action="<?= LOCATION; ?>admin/settings/save/" enctype="multipart/form-data">
		<div>
			<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>"/>
			<input type="hidden" name="which" value="<?= $settings; ?>"/>
		</div>
		
		<div class="setting-item">
			<h3 class="setting-item-title">Flint Debug Settings</h3> 

				<div class="item-checkbox">
					<input type="hidden" name="debug[SHOW_FLINT_ERRORS]" value="false" />
					<label class="checkbox"> 
						<input type="checkbox" name="debug[SHOW_FLINT_ERRORS]" value="true"<?= (SHOW_FLINT_ERRORS) ? ' checked="checked"': ''; ?> />
						Show Flint Errors
					</label>
				</div>

				<div class="item-checkbox">				
					<input type="hidden" name="debug[SHOW_FLINT_WARNINGS]" value="false" />
					<label class="checkbox">
						<input type="checkbox" name="debug[SHOW_FLINT_WARNINGS]" value="true"<?= (SHOW_FLINT_WARNINGS) ? ' checked="checked"': ''; ?> />
						Show Flint Warnings
					</label>				
				</div>
		</div>

		<div class="setting-item">
			<br /><br />
			<h3 class="setting-item-title">Site Debug Settings</h3>

				<div class="item-checkbox">
					<input type="hidden" name="debug[DEBUG]" value="false" />
					<label>
						<input type="checkbox" name="debug[DEBUG]" value="true"<?= (DEBUG) ? ' checked="checked"': ''; ?> onchange="debugDependant(this);" />
						Total Debug
					</label>
				</div>

				<div class="item-checkbox">
					<input type="hidden" name="debug[DEBUG_SQL][dependent_on]" value="DEBUG" />
					<input type="hidden" name="debug[DEBUG_SQL][value]" value="false" />
					<label>
						<input type="checkbox" name="debug[DEBUG_SQL][value]" value="true"<?= (DEBUG_SQL) ? ' checked="checked"': ''; ?><?= (DEBUG) ? ' disabled="disabled"': ''; ?>  class="debug-dependant" />
						SQL Debug
					</label>
				</div>

				<div class="item-checkbox">
					<input type="hidden" name="debug[DEBUG_JAVASCRIPTS][dependent_on]" value="DEBUG" />
					<input type="hidden" name="debug[DEBUG_JAVASCRIPTS][value]" value="false" />
					<label>
						<input type="checkbox" name="debug[DEBUG_JAVASCRIPTS][value]" value="true"<?= (DEBUG_JAVASCRIPTS) ? ' checked="checked"': ''; ?><?= (DEBUG) ? ' disabled="disabled"': ''; ?>  class="debug-dependant" />
						Javascripts
					</label>
				</div>

				<div class="item-checkbox">		
					<input type="hidden" name="debug[DEBUG_STYLESHEETS][dependent_on]" value="DEBUG" />
					<input type="hidden" name="debug[DEBUG_STYLESHEETS][value]" value="false" />
					<label>
						<input type="checkbox" name="debug[DEBUG_STYLESHEETS][value]" value="true"<?= (DEBUG_STYLESHEETS) ? ' checked="checked"': ''; ?><?= (DEBUG) ? ' disabled="disabled"': ''; ?>  class="debug-dependant" />
						Stylesheets					
					</label>
				</div>

		</div>
		
		<div class="setting-save">
			<input type="submit" value="Save Settings" class="btn-submit"/>
		</div>
	
	</form>
	
	<script type="text/javascript">
	
		function debugDependant(cb) {
			if (cb.checked) {
				$('.debug-dependant').attr('disabled', 'disabled').attr('checked', 'checked');
			} else {
				$('.debug-dependant').attr('disabled', false);
			}
		}
	
	</script>
