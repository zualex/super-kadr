$(document).ready( function () {
	$(document).on('click', '.droplist-group', function () {
		var display = $(this).find(".droplist").css('display');
		if (display == 'none') $(this).find(".droplist").show();
		else $(this).find(".droplist").hide();
	});
});