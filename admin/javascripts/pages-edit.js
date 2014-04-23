
	function massAction(link, type, action, msg) {
		// get all ids of selected
		var ids = new Array();
		$('.page-content-list-table tr input:checked').each(function(i) {
			ids.push($(this).attr('value'));
		});
		
		if (ids.length < 1) {
			alert("No selected items were found. Please select the items you wish to be affected.");
			return false;
		}
		
		// confirm that this is what the user wants to do.
		if (confirm(msg)) {
			// post to script
			$.post(root + 'admin/scripts/mass-action/', { type : type , action : action , ids : ids }, function(data, textStatus) {
				if (data == 'true') {
					window.location.href = window.location.href;
				} else {
					ajaxError(data);
				}
			});
		}
		return false;
	} // massAction
	
	$(function() {
		$('.add-page').sheet({frameWidth: 520, frameHeight: 425});
		$('.page-content-list-table :checkbox').bind('change', function() {
			if ($(this).attr('checked')) {
				$(this).parents('tr:first').addClass('selected');
			} else {
				$(this).parents('tr:first').removeClass('selected');
			}
		});
	});
