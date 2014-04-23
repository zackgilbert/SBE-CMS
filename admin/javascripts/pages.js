
	function ajaxError(message) {
		alert(message);
	}
	
	function is_html(data) {
		return (data.replace(/^\s*/, "").replace(/\s*$/, "").substr(0, 1) == '<');
	} // is_html
	
	/*** BASED UPON: http://boagworld.com/development/creating-a-draggable-sitemap-with-jquery ***/
	var sitemapHistory = {
	    stack: new Array(),
	    temp: null,
	    //takes an element and saves it's position in the sitemap.
	    //note: doesn't commit the save until commit() is called!
	    //this is because we might decide to cancel the move
	    saveState: function(item) {
	        sitemapHistory.temp = { item: $(item), itemParent: $(item).parent(), itemAfter: $(item).prev() };
	    },
	    commit: function() {
	        if (sitemapHistory.temp != null) sitemapHistory.stack.push(sitemapHistory.temp);
	    },
	    //restores the state of the last moved item.
	    restoreState: function() {
	        var h = sitemapHistory.stack.pop();
	        if (h == null) return;
	        if (h.itemAfter.length > 0) {
	            h.itemAfter.after(h.item);
	        }
	        else {
	            h.itemParent.prepend(h.item);
	        }
			//checks the classes on the lists
			$('.sitemap li.sm2_liOpen').not(':has(li)').removeClass('sm2_liOpen');
			$('.sitemap li:has(ul li):not(.sm2_liClosed)').addClass('sm2_liOpen');
	    }
	}

	//init functions
	$(function() {
		$('.add-page').sheet({frameWidth: 520, frameHeight: 425});
		
		initSort();
	});
	
	function initSort() {
		$('.sitemap ul:first li').draggable('destroy');
		$('.sitemap li .dropzone').remove();
		$('.sitemap li:not(.home)').prepend('<div class="dropzone"></div>');
		$('.sitemap ul').each(function(i) { $(this).children('li:last').prepend('<div class="dropzone after"></div>'); });
		//$('.sitemap ul:first > li:last').prepend('<div class="dropzone after"></div>');
		$('.sitemap .select-container, .sitemap .dropzone').droppable({
	        accept: '.sitemap li',
	        tolerance: 'pointer',
	        drop: function(e, ui) {
				var dragged_id = ui.draggable.get(0).id.substr(ui.draggable.get(0).id.lastIndexOf('-')+1);
				var li = $(this).parent();
	            var child = !$(this).hasClass('dropzone');
				var after = (!child && $(this).hasClass('after'));
				
				if (child && li.children('ul').length == 0) {
	                li.append('<ul/>');
	            }
	            if (child) {
	                li.addClass('sm2_liOpen').removeClass('sm2_liClosed').children('ul').append(ui.draggable);
				} else if (after) {
					li.after(ui.draggable);
				} else {
	                li.before(ui.draggable);
				}
				$('.sitemap li.sm2_liOpen').not(':has(li:not(.ui-draggable-dragging))').removeClass('sm2_liOpen');
	            //li.find('dl,.dropzone').css({ backgroundColor: '', borderColor: '' });
	            li.find('.select-container,.dropzone').removeClass('sm2_over');
				sitemapHistory.commit();
				
				if (child) {
					parent_id = this.parentNode.id.substr(this.parentNode.id.lastIndexOf('-')+1);
					parent_ul = $(this.parentNode).find('ul:first').get(0);
				} else {
					parent_id = this.parentNode.parentNode.parentNode.id.substr(this.parentNode.parentNode.parentNode.id.lastIndexOf('-')+1)
					parent_ul = $(this.parentNode.parentNode.parentNode).find('ul:first').get(0);
				}
				$('.sitemap ul:first').removeClass().addClass('sortable').addClass('col' + ($('.sitemap ul:first > li:not(.ui-draggable-dragging)').size()-1));
				
				sortChange(dragged_id, parent_id, parent_ul);
	        },
	        over: function() {
	            //$(this).filter('dl').css({ backgroundColor: '#ccc' });
	            //$(this).filter('.dropzone').css({ borderColor: '#aaa' });
				$(this).filter('.select-container,.dropzone').addClass('sm2_over');
	        },
	        out: function() {
	            //$(this).filter('dl').css({ backgroundColor: '' });
	            //$(this).filter('.dropzone').css({ borderColor: '' });
				$(this).filter('.select-container,.dropzone').removeClass('sm2_over');
	        }
	    });
	    $('.sitemap_undo').click(sitemapHistory.restoreState);
	    $(document).bind('keypress', function(e) {
	        if (e.ctrlKey && (e.which == 122 || e.which == 26))
	            sitemapHistory.restoreState();
	    });
		refreshSort();
	} // initSort
	
	function refreshSort() {
		$('.sitemap .sortable li:not(.home)').draggable({
	        handle: ' > .select-container',
	        opacity: .8,
	        addClasses: false,
	        helper: 'clone',
	        zIndex: 100,
	        start: function(e, ui) {
	            sitemapHistory.saveState(this);
	        }
	    });
	} // refreshSort
	
	function sortChange(dragged_id, parent_id, parent_ul) {
		// now i need to get the dom structure based on what was sorted
		// so i can figure out what changed and save it to db.
		// this = item dropped.
		// need to get the item's parent object
		//var parentUL = li.get(0).parentNode;//ui.item[0].parentNode;
		//var parentSection = parentUL.parentNode;
		//var parent_id = parentSection.id.substr(parentSection.id.lastIndexOf('-')+1);
		// then figure out the new order of the children of parent
		var children = new Array();
		for (x in parent_ul.childNodes) {
			if ((parent_ul.childNodes[x].nodeName) && (parent_ul.childNodes[x].nodeName.toLowerCase() == 'li')) {
				children[children.length] = parent_ul.childNodes[x].id.substr(parent_ul.childNodes[x].id.lastIndexOf('-')+1);
			}
		}
		//$('ul.sitemap li:even').removeClass('row-odd').addClass('row-even');
		//$('ul.sitemap li:odd').removeClass('row-even').addClass('row-odd');
		setTimeout(function() { 
			$('.sitemap li').find('ul').each(function(i) {
				if (!is_html(jQuery.trim($(this).html()))) {
					$(this).remove();
				}
			}); 
		}, 100);
		
		// make an ajax call so that we can update the database with changes
		$.post(root + 'admin/scripts/sitemap-order/?ajax', 
			{
				parent_id : parent_id,
				children_ids : children.join()
			},
			function(data, textStatus) {
				if (data != 'true') {
					ajaxError(data);
				} else {
					//setTimeout(function() { alert($('.sitemap').find('ul:empty').size()); $('.sitemap').find('ul:empty').remove(); }, 100);
					
					// update dragged item so that it now has a correct page location
					$.post(root + 'admin/includes/pages-slickmap/', { id : dragged_id }, function(data, textStatus) {
						$('#page-' + dragged_id).replaceWith(data);
						initSort();	// need to update dragging to keep it working
					});
				}
			}
		);
	}
		
	function toggleSortable (btn) {
		if (btn.src.indexOf('btn-reorderpages.gif') > -1) {
			$('.sitemap ul:first').addClass('sortable');
			btn.src = btn.src.replace(/btn-reorderpages.gif/i, "btn-saveorder.gif");
			refreshSort();
		} else {
			$('.sitemap ul:first').removeClass('sortable');
			btn.src = btn.src.replace(/btn-saveorder.gif/i, "btn-reorderpages.gif");
			$('.sitemap ul:first li').draggable('destroy');
		}
	}
