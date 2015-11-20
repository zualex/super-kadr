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


/* массовые операции */
function actionAll(url){
	var data = $("#form-admin").serialize();
	$.ajax({
		url: url, 
		dataType: "html",
		type: 'POST',
		data: data,
		success: function(data){
			$('#extra_field').val('');
			var data = $.parseJSON(data);
			if(data.status == 'error'){alert(data.message);}
			if(data.status == 'success'){
				alert(data.message);
				window.location.reload();
			}
			if(data.status == 'prompt'){
				var value = prompt(data.message);
				if(value){
					$('#extra_field').val(value);
					actionAll(url);
				}
			}
		},
		error: function(){
			alert('Произошла ошибка');
		}
	});
}



/* Изменения одного поля checkbox */
function saveFieldCheckbox(el, url){
	var value = 0;
	if($(el).prop('checked')){
		value = $(el).val();
	}
	$.ajax({
		url: url, 
		dataType: "html",
		type: 'POST',
		data: {
			'value' : value
		},
		success: function(data){
			var data = $.parseJSON(data);
			if(data.status == 'error'){alert(data.message);}
			if(data.status == 'success'){alert(data.message);}
		},
		error: function(){
			alert('Произошла ошибка');
		}
	});
	
}



/* В заказах при изменении кол-ва лайков сохранение*/
function saveNewLikes(el, url){
	var value = 0;
	value = $(el).val();
	$.ajax({
		url: url, 
		dataType: "html",
		type: 'POST',
		data: {
			'value' : value
		},
		success: function(data){
			var data = $.parseJSON(data);
			if(data.status == 'error'){alert(data.message);}
			if(data.status == 'success'){alert(data.message);}
		},
		error: function(){
			alert('Произошла ошибка');
		}
	});
	
}
