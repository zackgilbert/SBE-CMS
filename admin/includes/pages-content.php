<?php $filename = get_page_file_location($section); ?>

	<?php if ($versions = previous_edits($section)) : ?> 
	<div id="versions-loadprevious">
		<p class="currentversion">
			Current Version: v.<?= pad(1+count($versions)); ?> last updated on <?= format_date(filemtime($filename), 'M j, Y'); ?> 
		</p>
		<?php if (count($versions) > 0) : ?> 
			<p class="previousversion">
				View Previous Versions: 
				<select onchange="if (this.value>0) window.location.href='<?= LOCATION; ?>admin/pages/versions/'+this.value+'/';" class="version">
					<option value="0">&nbsp;</option>
					<?php for ($i=count($versions)-1; $i>=0; $i--) : ?> 
					<option value="<?= $versions[$i]['id']; ?>">v.<?= pad($i+1); ?></option>
					<?php endfor; ?> 
				</select>
			</p>
		<?php endif; ?> 
	</div>
	<?php endif; ?> 

	<form method="post" action="<?= LOCATION; ?>admin/pages/save/" enctype="multipart/form-data">
	
		<div>
			<input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI']; ?>" />
			<input type="hidden" name="page" value="<?= $section['id']; ?>" /> 
		</div>
			
		<?php if (file_exists($filename)) : ?>
			
			<div id="content-editable-regions">
			
			<?php
				
				$a = str_replace(array("&mdash;", "<?", "?>", "></textarea>"), array("&amp;mdash;", "&lt;?", "?&gt;", ">SEENREPLACEME</textarea>"), get_file($filename));
				
				$html = str_get_html($a);
				
				//$query = "//*[contains(concat(' ',normalize-space(@class),' '),' editable ') or contains(concat(' ',normalize-space(@class),' '),' editable-html ') or contains(concat(' ',normalize-space(@class),' '),' editable-title ') or contains(concat(' ',normalize-space(@class),' '),' editable-photo ') or contains(concat(' ',normalize-space(@class),' '),' editable-text ')]";
				$query = "//[class*=editable]";
				$editableAreas = $html->find($query);
				
				//$xml = simplexml_load_string(trim("<div>".$a."</div>"));
				
				//if (!$xml) : 
				if (!is_array($editableAreas)) : 
			?>
				
				<div id="editable-noregions">
					<h4>There was an error loading this page as editable.</h4>
					<h5>This usually happens after an edit because of a code error or poor formatting.</h5>
					<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/html/"><img src="images/btn-edithtml.gif" alt="Edit HTML" title="Edit the HTML for this Page" /></a>
				</div>
			
			<?php elseif (count($editableAreas) < 1) : ?>

				<div id="editable-noregions">
					<h4>This page doesn't appear to have any editable content.</h4>
					<h5>To make content editable, add the class <em><strong>'editable'</strong></em> to any HTML tags on this page you want to edit.</h5>
					<a href="<?= LOCATION; ?>admin/pages/<?= get_var('id'); ?>/html/"><img src="images/btn-edithtml.gif" alt="Edit HTML" title="Edit the HTML for this Page" /></a>
				</div>

			<?php
			 	else :
					/*$query = "//*[contains(concat(' ',normalize-space(@class),' '),' editable ') or contains(concat(' ',normalize-space(@class),' '),' editable-html ') or contains(concat(' ',normalize-space(@class),' '),' editable-title ') or contains(concat(' ',normalize-space(@class),' '),' editable-photo ') or contains(concat(' ',normalize-space(@class),' '),' editable-text ')]";
					$editableAreas = $xml->xpath($query);
				
					foreach ($editableAreas as $key => $editableArea) {
						$type = get_editable_type($editableArea);
						$content = get_editable_content($editableArea);
						$content = str_replace("&amp;mdash;", "&mdash;", $content);
						$title = get_editable_title($editableArea, ('Editable Area #' . (1+$key)));
			
						load_include("editable-" . $type, array('num' => $key, 'title' => $title, 'content' => $content));
					} */
					foreach ($editableAreas as $key => $editableArea) {
						$type = get_editable_type($editableArea);
						$content = get_editable_content($editableArea);
						$content = convert_smart_quotes(str_replace("&amp;mdash;", "&mdash;", $content));
						$title = get_editable_title($editableArea, ('Editable Area #' . (1+$key)));
			
						load_include("editable-" . $type, array('num' => $key, 'title' => $title, 'content' => $content));
					}
				
			?>
				<?php endif; ?>	
			</div>
			
		<?php else : ?> 
			
			<div id="editable-nofile">
				<h4>We couldn't find this Page.</h4>
				<h5>There is no file associated with this Page. Please create a PHP file named <em><strong><?= $filename; ?></strong></em> and upload it to the <em><strong>/Pages/</strong></em> directory on the server.</h5>
			</div>
			
		<?php endif; ?> 	
		
		<?php if (file_exists($filename) && (count($editableAreas) > 0)) : ?> 
			
		
			<div id="content-editable-save">
				<input type="submit" value="Save Page Content" class="btn-save"/>
				<div class="save-noversion">
					<label><input type="checkbox" name="dontVersion" value="true"/> I only made small changes, don't save a new version.</label>
				</div>
			</div>
			
		<?php endif; ?>
	
	</form>
