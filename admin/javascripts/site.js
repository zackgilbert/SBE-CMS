
	function ajaxError(msg) {
		alert(msg);
	} // ajaxError
	
	function installPlugin(btn, plugin, par) {
		$(btn).attr('value', 'Installing...').attr('disabled', 'disabled');
		$.post(root + 'admin/scripts/plugins-install/', { plugin : plugin }, function(data) {
			if (data == 'true') { 
				$(btn).parents(par + ':first').find('input').removeAttr('disabled');
				$(btn).parents(par + ':first').find('.install-btn').remove(); 
			} else { 
				$(btn).attr('value', 'Install Failed'); 
				ajaxError(data); 
			}
		});
	} // installPlugin

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
	} // deleteArticleMedia
	
	function selectNavItem(link, parent, item) {
		$('.content-nav ul li, ul.content-nav li').removeClass('selected');
		$('.content-items').hide();
		$('#'+parent+'-content-nav-'+item).addClass('selected');
		$('#'+parent+'-content-'+item).show();
	} // selectSubNavItem
	
	function toggleContainer(elm) {
		var src = $(elm).find('img').attr('src');
		var newsrc = (src.indexOf('icon-arrow-closed.gif') >= 0) ? src.replace(/icon-arrow-closed.gif/i, "icon-arrow-open.gif") : src.replace(/icon-arrow-open.gif/i, "icon-arrow-closed.gif")
		$(elm).find('img').attr('src', newsrc);
		$(elm.parentNode).find('.edit-item-content').toggle();
	} // toggleContainer
	
	$(function() {
		$('.edit-info').sheet({frameWidth: 520, frameHeight: 390});
	});
	