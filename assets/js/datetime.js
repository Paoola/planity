require('jquery-datetimepicker/build/jquery.datetimepicker.min.css');
require('jquery-datetimepicker');

$.datetimepicker.setLocale('fr');

$('.datetime-picker').datetimepicker({
	format: 'd/m/Y H:i',
	step: 5
});