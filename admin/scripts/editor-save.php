<?php

	$file = request('file');
	$shouldVersion = (request('dontVersion') != 'true');
		
	$filename = ABSPATH . "sites/" . get_site() . "/" . get_theme() . "/" . $file;

	// we are editting a template's editable content...
	if (request('editable')) {

		if (!is_string($filename)) {
			failure("We were unable to find the file for this page. Try manually updating the file: " . $filename);
			redirect_failure();
		}
		
		$originalContent = get_file($filename);
		$originalContent = tagstolower($originalContent);
		$originalContent = str_replace("<!doctype", "<!DOCTYPE", $originalContent);

		// check to make sure there are changes and that we need to save this...
		// all you need to do is make sure that at least 1 of the new saved content isn't found in the original...
		$updates = request('editable');

		fix_files_superglobal();
		$files = (isset($_FILES['editable'])) ? $_FILES['editable'] : array();

		$wereChanges = false;

		foreach ($updates as $update) {
			if (empty($update) || (strpos($originalContent, $update) === false))
				$wereChanges = true;
		}

		if (!$wereChanges) {
			failure("We couldn't find any changes that were made, so we didn't update anything.");
			redirect_failure();	
		}

		$errors = array();

		// save a version of old content into database...
		if ($shouldVersion) {
			$versioned = $db->insert('table=>versions', array('sitemap_id' => 0, 'filename' => str_replace_once(ABSPATH, "", $filename), 'content' => $originalContent, 'created_by' => user('id')));

			if (!$versioned) {
				$errors[] = "There was an error saving the previous version of this template to the database.";
			}
		}
		
		require_once(LIBRARY . 'simple_html_dom.php');
		
		$a = str_replace(array("&mdash;", "<?", "?>", "></textarea>"), array("&amp;mdash;", "&lt;?", "?&gt;", ">SEENREPLACEME</textarea>"), $originalContent);
		$html = str_get_html($a);
		$query = "//[class*=editable]";
		$editableAreas = $html->find($query);
		$updatedContent = str_replace("&amp;mdash;", "&mdash;", $html);
		
		foreach ($editableAreas as $key => $editableArea) {
			// if it's been updated...
			if (get_editable_type($editableArea) == 'photo') {
				if (isset($files[$key]) && !empty($files[$key]['name'])) {
					$ftmp = $files[$key]['tmp_name'];
					$time = time();
					$uploadfile = upload_folder('pages', true) . strip_ext($files[$key]['name']) . '-' . $time . '.' . get_ext($files[$key]['name']);

					if (!($file_url = upload_media($files[$key], $uploadfile))) {
						$errors[] = "There was an error uploading your new photo (" . $files[$key]['name'] . ").";
					} else {
						//$updatedContent = str_replace($editableArea->asXML(), sprintf(get_editable_container($editableArea), str_replace(LOCATION, "", $file_url)), $updatedContent);
						$updatedContent = str_replace($editableArea->outertext, sprintf(get_editable_container($editableArea), str_replace_once(LOCATION, "", $file_url)), $updatedContent);
					}
				}
			} else {
				if (isset($updates[$key])) {
					// replace the original content with the new updated version...
					//$updatedContent = str_replace($editableArea->asXML(), sprintf(get_editable_container($editableArea), stripslashes($updates[$key])), $updatedContent);
					$updates[$key] = str_replace(LOCATION . "admin/editor/", "", $updates[$key]);
					$updatedContent = str_replace($editableArea->outertext, sprintf(get_editable_container($editableArea), stripslashes($updates[$key])), $updatedContent);
				}
			}
		}
		
		//$updatedContent = str_replace("<p>&nbsp;</p>", "", $updatedContent);
		$updatedContent = trim($updatedContent);
		
	} else {
		//$filename = $file;
		$updatedContent = stripslashes(request('content', ''));
		
		$errors = array();

		if ($shouldVersion && file_exists($filename) && (get_ext($filename) == '.php')) {
			$originalContent = get_file($filename);
			$originalContent = str_replace("<!doctype", "<!DOCTYPE", $originalContent);
			// save a version of old content into database...
			$versioned = $db->insert('table=>versions', array('sitemap_id' => 0, 'filename' => str_replace_once(ABSPATH, "", $filename), 'content' => $originalContent, 'created_by' => user('id')));

			if (!$versioned) {
				$errors[] = "There was an error saving the previous version of this template to the database.";
			}
		}
	}
	
	$updatedContent = tagstolower($updatedContent);
	$updatedContent = str_replace(array("<!doctype", "&amp;lt;?", "?&amp;gt;", "&lt;?", "?&gt;", ">SEENREPLACEME</textarea>"), array("<!DOCTYPE", "<?", "?>",  "<?", "?>", "></textarea>"), $updatedContent);
		
	// save the page file with new content
	if (file_put_contents($filename, $updatedContent)) {
		
		if (count($errors) < 1) {
			success("Template was successfully updated.");
			redirect_success();			
		} else if (count($errors) == 1) {
			failure($errors[0]);
			redirect_failure();
		} else {
			failure("There were multiple errors that occurred while updating this template.");
			redirect_failure();
		}
	} else {
		failure("There was an error updating the template with the new content. This is usually a permissions issue. Please make sure your template files have a permissions setting of 777.");
		redirect_failure();		
	}
	
?>