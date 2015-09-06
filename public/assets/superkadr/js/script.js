function is_json(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

$(function(){	

	//var win_h = $(window).height();
	//var content_h = $("#first").height();
	
	//$('#header').css('padding-top', win_h/2-91).css('padding-right', '0').css('padding-bottom', win_h/2-91).css('padding-left', '0');
	
	//if(content_h < win_h){
	//	var wh_h = $(".wh").height();
	//	var omcd = win_h/2-wh_h/2;
	//	$('.content').css('padding-top', omcd).css('padding-right', '0').css('padding-bottom', omcd).css('padding-left', '0');
	//}

	//$('#status').fadeOut();
	//$('#preloader').delay(500).fadeOut('normal');
	//$('body').delay(500).css({'overflow':'visible'});

	//$("#ip").click(function(){
	//	var ipinf = $('#ipa').text();
	//	prompt("IP Сервера. Для копирования нажмите CTRL+C", ipinf);
	//	return false;
	//});

	$(".alert > .close").click(function(){
		$(this).parent().fadeOut("normal");
		return false;
	});

	$('[name="delete"]').click(function(){
		if(!confirm("Вы уверены, что хотите удалить выбранные элементы?")){ return false; }
		return true;
	});

	$("body").on("click", ".buy-item", function(){
		var tarif	= $('.tarif').attr('id');
		var monitor	= $('.select').val();

		prompt("INFO", monitor);
		if(monitor==''){ return false; }
		if(term==''){ return false; }
		
		$(".buy-item").prop('disabled', true);
		$(".buy-item").html('<img src="../../../uploads/ajax-loader.gif" alt="loading..." />');

	/*!	$.ajax({
			url: "index.php?do=ajax&op=get_option", 
			dataType: "html",
			type: 'POST',
			data: "act=new_trans&iid=" + iid + "&m=" + monitor + "&method=" + method,
			beforeSend: function(){
				$("#"+formid+" .buy-item").prop('disabled', true);
				$("#"+formid+" .buy-item").html('<img src="../../../uploads/ajax-loader.gif" alt="loading..." />');
			},

			success: function(data){
				if(!is_json(data)){
					$("#"+formid+" .buy-item").prop('disabled', false);
					$("#"+formid+" .buy-item").text('Купить за '+old_price+' руб.');
					console.log(data);
					return false;
				}

				var jsondata	= JSON.parse(data);

				var i_form		= jsondata.i_form;
				var i_inputs	= jsondata.i_inputs;
				var i_charset	= jsondata.i_charset;

				var invdesc		= $('#'+formid+' input[name="desc"]').val();

				$("#"+formid+" .buy-item").prop('disabled', false);
				$("#"+formid+" .buy-item").text('Подтвердить');

				$("#"+formid+" .buy-login, #"+formid+" .buy-promo").prop('disabled', true);
				$('#'+formid+' > form').attr("accept-charset", i_charset);
				$('#'+formid+' > form').attr("action", i_form);
				$('#'+formid+' .method-inputs').html(i_inputs);

				$('#'+formid+' .buy-item').removeClass('buy-item');

				$("#"+formid+" .modal_content")[0].submit();

			}
		});

		return false; */

	});

	$("body").on("keyup", ".buy-promo, .buy-login", function(){
		var formid = $(this).closest('.modal_div').attr('id');
		var player = $('#'+formid+' .buy-login').val();
		var promo = $('#'+formid+' .buy-promo').val();
		var iid = $('#'+formid+' .buy-item').val();
		var old_price = $('#'+formid+' .buy-item > span').text();
		
		$("#"+formid+" .buy-item").prop('disabled', true);
		$("#"+formid+" .buy-item").html('<img src="../../../uploads/ajax-loader.gif" alt="loading..." />');

		$.ajax({
			url: "/index.php?do=ajax&op=get_option", 
			dataType: "html",
			type: 'POST',
			data: "act=get_price&iid=" + iid + "&player=" + player + "&promo=" + promo,
			beforeSend: function(){
				$("#"+formid+" .buy-item").prop('disabled', true);
				$("#"+formid+" .buy-item").html('<img src="../../../uploads/ajax-loader.gif" alt="loading..." />');
			},

			success: function(data){
				if(!is_json(data)){
					$("#"+formid+" .buy-item").prop('disabled', false);
					$("#"+formid+" .buy-item").text('Купить за '+old_price+' руб.');
					console.log(data);
					return false;
				}

				var jsondata	= JSON.parse(data);

				var i_price		= jsondata.i_price;

				$("#"+formid+" .buy-item").prop('disabled', false);
				$("#"+formid+" .buy-item").text('Купить за '+i_price+' руб.');

			}
		});

		return false;
	});














	$(".slide").css({"width":$(".slider .content").outerWidth()/5});
	$(document).on('click', ".slider .controls .nav-right",function(){ 
		var carusel = $(this).parents('.slider');
		right_carusel(carusel);
		return false;
	});
	$(document).on('click',".slider .controls .nav-left",function(){ 
		var carusel = $(this).parents('.slider');
		left_carusel(carusel);
		return false;
	});
	function left_carusel(carusel){
	   var block_width = $(carusel).find('.slide').outerWidth();
	   $(carusel).find(".slide-list .slide").eq(-1).clone().prependTo($(carusel).find(".slide-list")); 
	   $(carusel).find(".slide-list").css({"left":"-"+block_width+"px"});
	   $(carusel).find(".slide-list .slide").eq(-1).remove();    
	   $(carusel).find(".slide-list").animate({left: "0px"}, 200); 
	   
	}
	function right_carusel(carusel){
	   var block_width = $(carusel).find('.slide').outerWidth();
	   $(carusel).find(".slide-list").animate({left: "-"+ block_width +"px"}, 200, function(){
		  $(carusel).find(".slide-list .slide").eq(0).clone().appendTo($(carusel).find(".slide-list")); 
		  $(carusel).find(".slide-list .slide").eq(0).remove(); 
		  $(carusel).find(".slide-list").css({"left":"0px"}); 
	   }); 
	}

	$(function() {
		auto_right('.slider');
	})

	function auto_right(carusel){
		setInterval(function(){
			if (!$(carusel).is('.hover'))
				right_carusel(carusel);
		}, 5000)
	}
	$(document).on('mouseenter', '.slider', function(){$(this).addClass('hover')})
	$(document).on('mouseleave', '.slider', function(){$(this).removeClass('hover')})
});
$(document).ready( function () {
	$('.add-comments .textarea').hover(function () {
		if ($('.add-comments .textarea').is(':empty')){
			$('.add-comments .submit').removeClass('active');
		} else{
			$('.add-comments .submit').addClass('active');
		}
	});
	$('.add-comments .submit').click(function (event) {
		event.preventDefault();
		if ($('.add-comments .textarea').is(':empty')){
			alert('Введите текст комментария!');
		}else{
			var text = $('.add-comments .textarea').text();
			var id = $('.add-comments .textarea').attr('id');
			alert("message=" + text + "&image=" + id);
			$.ajax({
				url: "comments.php",
				type: "POST",
				data: "message=" + text + "&image=" + id,
				success: function (data) {
					var data = $.parseJSON(data);
					if (data.error == '1'){
						alert('Неправильная длинна сообщения');
					}else{
						location.reload();
					}
				}
			});
		}
	});
	$('.likes').click(function (event) {
		event.preventDefault();
		if ($('.likes').hasClass('active')){
			var id = $('.likes').attr('id');
			alert("like="+ id);
			$.ajax({
				url: "comments.php",
				type: "POST",
				data: "like=" + id,
				success: function (data) {
					var data = $.parseJSON(data);
					if (data.error == '0'){
						alert('Указана неверная запись');
					}else{
						location.reload();
					}
				}
			});
		}else{
			alert('Сначала нужно авторизоваться!');
		}
	});
	$(function() {
	  $('.tabs').on('click', '.tab-head:not(.active)', function() {
		$(this).addClass('active').siblings().removeClass('active')
		  .parents('.tab-block').find('section').eq($(this).index()).slideToggle(1000).siblings('section').hide();
	  })
	});
	
	
	
	
	
	
	/*
	*	Показываем первый тариф
	*/
	$('.tariff').find('.label').each(function(i){
		if(i == 0){
			tarif = $(this).attr('data-tarif');
			$(this).removeClass('hidden');
			$('.tariff').attr('data-tarif', tarif);
			$('.tariff').find('.info_'+tarif).removeClass('hidden');
		}
	});
	
	/*
	*	Переключение тарифов
	*/
	$('.tariff').on('click', '.controls .nav-left', function() {changeTarif(-1)});
	$('.tariff').on('click', '.controls .nav-right', function() {changeTarif(1)});
	function changeTarif(cnt){
		var arrTarif = [];
		var nowTarif = $('.tariff').attr('data-tarif');
		var nextTarif = 0;
		
		$('.tariff').find('.label').each(function(i){
			arrTarif.push($(this).attr('data-tarif'));
		});
		var arrLen = arrTarif.length;
		for(i = 0; arrLen > i; i++){
			if(arrTarif[i] == nowTarif){
				nextIndex = i+cnt;
				//console.log('nextIndex: '+nextIndex);
				if(nextIndex >= arrLen){nextIndex = 0;}
				if(nextIndex < 0){nextIndex = arrLen-1;}
				nextTarif = arrTarif[nextIndex]
			}
		}
		
		//console.log('Текущий тариф: '+nowTarif);
		//console.log('Следующий тариф: '+nextTarif);
		
		
		$('.tariff').attr('data-tarif', nextTarif);
		$('.tariff').find('.item_'+nowTarif).addClass('hidden');
		$('.tariff').find('.info_'+nowTarif).addClass('hidden');
		$('.tariff').find('.item_'+nextTarif).removeClass('hidden');
		$('.tariff').find('.info_'+nextTarif).removeClass('hidden');

		
	}
	
	
	/*
	*	Инициализируем первый монитор
	*/
	var nowMonitor = $('.monitor select').val();
	$('.tariff').attr('data-monitor', nowMonitor);
	croppic.options.cropData.monitor = nowMonitor;

	
	/*
	*	Переключение экранов
	*/
	$('.monitor select').change(function() {
		oldMonitor = $('.tariff').attr('data-monitor');
		newMonitor = $(this).val();
		$('.tariff').attr('data-monitor', newMonitor);
		croppic.options.cropData.monitor = newMonitor;

		
		

		$('html, body').animate({
			scrollTop: $("#monitor-change-class").offset().top - 100
		}, 500);
		setTimeout(function() {
			$('#monitor-change-class').removeClass('activeMonitor_'+oldMonitor);
			$('#monitor-change-class').addClass('activeMonitor_'+newMonitor);
		}, 300);
	});
	
	
	/*
	*	Выбор даты
	*/
	$('.box-content').on('click', '.time-item.active', function() {
		$('.box-content').find('.time-item.active').removeClass('select');
		$(this).addClass('select');
		
		dateShow = $(this).attr('data-time');
		$('.tariff').attr('data-dateShow', dateShow);
	});
	
	
});