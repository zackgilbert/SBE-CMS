
	<div id="content">
	
		<div id="content-header">
			<p class="header-section">File Editor</p>	
			<h2 class="header-title">
				Edit Templates and Stylesheets 				
			</h2>
		</div>
		
		<div id="content-2colR-left">

			<h3 class="files-title">Select a Template</h3>
			<ul class="files-templates">
				<?php foreach ($templates as $template) : ?> 
					<li<?= (isset($filename) && ('templates/'.$template == $filename)) ? ' class="selected"' : ''; ?>><a href="?template=<?= $template; ?>"><?= $template; ?></a></li>
				<?php endforeach; ?> 
				<?php if (count($templates) < 1) : ?> 
					<li class="none">Your Site Has No Templates</li>
				<?php endif; ?> 
			</ul>
	
			<h3 class="files-title">Select a Stylesheet</h3>
			<ul class="files-stylesheets">
				<?php foreach ($stylesheets as $stylesheet) : ?> 
					<li<?= (isset($filename) && ('stylesheets/'.$stylesheet == $filename)) ? ' class="selected"' : ''; ?>><a href="?stylesheet=<?= $stylesheet; ?>"><?= $stylesheet; ?></a></li>
				<?php endforeach; ?> 
				<?php if (count($stylesheets) < 1) : ?> 
					<li class="none">Your Site Has No Stylesheets</li>
				<?php endif; ?> 
			</ul>
			
		</div>

		<div id="content-2colR-right">
		
			<?php if (!isset($file) || !is_string($file)) : ?> 
			
				<div id="choosefile">
					<h4>Select a File to Edit</h4>
					<p>
						NOTE: Any changes to these files will affect your site. If you aren't familiar with editing HTML, PHP, or CSS, please consult your web developer.
					</p>
				</div>
			
			<?php elseif (is_array($editableAreas) && (!isset($_GET['source']))) : ?>
			
			<?php if (false) : //$versions = previous_edits($filename)) : ?> 
			<div id="versions-loadprevious">
				<p class="currentversion">
					Current Version: v.<?= pad(1+count($versions)); ?> last updated on <?= format_date(filemtime(ABSPATH . 'sites/' . get_site() . '/' . get_theme() . '/' . $filename), 'M j, Y'); ?> 
				</p>
				<?php if (count($versions) > 0) : ?> 
					<p class="previousversion">
						View Previous Versions: 
						<select onchange="if (this.value>0) window.location.href='<?= LOCATION; ?>admin/editor/versions/'+this.value+'/';" class="version">
							<option value="0">&nbsp;</option>
							<?php for ($i=count($versions)-1; $i>=0; $i--) : ?> 
							<option value="<?= $versions[$i]['id']; ?>">v.<?= pad($i+1); ?></option>
							<?php endfor; ?> 
						</select>
					</p>
				<?php endif; ?> 
			</div>
			<?php endif; ?> 
				
			<form method="post" action="<?= LOCATION; ?>admin/editor/save/" enctype="multipart/form-data">

				<div>
					<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>" />
					<input type="hidden" name="file" value="<?= $filename; ?>"/>
				</div>
				
				<div id="file-editor">
					<h3 class="file-editor-filename">
						<?= str_replace(ABSPATH, "", $filename); ?> 
						<?php if (get('template')) : ?> 
						<span><a href="<?= LOCATION; ?>admin/editor/?template=<?= get('template'); ?>&amp;source">View HTML</a></span>
						<?php endif; ?> 
					</h3>

					<div id="content-editable-regions">
				
				<?php
				
					foreach ($editableAreas as $key => $editableArea) {
						$type = get_editable_type($editableArea);
						$content = get_editable_content($editableArea);
						$content = convert_smart_quotes(str_replace("&amp;mdash;", "&mdash;", $content));
						$title = get_editable_title($editableArea, ('Editable Area #' . (1+$key)));
			
						load_include("editable-" . $type, array('num' => $key, 'title' => $title, 'content' => $content));
					}
				
				?>
					
					</div>
					
				</div>
				
				<div id="file-editor-save">
					<input type="submit" value="Save Changes" class="btn-save" />
					<?php /*?><div class="save-noversion">
						<label><input type="checkbox" name="dontVersion" value="true"/> I only made small changes, don't save a new version.</label>
					</div>*/?>
				</div>

			</form>
				
			<?php else : ?> 
			
			<form method="post" action="<?= LOCATION; ?>admin/editor/save/" enctype="multipart/form-data">
				<div>
					<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>" />
					<input type="hidden" name="file" value="<?= $filename; ?>" /> 
				</div>
							
				<div id="file-editor">
					<h3 class="file-editor-filename">
						<?= str_replace(ABSPATH, "", $filename); ?> 
						<?php if (get('template') && is_array($editableAreas)) : ?> 
						<span><a href="<?= LOCATION; ?>admin/editor/?template=<?= get('template'); ?>">View Editable Regions</a></span>
						<?php endif; ?>
					</h3>
					<textarea name="content" id="editor-container" class="editor codepress autocomplete-off <?= (strpos($filename, 'stylesheets') !== false) ? 'css' : 'php'; ?>" rows="10" cols="60"><?= htmlentities($file); ?></textarea>
				</div>
				
				<div id="file-editor-save">
					<input type="submit" value="Save Changes" class="btn-save" />
					<?php /*?><div class="save-noversion">
						<label><input type="checkbox" name="dontVersion" value="true"/> I only made small changes, don't save a new version.</label>
					</div>*/?>
				</div>
			
			</form>
			
			<?php endif; ?> 
		
		</div>		

	</div>
	
	<script type="text/javascript" src="<?= LOCATION; ?>admin/javascripts/codepress/codepress.js"></script>
