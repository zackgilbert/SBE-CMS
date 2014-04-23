/*
 * Based on: Fancybox - simple jQuery plugin for sheet image zooming
 * Examples and documentation at: http://sheet.klade.lv/
 * Version: 1.0.0 (25/04/2008)
 * Copyright (c) 2008 Janis Skarnelis
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * Requires: jQuery v1.2.1 or later
*/
(function($) {
	var opts = {}, 
		imgPreloader = new Image, 
		//imgTypes = ['png', 'jpg', 'jpeg', 'gif'], 
		loadingTimer, 
		loadingFrame = 1,
		startTop = -500;

	$.fn.sheet = function(settings) {
		opts.settings = $.extend({}, $.fn.sheet.defaults, settings);

		$.fn.sheet.init();

		return this.each(function() {
			var $this = $(this);
			var o = $.metadata ? $.extend({}, opts.settings, $this.metadata()) : opts.settings;

			$this.unbind('click').click(function() {
				$.fn.sheet.start(this, o); return false;
			});
		});
	};

	$.fn.sheet.start = function(el, o) {
		if (opts.animating) return false;

		if (o.overlayShow) {
			$("#sheet_wrap").prepend('<div id="sheet_overlay"></div>');
			$("#sheet_overlay").css({'width': $(window).width(), 'height': $(document).height(), 'opacity': o.overlayOpacity});

			if ($.browser.msie) {
				$("#sheet_wrap").prepend('<iframe id="sheet_bigIframe" scrolling="no" frameborder="0"></iframe>');
				$("#sheet_bigIframe").css({'width': $(window).width(), 'height': $(document).height(), 'opacity': 0});
			}

			$("#sheet_overlay").click($.fn.sheet.close);
		}

		opts.itemArray	= [];
		opts.itemNum	= 0;

		if (jQuery.isFunction(o.itemLoadCallback)) {
		   o.itemLoadCallback.apply(this, [opts]);

			var c	= $(el).children("img:first").length ? $(el).children("img:first") : $(el);
			var tmp	= {'width': c.width(), 'height': c.height(), 'pos': $.fn.sheet.getPosition(c)}

		   for (var i = 0; i < opts.itemArray.length; i++) {
				opts.itemArray[i].o = $.extend({}, o, opts.itemArray[i].o);
				
				if (o.zoomSpeedIn > 0 || o.zoomSpeedOut > 0) {
					opts.itemArray[i].orig = tmp;
				}
		   }

		} else {
			if (!el.rel || el.rel == '') {
				var item = {url: el.href, title: el.title, o: o};

				if (o.zoomSpeedIn > 0 || o.zoomSpeedOut > 0) {
					var c = $(el).children("img:first").length ? $(el).children("img:first") : $(el);
					item.orig = {'width': c.width(), 'height': c.height(), 'pos': $.fn.sheet.getPosition(c)}
				}

				opts.itemArray.push(item);

			} else {
				var arr	= $("a[@rel=" + el.rel + "]").get();

				for (var i = 0; i < arr.length; i++) {
					var tmp		= $.metadata ? $.extend({}, o, $(arr[i]).metadata()) : o;
   					var item	= {url: arr[i].href, title: arr[i].title, o: tmp};

   					if (o.zoomSpeedIn > 0 || o.zoomSpeedOut > 0) {
						var c = $(arr[i]).children("img:first").length ? $(arr[i]).children("img:first") : $(el);

						item.orig = {'width': c.width(), 'height': c.height(), 'pos': $.fn.sheet.getPosition(c)}
					}

					if (arr[i].href == el.href) opts.itemNum = i;

					opts.itemArray.push(item);
				}
			}
		}

		$.fn.sheet.changeItem(opts.itemNum);
	};

	$.fn.sheet.changeItem = function(n) {
		$.fn.sheet.showLoading();

		opts.itemNum = n;

		$("#sheet_nav").empty();
		$("#sheet_outer").stop();
		$("#sheet_title").hide();
		$(document).unbind("keydown");

		//imgRegExp = imgTypes.join('|');
    	//imgRegExp = new RegExp('\.' + imgRegExp + '$', 'i');

		var url = opts.itemArray[n].url;

		/*if (url.match(/#/)) {
			var target = window.location.href.split('#')[0]; target = url.replace(target,'');

	        $.fn.sheet.showItem('<div id="sheet_div">' + $(target).html() + '</div>');

	        $("#sheet_loading").hide();

		} else if (url.match(imgRegExp)) {
			$(imgPreloader).unbind('load').bind('load', function() {
				$("#sheet_loading").hide();

				opts.itemArray[n].o.frameWidth	= imgPreloader.width;
				opts.itemArray[n].o.frameHeight	= imgPreloader.height;

				$.fn.sheet.showItem('<img id="sheet_img" src="' + imgPreloader.src + '" />');

			}).attr('src', url);

		} else {*/
			$.fn.sheet.prepItem('<iframe id="sheet_frame" onload="$.fn.sheet.showIframe()" name="sheet_iframe' + Math.round(Math.random()*1000) + '" frameborder="0" hspace="0" src="' + url + '"></iframe>');
		//}
	};

	$.fn.sheet.showIframe = function() {
		$("#sheet_loading").hide();
		$("#sheet_frame").show();
		$.fn.sheet.showItem();
	};

	$.fn.sheet.prepItem = function(val) {
		//$.fn.sheet.preloadNeighborImages();

		opts.active = true;
		
		var viewportPos	= $.fn.sheet.getViewport();
		
		// fix width/height for videos
		if (val.match(/width="(\d+)"/) && val.match(/height="(\d+)"/)) {
			opts.itemArray[opts.itemNum].o.frameWidth = val.match(/width="(\d+)"/)[1];
			opts.itemArray[opts.itemNum].o.frameHeight = val.match(/height="(\d+)"/)[1];
		}
		
		var itemSize	= $.fn.sheet.getMaxSize(viewportPos[0] - 50, viewportPos[1] - 100, opts.itemArray[opts.itemNum].o.frameWidth, opts.itemArray[opts.itemNum].o.frameHeight);
		
		var itemLeft	= viewportPos[2] + Math.round((viewportPos[0] - itemSize[0]) / 2) - 1;
		//var itemTop		= viewportPos[3] + Math.round((viewportPos[1] - itemSize[1]) / 2) - 40;

		var itemOpts = {
			'left':		itemLeft, 
			'top':		startTop,
			'width':	itemSize[0] + 'px', 
			'height':	itemSize[1] + 'px'	
		}
		$("#sheet_content").html($(val)).show();
		$("#sheet_outer").css(itemOpts).show();		
	};
		
	$.fn.sheet.showItem = function() {
		var itemOpts = { 'top' : 0 };
		if (opts.itemArray[opts.itemNum].o.zoomSpeedIn > 0) {
			opts.animating		= true;

			$("#sheet_outer").animate(itemOpts, opts.itemArray[opts.itemNum].o.zoomSpeedIn, function() {
				opts.animating = false;
				$.fn.sheet.updateDetails();
			});

		} else {
			//$("#sheet_content").append($(val)).show();
			$("#sheet_outer").css(itemOpts).show();
			$.fn.sheet.updateDetails();
		}
	};

	$.fn.sheet.updateDetails = function() {
		//$("#sheet_bg,#sheet_close").show();
		$("#sheet_bg").show();

		/*if (opts.itemArray[opts.itemNum].title !== undefined && opts.itemArray[opts.itemNum].title !== '') {
			$('#sheet_title div').html(opts.itemArray[opts.itemNum].title);
			$('#sheet_title').show();
		}*/

		/*if (opts.itemArray[opts.itemNum].o.hideOnContentClick) {
			$("#sheet_content").click($.fn.sheet.close);
		} else {
			$("#sheet_content").unbind('click');
		}*/

		/*if (opts.itemNum != 0) {
			$("#sheet_nav").append('<a id="sheet_left" href="javascript:;"></a>');

			$('#sheet_left').click(function() {
				$.fn.sheet.changeItem(opts.itemNum - 1); return false;
			});
		}

		if (opts.itemNum != (opts.itemArray.length - 1)) {
			$("#sheet_nav").append('<a id="sheet_right" href="javascript:;"></a>');
			
			$('#sheet_right').click(function(){
				$.fn.sheet.changeItem(opts.itemNum + 1); return false;
			});
		}*/
		$('#sheet_content iframe body').find('.close').click(function() { parent.$.fn.sheet.close(); });

		$(document).keydown(function(event) {
			if (event.keyCode == 27) {
            	$.fn.sheet.close();

			/*} else if(event.keyCode == 37 && opts.itemNum != 0) {
            	$.fn.sheet.changeItem(opts.itemNum - 1);

			} else if(event.keyCode == 39 && opts.itemNum != (opts.itemArray.length - 1)) {
            	$.fn.sheet.changeItem(opts.itemNum + 1);*/
			}
		});
	};

	/*$.fn.sheet.preloadNeighborImages = function() {
		if ((opts.itemArray.length - 1) > opts.itemNum) {
			preloadNextImage = new Image();
			preloadNextImage.src = opts.itemArray[opts.itemNum + 1].url;
		}

		if (opts.itemNum > 0) {
			preloadPrevImage = new Image();
			preloadPrevImage.src = opts.itemArray[opts.itemNum - 1].url;
		}
	};*/

	$.fn.sheet.close = function() {
		if (opts.animating) return false;

		$(imgPreloader).unbind('load');
		$(document).unbind("keydown");

		$("#sheet_loading,#sheet_title,#sheet_close,#sheet_bg").hide();

		$("#sheet_nav").empty();

		opts.active	= false;

		if (opts.itemArray[opts.itemNum].o.zoomSpeedOut > 0) {
			/*var itemOpts = {
				'top':		opts.itemArray[opts.itemNum].orig.pos.top - 18,
				'left':		opts.itemArray[opts.itemNum].orig.pos.left - 18,
				'height':	opts.itemArray[opts.itemNum].orig.height,
				'width':	opts.itemArray[opts.itemNum].orig.width,
				'opacity':	'hide'
			};*/
			var itemOpts = {
				'top' : startTop
			};

			opts.animating = true;

			$("#sheet_outer").animate(itemOpts, opts.itemArray[opts.itemNum].o.zoomSpeedOut, function() {
				$('#sheet_outer').hide();
				$("#sheet_content").hide().empty();
				$("#sheet_overlay,#sheet_bigIframe").remove();
				opts.animating = false;
			});

		} else {
			$("#sheet_outer").hide();
			$("#sheet_content").hide().empty();
			$("#sheet_overlay,#sheet_bigIframe").fadeOut("fast").remove();
		}
	};

	$.fn.sheet.showLoading = function() {
		clearInterval(loadingTimer);

		var pos = $.fn.sheet.getViewport();

		$("#sheet_loading").css({'left': ((pos[0] - 40) / 2 + pos[2]), 'top': ((pos[1] - 40) / 2 + pos[3])}).show();
		$("#sheet_loading").bind('click', $.fn.sheet.close);
		
		loadingTimer = setInterval($.fn.sheet.animateLoading, 66);
	};

	$.fn.sheet.animateLoading = function(el, o) {
		if (!$("#sheet_loading").is(':visible')){
			clearInterval(loadingTimer);
			return;
		}

		$("#sheet_loading > div").css('top', (loadingFrame * -40) + 'px');

		loadingFrame = (loadingFrame + 1) % 12;
	};

	$.fn.sheet.init = function() {
		if (!$('#sheet_wrap').length) {
			$('<div id="sheet_wrap"><div id="sheet_loading"><div></div></div><div id="sheet_outer"><div id="sheet_inner"><div id="sheet_nav"></div><div id="sheet_close"></div><div id="sheet_content"></div><div id="sheet_title"></div></div></div></div>').appendTo("body");
			$('<div id="sheet_bg"><div class="sheet_bg sheet_bg_n"></div><div class="sheet_bg sheet_bg_ne"></div><div class="sheet_bg sheet_bg_e"></div><div class="sheet_bg sheet_bg_se"></div><div class="sheet_bg sheet_bg_s"></div><div class="sheet_bg sheet_bg_sw"></div><div class="sheet_bg sheet_bg_w"></div><div class="sheet_bg sheet_bg_nw"></div></div>').prependTo("#sheet_inner");
			
			$('<table cellspacing="0" cellpadding="0" border="0"><tr><td id="sheet_title_left"></td><td id="sheet_title_main"><div></div></td><td id="sheet_title_right"></td></tr></table>').appendTo('#sheet_title');
		}

		if ($.browser.msie) {
			$("#sheet_inner").prepend('<iframe id="sheet_freeIframe" scrolling="no" frameborder="0"></iframe>');
		}

		if (jQuery.fn.pngFix) $(document).pngFix();

    	$("#sheet_close, #sheet_bg").click($.fn.sheet.close);
 	};

	$.fn.sheet.getPosition = function(el) {
		var pos = el.offset();

		pos.top	+= $.fn.sheet.num(el, 'paddingTop');
		pos.top	+= $.fn.sheet.num(el, 'borderTopWidth');

 		pos.left += $.fn.sheet.num(el, 'paddingLeft');
		pos.left += $.fn.sheet.num(el, 'borderLeftWidth');

		return pos;
	};

	$.fn.sheet.num = function (el, prop) {
		return parseInt($.curCSS(el.jquery?el[0]:el,prop,true))||0;
	};

	$.fn.sheet.getPageScroll = function() {
		var xScroll, yScroll;

		if (self.pageYOffset) {
			yScroll = self.pageYOffset;
			xScroll = self.pageXOffset;
		} else if (document.documentElement && document.documentElement.scrollTop) {
			yScroll = document.documentElement.scrollTop;
			xScroll = document.documentElement.scrollLeft;
		} else if (document.body) {
			yScroll = document.body.scrollTop;
			xScroll = document.body.scrollLeft;	
		}

		return [xScroll, yScroll]; 
	};

	$.fn.sheet.getViewport = function() {
		var scroll = $.fn.sheet.getPageScroll();

		return [$(window).width(), $(window).height(), scroll[0], scroll[1]];
	};

	$.fn.sheet.getMaxSize = function(maxWidth, maxHeight, imageWidth, imageHeight) {
		//var r = Math.min(Math.min(maxWidth, imageWidth) / imageWidth, Math.min(maxHeight, imageHeight) / imageHeight);

		//return [Math.round(r * imageWidth), Math.round(r * imageHeight)];
		return [imageWidth, imageHeight];
	};

	$.fn.sheet.defaults = {
		hideOnContentClick:	false,
		zoomSpeedIn:		450,
		zoomSpeedOut:		350,
		frameWidth:			600,
		frameHeight:		400,
		overlayShow:		true,
		overlayOpacity:		0,
		itemLoadCallback:	null
	};
})(jQuery);