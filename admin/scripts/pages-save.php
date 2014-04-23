<?php

	$page = request('page');
	$section = get_sitemap_section($page);
	$file = request('file');
	$shouldVersion = (request('dontVersion') != 'true');
	$errors = array();

	if (!is_numeric($page) || !is_array($section)) {
		failure("We were unable to find the page you were looking for. This could be due to trying to accessing this page incorrectly.");
		redirect_failure();
	}
	
	// we are editting a page's content...
	if (!is_string($file)) {

		$filename = get_page_file_location($section);

		if (!is_string($filename)) {
			failure("We were unable to find the file for this page. Try manually updating the file: " . $filename);
			redirect_failure();
		}
		
		$originalContent = get_file($filename);
		$originalContent = tagstolower($originalContent);

		// check to make sure there are changes and that we need to save this...
		// all you need to do is make sure that at least 1 of the new saved content isn't found in the original...
		$updates = request('editable');

		fix_files_superglobal();
		$files = (isset($_FILES['editable'])) ? $_FILES['editable'] : array();

		$wereChanges = (is_array($files) && (count($files) > 0));

		foreach ($updates as $update) {
			if (empty($update) || (strpos($originalContent, $update) === false))
				$wereChanges = true;
		}

		if (!$wereChanges) {
			failure("We couldn't find any changes that were made, so we didn't update anything.");
			redirect_failure();	
		}

		// updated file with new content...
		require_once(LIBRARY . 'simple_html_dom.php');
		
		$a = str_replace(array("&mdash;", "<?", "?>", "></textarea>"), array("&amp;mdash;", "&lt;?", "?&gt;", ">SEENREPLACEME</textarea>"), $originalContent);
		$html = str_get_html($a);
		$query = "//[class*=editable]";
		$editableAreas = $html->find($query);
		$updatedContent = str_replace("&amp;mdash;", "&mdash;", $html->save());
		
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
						$new_file_url = (substr($file_url, 0, strlen(LOCATION)) == LOCATION) ? str_replace_once(LOCATION, "", $file_url) : $file_url;
						$updatedContent = str_replace($editableArea->outertext, sprintf(get_editable_container($editableArea), $new_file_url), $updatedContent);
					}
				}
			} else {
				if (isset($updates[$key])) {
					$updates[$key] = str_replace(array(LOCATION . "admin/pages/" . $page . "/", '../../uploads/'), array("", 'uploads/'), $updates[$key]);
					$updatedContent = str_replace(str_replace('&amp;mdash;', '&mdash;', $editableArea->outertext), sprintf(get_editable_container($editableArea), stripslashes($updates[$key])), $updatedContent);		
				}
			}
			$editableArea->clear();
			unset($editableArea);
			$editableAreas[$key]->clear();
			unset($editableAreas[$key]);
		}
		$html->clear();
		unset($html);
		
		$updatedContent = "\n" . trim($updatedContent) . "\n";
		
	} else if (is_string($file)) {
		$filename = $file;
		$updatedContent = stripslashes(request('content', ''));
		$originalContent = (file_exists($filename) && (get_ext($filename) == '.php')) ? get_file($filename) : false;
	}
	
	$updatedContent = tagstolower($updatedContent);
	$updatedContent = str_replace(array("&amp;lt;?", "?&amp;gt;", "&lt;?", "?&gt;", ">SEENREPLACEME</textarea>"), array("<?", "?>",  "<?", "?>", "></textarea>"), $updatedContent);
			
	// save the page file with new content
	if (file_put_contents($filename, $updatedContent)) {
		
		if ($shouldVersion && is_string($originalContent)) {
			// save a version of old content into database...
			$versioned = $db->insert('table=>versions', array('sitemap_id' => $section['id'], 'filename' => str_replace_once(ABSPATH, "", $filename), 'content' => $originalContent, 'created_by' => user('id')));

			if (!$versioned) {
				$errors[] = "There was an error saving the previous version of this page to the database.";
			}
		}
		
		if (count($errors) < 1) {
			success("Page's content was successfully updated.");
			redirect_success();			
		} else if (count($errors) == 1) {
			failure($errors[0]);
			redirect_failure();
		} else {
			failure("There were multiple errors that occurred while updating this page.");
			redirect_failure();
		}
	} else {
		failure("There was an error updating the file with the new content. This is usually a permissions issue. Please make sure your page files have a permissions setting of 777.");
		redirect_failure();		
	}
	
?>