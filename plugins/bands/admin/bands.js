
	function addNewBandMedia(btn, type) {
		var count = 0;
		var name = $(btn.parentNode.parentNode).find('.media-item:last input:first').attr('name');
		if (name) {
			var stripped = name.substr(0, name.lastIndexOf(']['));
			count = 1+parseInt(stripped.substr(stripped.lastIndexOf('[')+1));
		}
		$(btn.parentNode).before('<div id="media-loader"><p>Loading...</p></div>');
		
		$.post(root + 'plugins/bands/admin/media-add-' + type + '/', 
			{ count : count }, 
			function(data, textStatus){
				$('#media-loader').replaceWith(data);
			}
		);
		/*if ((type == 'video')) {
			$(btn.parentNode).hide();
		} else if ((type == 'audio') && ($(btn.parentNode.parentNode).find('.media-item').size() >= 2)) {
			$(btn.parentNode).hide();
		}*/
	} // addNewBandMedia
	
	function cancelNewBandMedia(link) {
		$(link.parentNode.parentNode.parentNode).find('.media-add-container').show();
		$(link.parentNode.parentNode).remove();
	} // cancelNewBandMedia
	
	function deleteBandMedia(link, media_id) {
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
	} // deleteBandMedia
