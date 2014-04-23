
	function addAuthor(link, selectName) {
		$(link).parent().before('<div id="authors-loading"><img src="../images/icon-loading.gif" alt="Loading..."/></div>');
		//$(link).parent().before($(link).parent().parent().find('.author-container:first').html());
		$.post(root + 'admin/scripts/getauthors/?ajax', {}, 
			function(data, textStatus) {
				$('#authors-loading').replaceWith('<div class="author-container"><select name="' + selectName + '" style="clear:left;" class="author"><option value="">&nbsp;</option>' + data + '</select> <span class="deleteAuthor"><a href="javascript:;" onclick="deleteAuthor(this);"><img src="../images/btn-remove.gif" alt="Remove" title="Remove Author" /></a></span></div>');
			}
		);
		
	} // addAuthor
	
	function deleteAuthor(link) {
		$(link).parent().prev('select').andSelf().fadeOut('fast', function(){ $(this).remove();});
	} // deleteAuthor
	
	function addNewMedia(btn, type) {
		var count = 0;
		var name = $(btn.parentNode.parentNode).find('.media-item:last input:first').attr('name');
		if (name) {
			var stripped = name.substr(0, name.lastIndexOf(']['));
			count = 1+parseInt(stripped.substr(stripped.lastIndexOf('[')+1));
		}
		$(btn.parentNode).before('<div id="media-loader"><p>Loading...</p></div>');
		
		$.post(root + 'admin/includes/media-add-' + type + '/', 
			{ count : count }, 
			function(data, textStatus){
				$('#media-loader').replaceWith(data);
			}
		);
		if ((type == 'video') || (type == 'audio') || (type == 'slideshow')) {
			$(btn.parentNode).hide();
		} else if ((type == 'directoryphoto') && ($(btn.parentNode.parentNode).find('.media-item').size() >= 2)) {
			$(btn.parentNode).hide();
		}
	} // addNewMedia
	
	function cancelNewMedia(link) {
		$(link.parentNode.parentNode.parentNode).find('.media-add-container').show();
		$(link.parentNode.parentNode).remove();
	} // cancelNewMedia
	
	
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
	
	function deleteMedia(link, media_id) {
		if (confirm("Are you sure you want to delete this media item?")) {
			$.post(root + 'admin/media/delete/?ajax', 
				{ id : media_id }, 
				function(data, textStatus) {
					if (data == 'true') {
						$(link).parent().parent().fadeOut(function(){ $(this).remove() });
						$(link.parentNode.parentNode.parentNode).find('.media-add-container').show();
					} else {
						ajaxError(data);
					}
				}
			);
		}
		return false;
	} // deleteMedia
