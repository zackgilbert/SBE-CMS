<?php

	$plugin = post('plugin');
	
	if ($plugin === '_ALL_') {
		// install all uninstalled plugins
	} elseif (!is_plugin($plugin)) {
		echo "Sorry, plugin was not found.";
	} else {
		$installed = install_plugin($plugin);
		
		if ($installed === true) {
			echo 'true';
		} else {
			echo $installed;
		}
	}

?>