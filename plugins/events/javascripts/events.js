
	function changeEventType(sel) {
		if (!sel) return false;
		if (sel.value === '1') {
			// art exhibit (1)	-- hide price/age/event-music-subcategory/event-literary-subcategory
			$('#event-age').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-price').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-literary-subcategory').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-music-subcategory').parent().hide().find('input, select').attr('disabled','disabled');
		} else if (sel.value === '9') {
			// kids (9) -- show price/age, hide event-music-subcategory/event-literary-subcategory
			//$('#event-age').parent().show().find('input, select').removeAttr('disabled');
			$('#event-age').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-price').parent().show().find('input, select').removeAttr('disabled');
			$('#event-literary-subcategory').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-music-subcategory').parent().hide().find('input, select').attr('disabled','disabled');
		} else if (sel.value === '11') {
			// literary (11) -- show price/event-literary-subcategory, hide age/event-music-subcategory
			$('#event-age').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-price').parent().show().find('input, select').removeAttr('disabled');
			$('#event-literary-subcategory').parent().show().find('input, select').removeAttr('disabled');
			$('#event-music-subcategory').parent().hide().find('input, select').attr('disabled','disabled');
		} else if (sel.value === '12') {
			// music (12) -- show price/event-music-subcategory, hide age/event-literary-subcategory
			$('#event-age').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-price').parent().show().find('input, select').removeAttr('disabled');
			$('#event-literary-subcategory').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-music-subcategory').parent().show().find('input, select').removeAttr('disabled');
		} else {
			// general (all other events) -- show price, hide age, event-music-subcategory,event-literary-subcategory
			$('#event-age').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-price').parent().show().find('input, select').removeAttr('disabled');
			$('#event-literary-subcategory').parent().hide().find('input, select').attr('disabled','disabled');
			$('#event-music-subcategory').parent().hide().find('input, select').attr('disabled','disabled');
		}
	} // changeEventType
		
	function changeOccurrence(sel) {
		(sel.value == 'once') ? initOnce() : initRepeats();
	} // chanceOccurance
	
	function changeFrequencyPluralization(sel) {
		var isPlural = ($(sel).get(0).value !== '1');
		$(sel).next().find('option').each(function() { 
			if (isPlural) {
				this.text = (this.text.substr(-1) == 's') ? this.text : this.text + 's';
			} else {
				this.text = (this.text.substr(-1) == 's') ? this.text.substr(0, this.text.length-1) : this.text;				
			}
		});
	} // changeFrequencyPluralization
	
	function changeFrequency(sel) {
		$('.' + sel.value).show().find('input, select').removeAttr('disabled');
		$('.frequency:not(.' + sel.value + '), .multiple').hide().find('input, select').attr('disabled','disabled'); 
	} // changeFrequency
	
	function initOnce() {
		$('.once').show().find('input, select').removeAttr('disabled');
		$('.repeats, .months, .years, .multiple, .ending').hide().find('input, select').attr('disabled','disabled');
		$('.once').find('select').each(function(){ this.selectedIndex = 0; });
	} // initOnce
	
	function initRepeats() {
		$('.repeats, .weeks').show().find('input, select').removeAttr('disabled');
		$('.once, .months, .years, .multiple, .ending').hide().find('input, select').attr('disabled','disabled');
		$('.repeats').find('select').each(function(){ this.selectedIndex = 0; });
	} // initRepeats
	
	/*function changeLocation(sel, value) {
		if (sel.value == '--OTHER--') {
			sel.selectedIndex = 0;
			$(sel).attr('disabled', 'disabled').hide();
			//$(sel.parentNode).find('span input').removeAttr('disabled').attr('value', value).parent().show();
			$(sel).after('<span><input type="text" name="event[location]" class="field-medium" value="' + value + '"/> or <a href="javascript:;" onclick="cancelChanceLocation(this);" class="cancel-changes">Cancel</a></span>')
		}
	} // changeLocation

	function cancelChanceLocation(input) {
		$(input.parentNode.parentNode).find('select').removeAttr('disabled').show();
		$(input).parent().remove();
	} // cancelChangeLocation*/

	function changeLocation(link, value) {
		$(link).parent().hide().find('select').attr('disabled', 'disabled');
		$(link).parent().after('<span><input type="text" name="event[location]" class="field-medium" value="' + value + '"/> or <a href="javascript:;" onclick="cancelChanceLocation(this);" class="location-choose">Select a Venue</a></span>');
		$(link).parent().parent().find('input.field-medium').focus();
	} // changeLocation

	function cancelChanceLocation(link) {
		$('#event-location-container').show().find('select').removeAttr('disabled');
		$(link).parent().remove();
	} // cancelChangeLocation

	$(document).ready(function() {
		initOnce();
		changeEventType($('#event-category').get(0));
		
		/*$('.venues').autocomplete(root + "/scripts/event-location/", {
			max: 20,
			matchContains: true,
			minChars: 0
		}).result(function(event, data, formatted) {
			if (data) $(this).parent().find("input[type=hidden]:first").val(data[1]);
		}).focus(function() { 
			$(this).click(); 
			if (this.value != '') this.select();
		}).keypress(function(e) {
			var key = (window.event) ? window.event.keyCode : e.which;
			return !(key == 13);
		}).change(function() {
			if (this.value == '') { 
				$(this).parent().find("input[type=hidden]:first").val('');
			}
		});*/
	});
