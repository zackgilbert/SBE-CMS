
	function selectPageType(radio) {
		var parentLI = radio.parentNode.parentNode;
		var parentUL = parentLI.parentNode;
		$(parentUL).find('li').removeClass('selected');
		$(parentUL).find('li ul li input, li div input').attr('disabled', 'disabled');
		$(parentLI).addClass('selected');
		$(parentLI).find('input').attr('disabled', false);
	}
