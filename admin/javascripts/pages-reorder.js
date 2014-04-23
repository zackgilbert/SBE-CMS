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
			$('#sitemap li.sm2_liOpen').not(':has(li)').removeClass('sm2_liOpen');
			$('#sitemap li:has(ul li):not(.sm2_liClosed)').addClass('sm2_liOpen');
	    }
	}

	//init functions
	$(function() {
	    $('#sitemap li').prepend('<div class="dropzone"></div>');

	    $('#sitemap dl, #sitemap .dropzone').droppable({
	        accept: '#sitemap li',
	        tolerance: 'pointer',
	        drop: function(e, ui) {
	            var li = $(this).parent();
	            var child = !$(this).hasClass('dropzone');
	            if (child && li.children('ul').length == 0) {
	                li.append('<ul/>');
	            }
	            if (child) {
	                li.addClass('sm2_liOpen').removeClass('sm2_liClosed').children('ul').append(ui.draggable);
	            }
	            else {
	                li.before(ui.draggable);
	            }
				$('#sitemap li.sm2_liOpen').not(':has(li:not(.ui-draggable-dragging))').removeClass('sm2_liOpen');
	            //li.find('dl,.dropzone').css({ backgroundColor: '', borderColor: '' });
	            li.find('dl,.dropzone').removeClass('sm2_over');
				sitemapHistory.commit();
				sortChange(li);
	        },
	        over: function() {
	            //$(this).filter('dl').css({ backgroundColor: '#ccc' });
	            //$(this).filter('.dropzone').css({ borderColor: '#aaa' });
				$(this).filter('dl,.dropzone').addClass('sm2_over');
	        },
	        out: function() {
	            //$(this).filter('dl').css({ backgroundColor: '' });
	            //$(this).filter('.dropzone').css({ borderColor: '' });
				$(this).filter('dl,.dropzone').removeClass('sm2_over');
	        }
	    });
	    $('#sitemap li').draggable({
	        handle: ' > dl',
	        opacity: .8,
	        addClasses: false,
	        helper: 'clone',
	        zIndex: 100,
	        start: function(e, ui) {
	            sitemapHistory.saveState(this);
	        }
	    });
	    $('.sitemap_undo').click(sitemapHistory.restoreState);
	    $(document).bind('keypress', function(e) {
	        if (e.ctrlKey && (e.which == 122 || e.which == 26))
	            sitemapHistory.restoreState();
	    });
	});
	
	function sortChange(li) {
		// now i need to get the dom structure based on what was sorted
		// so i can figure out what changed and save it to db.
		// this = item dropped.
		// need to get the item's parent object
		var parentUL = li.get(0).parentNode;//ui.item[0].parentNode;
		var parentSection = parentUL.parentNode;
		var parent_id = parentSection.id.substr(parentSection.id.lastIndexOf('-')+1);
		// then figure out the new order of the children of parent
		var children = new Array();
		for (x in parentUL.childNodes) {
			if ((parentUL.childNodes[x].nodeName) && (parentUL.childNodes[x].nodeName.toLowerCase() == 'li')) {
				children[children.length] = parentUL.childNodes[x].id.substr(parentUL.childNodes[x].id.lastIndexOf('-')+1);
			}
		}
		//$('ul.sitemap li:even').removeClass('row-odd').addClass('row-even');
		//$('ul.sitemap li:odd').removeClass('row-even').addClass('row-odd');
		
		// make an ajax call so that we can update the database with changes
		$.post(root + 'admin/scripts/sitemap-order/?ajax', 
			{
				parent_id : parent_id,
				children_ids : children.join()
			},
			function(data, textStatus) {
				if (data != 'true') {
					ajaxError(data);
				}
			}
		);
	}
