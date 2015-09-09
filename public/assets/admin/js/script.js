$(document).ready( function () {
	$(document).on('click', '.droplist-group', function () {
		var display = $(this).find(".droplist").css('display');
		if (display == 'none') $(this).find(".droplist").show();
		else $(this).find(".droplist").hide();
	});
	
	/* Tabs */
	$('.tabs').on('click', 'li:not(.active)', function() {
	$(this).addClass('active').siblings().removeClass('active')
	  .parents('.tab-block').find('section').eq($(this).index()).fadeIn(150).siblings('section').hide();
	})
	
	
	/* Fancybox */
	$(".modalbox").fancybox();
	
});