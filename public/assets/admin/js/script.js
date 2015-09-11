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
	
	/* Click checkbox all */
	$("input:checkbox[name='checkall']").click(function(){
		$(this).closest(".table-list").find("input:checkbox").prop('checked', this.checked);
	});
	
});


/* Одобрить заказы */
function actionAll(url){
	var data = $("#form-admin").serialize();
	$.ajax({
		url: url, 
		dataType: "html",
		type: 'POST',
		data: data,
		success: function(data){
			var data = $.parseJSON(data);
			if(data.status == 'error'){alert(data.message);}
			if(data.status == 'success'){
				alert(data.message);
				window.location.reload();
			}
		},
		error: function(){
			alert('Произошла ошибка');
		}
	});
}
