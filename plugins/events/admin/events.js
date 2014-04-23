
	
	function deleteContent(link) {
		var script_url = link.getAttribute('href');
		if (script_url != 'javascript:;') {
			if (confirm('Are you sure you want to delete this item?')) {
		
				$.get(script_url + '?ajax', 
					{},
					function(data, textStatus) {
						if (data == 'true') {
							$(link.parentNode.parentNode).fadeOut('fast');
						} else {
							ajaxError(data);
						}
					}
				);
			
			}
		}
		
		return false;
	} // deleteContent

	function deleteFile(link, filename, thumb) {
		if (confirm("Are you sure you want to delete this file?")) {
			$.post(root + 'admin/file/delete/?ajax', 
				{ file : filename , thumb : thumb }, 
				function(data, textStatus) {
					if (data == 'true') {
						$(link.parentNode.parentNode).fadeOut(function() { $(this).remove(); });
						$(link.parentNode.parentNode.parentNode).find('.media-add-container').show();
					} else {
						ajaxError(data);
					}
				}
			);
		}
		return false;
	} // deleteFile
