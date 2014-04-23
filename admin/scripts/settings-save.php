<?php

	/*
	**********************************************
	**
	**	File: admin/Flint/scripts/settings-save.php
	**	Creation Date: 11/4/08
	**	Description: Saves settings
	**	Called From: admin/Flint/pages/settings.php
	**
	**********************************************
	*/
	
	$which = post('which');
	$saved = true;

	if ($which == 'general') {
		// general
		$saved = true;
		$errors = array();
		
		// logo -- see if new file was uploaded. if it was, replace it and update database
		fix_files_superglobal();
		$logo = (isset($_FILES['settings']['logo'])) ? $_FILES['settings']['logo'] : array();
		if (isset($logo['tmp_name']) && is_string($logo['tmp_name']) && !empty($logo['tmp_name'])) {
			$ftmp = $logo['tmp_name'];
			$filename = str_replace(' ', '-', basename($logo['name']));
			//$uploaddir = chdir("../images/uploads");
			$uploadfile = upload_folder('logos', true) . strip_ext($filename);

			if ($logo['type'] == 'image/gif') {
				$fext = ".gif";
			} else if (($logo['type'] == 'image/jpeg') || ($logo['type'] == 'image/pjpeg')) {
				$fext = ".jpg";
			} else if ($logo['type'] == 'image/png') {
				$fext = ".png";
			} else {
				failure("We do not accept that type of file. Sorry. Please stick to images (gif, jpg or png).");
				redirect_success();
			}

			if (($file_url = upload_media($logo, $uploadfile . $fext))) {
				$media = array('type' => 'logo');
				$media['location'] = $file_url;
				//$media['location'] = $uploadfile . $fext;
				$saved = $db->insert('table=>media', $media);
				
				if (!$saved)
					$error[] = "There was an error saving your new logo to the database.";
			} else {
				$error[] = "There was an error uploading your new logo to the server.";
			}
		}
		
		// theme -- update database sites table
		$settings = post('settings');
		if (isset($settings['theme']) && is_string($settings['theme'])) {
			if ($settings['theme'] != get_theme()) {
				$saved = $db->update('table=>sites', 'id=>' . get_site_id(), array('theme' => $settings['theme']));
				if (!$saved)
					$errors[] = "There was an error changing the site's theme.";
					
				$sites = $db->get('table=>sites', 'where=>(`deleted_at` IS NULL)');
				if (is_array($sites)) {
					$fileToWrite = "";
					foreach ($sites as $site) {
						$site['subdomains'] = trim_explode(',', $site['subdomains']);
						$site['domains'] = trim_explode(',', $site['domains']);
						$_SITES[$site['name']] = $site;
						$fileToWrite .= "\t" . '$_SITES["' . $site['name'] . '"] = array("id" => ' . $site['id'] . ', "name" => "' . $site['name'] . '", "theme" =>"' . $site['theme'] . '", "subdomains" => array(' . ((count($site['subdomains']) > 0) ? "'" . join("', '", $site['subdomains']) . "'" : '') . '), "domains" => array(' . ((count($site['domains']) > 0) ? "'" . join("', '", $site['domains']) . "'" : '') . '));' . "\n";
					}
					save_setting_file(CONFIG . "sites.php", $fileToWrite);
				}
			}
		}
		
		if (count($errors) < 1) {
			success("Site settings have successfully been saved.");
		} else {
			if (count($errors) == 1) {
				failure($errors[0]);
			} else {
				failure("There were multiple errors while trying to save the site's settings.");				
			}
		}
		
	} else if ($which == 'metadata') {
		// if metadata, get post(metadata), and save to the database... delete config/metadata.php file
		$metadata = post('metadata');
		
		foreach ($metadata as $key => $value) {
			if ($saved) {
				$meta = array('name' => $key, 'value' => $value, 'deleted_at' => NULL);
				$rec = $db->getOne('table=>metadata', "where=>(`site_id` = " . site_id() . ") AND (`name` = '" . $db->escape($key) . "')");
				if (isset($rec['id'])) $meta['id'] = $rec['id'];
				$saved = $db->save('table=>metadata', $meta);
			}
		}
		
		if ($saved) {
			unlink(CONFIG . 'metadata.php');
			success("Site metadata has successfully been saved.");
		} else {
			failure("There was an error saving the site's metadata. You can manually update it by editing the config/metadata.php file.");
		}
		
	} else if ($which == 'debug') {
		// debug
		$debug = post("debug");
		
		$fileToWrite = "<?php\n\n";
		foreach ($debug as $key => $value) {
			if (!is_array($value)) $value = array($value);
			$fileToWrite .= "\t" . 'define("' . $key . '", (boolean)(' . join("|", $value) . '));' . "\n";
		}
		$fileToWrite .= "\n?>";
		
		if (unlink(CONFIG . "debug.php") && file_put_contents(CONFIG . "debug.php", $fileToWrite)) {
			success("Site Debug Settings have successfully been saved.");
		} else {
			failure("There was an error saving the site's debug settings. You can manually update them by editing the config/debug.php file.");
		}
	
	} else if ($which == 'apis') {
		// if apikeys, get post(apikeys), and save to the database... delete config/apikeys.php file
		$apikeys = post('apikeys');
		
		foreach ($apikeys as $key => $value) {
			if ($saved) {
				if (($key == 'new') && is_array($value)) {
					if (!empty($value['value']))
						$saved = $db->save('table=>apikeys', $value);
				} else {
					// either global items or site specific
					// site specific have an extra array layer
					if (isset($value['name']) && ($key == $value['name'])) { // global item
						$api = $value;
						$rec = $db->getOne('table=>apikeys', "where=>(`site_id` = 0) AND (`name` = '" . $db->escape($value['name']) . "')");
						if (isset($rec['id'])) $api['id'] = $rec['id'];
						if (empty($value)) {
							$saved = $db->delete('table=>apikeys', 'id=>' . $api['id']);
						} else {
							$saved = $db->save('table=>apikeys', $api);
						}
					} else {
						foreach ($value as $key2 => $value2) {
							$api = $value2;
							$rec = $db->getOne('table=>apikeys', "where=>(`site_id` = " . $db->escape(get_site_id($key)) . ") AND (`name` = '" . $db->escape($value2['name']) . "')");
							if (isset($rec['id'])) $api['id'] = $rec['id'];
							if (empty($value)) {
								$saved = $db->delete('table=>apikeys', 'id=>' . $api['id']);
							} else {
								$saved = $db->save('table=>apikeys', $api);
							}
						}
					}
				}
			}
		}

		if ($saved) {
			unlink(CONFIG . 'apikeys.php');
			get_api_key('__ALL__');
			success("Site API Keys have successfully been saved.");
		} else {
			failure("There was an error saving the site's API Keys. You can manually update them by editing the config/apikeys.php file.");
		}

	} else {
		// ERROR...
		failure("Invalid script call. Please contact an administrator to assist in solving this problem.");
	}
	
	redirect_success();

?>