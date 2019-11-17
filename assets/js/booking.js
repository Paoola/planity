const $ = require('jquery');

$(document).ready(function() {
	$('#choose_day').hide();
	$('.price').css('cursor', 'pointer');
	
	$('.price').click(function() {
		var id = $(this).attr('id');
		$('#choose_day').fadeIn('slow');
		$('#choose_prices').hide();
		$('#choose_day a').attr('href', function() {
	        return this.href.slice(0,-2) + '/' + id;
	    });
	});
});