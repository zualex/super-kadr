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
		auto_right('#slider1');
		auto_right('#slider2');
		auto_right('#slider3');
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
	/*$('.add-comments .submit').click(function (event) {
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
	});*/

	$(function() {
	  $('.tabs').on('click', '.tab-head:not(.active)', function() {
		$(this).addClass('active').siblings().removeClass('active')
		  .parents('.tab-block').find('section').eq($(this).index()).slideToggle(1000).siblings('section').hide();
	  })
	});
	
	
	

	
	/*
	*	Переключение тарифов
	*/
	$('.tariffs').on('click', '.tariff', function() {
		changeTarif($(this).find('.select').attr('tariff-id'));
		$('.tariffs .tariff').removeClass('active').find('.select span').text('Выбрать');
		$(this).addClass('active').find('.select span').text('Ваш выбор');
	});
	function changeTarif(cnt){
		var tariff = cnt;
		
		$('.tariffs').attr('data-tariff', tariff);
		availabilityDate('', tariff);

	}
	
	
	/*
	*	Инициализируем первый монитор
	*/
	if($('#upload').length){
	$('.monitor-select .select:first').addClass('active');
	var nowMonitor = $('.monitor-select .select:first').attr('data-monitor');
	var Cwidth = $('.monitor-select .select:first').attr('data-width')/1;
	var Cheight = $('.monitor-select .select:first').attr('data-height')/1;
	$('#monitor-change-class').width(Cwidth).height(Cheight);
	$('.cropImgWrapper').find('img').css({ "top": "0px", "left": "0px" });
	$('.tariffs').attr('data-monitor', nowMonitor);
	croppic.options.cropData.monitor = nowMonitor;
	croppic.objW = Cwidth;
	croppic.objH = Cheight;
	
	/*
	*	Переключение экранов
	*/
	$('.monitor-select .select').click(function() {
		$(this).parents('.monitor-select').find('.select').removeClass('active');
		$(this).addClass('active');
		var newMonitor = $(this).attr('data-monitor');
		$('.tariffs').attr('data-monitor', newMonitor);
		croppic.options.cropData.monitor = newMonitor;
		availabilityDate('', '', newMonitor);
		
		// Для того чтобы нормально отображалась картинка при смене экранов
		var newW = $(this).attr('data-width')/1;
		var newH = $(this).attr('data-height')/1;
		croppic.objW = newW;
		croppic.objH = newH;
		$('#croppic').width(newW).height(newH);
		$('.cropImgWrapper').width(newW).height(newH);
		$('.cropImgWrapper').find('img').css({ "top": "0px", "left": "0px" });

		
		

		$('html, body').animate({
			scrollTop: $("#monitor-change-class").offset().top - 100
		}, 500);
		setTimeout(function() {
			$('#monitor-change-class').width(newW).height(newH);
		}, 300);
	});
	}
	
	/*
	*	Выбор даты
	*/
	$('.box-content').on('click', '.time-item.active', function() {
		$('.box-content').find('.time-item.active').removeClass('select');
		$(this).addClass('select');
		
		dateShow = $(this).attr('data-time');
		$('.tariffs').attr('data-dateShow', dateShow);
	});
	
	
});


/*
*	Оплата
*/
function paySite(el){
	checkDate();		//Проверка валидности даты

	var selector = $('.tariffs');
	var url = selector.attr('data-url');
	var tarif = selector.attr('data-tariff');
	var monitor = selector.attr('data-monitor');
	var dateShow = selector.attr('data-dateShow');
	var image = selector.attr('data-image');
	if(url != '' && tarif != '' && monitor != '' && dateShow != '' && image != ''){
		var htmlOld = $(el).html();
		$(el).html('<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div>');
		
		$.ajax({
			url: url, 
			dataType: "html",
			type: 'POST',
			data: {
				'tarif' : tarif,
				'monitor' : monitor,
				'dateShow' : dateShow,
				'image' : image
			},
			success: function(msg){
				$(el).html(htmlOld);
				var data = $.parseJSON(msg);
				if(data.status == 'error'){alert(data.message);}
				if(data.status == 'success'){
					if(data.pay == 'true'){
						window.location.href=data.url;
					}else{
						alert(data.message);
						window.location.reload();
					}
					
				}	
				//console.log(msg);
			},
			error: function(){
				$(el).html(htmlOld);
				alert('Произошла ошибка');
			}
		});
	}else{
		var err = '';
		if(url == ''){err += 'Не задан url оплаты\n';}
		if(tarif == ''){err += 'Не выбран тариф\n';}
		if(monitor == ''){err += 'Не выбран экран\n';}
		if(dateShow == ''){err += 'Не выбраны дата и начало паказа\n';}
		if(image == ''){err += 'Не загружено фото\n';}
		
		if(err){alert(err);}
	}
	return false;
}



/*
*	Лайк
*/
function likeGallery(el, gallery, url){
	$.ajax({
		url: url, 
		dataType: "html",
		type: 'POST',
		data: {
			'_token' : $('meta[name="csrf-token"]').attr('content'),
			'gallery': gallery
		},
		success: function(data){
			var data = $.parseJSON(data);
			if(data.status == 'error'){alert(data.message);}
			if(data.status == 'success'){
				var countLikes = $(el).find('span').html()/1;
				countLikes += data.message;
				$(el).find('span').html(countLikes);
				if(data.message > 0){
					$(el).find('i').addClass('like_active');
				}else{
					$(el).find('i').removeClass('like_active');
				}
			}			
		},
		error: function(){
			alert('Произошла ошибка');
		}
	});
}


/*
*	Вставка комментария
*/
function setComment(el, gallery, url, url_show){
	var text = $(el).parent().find('.textarea').html();

	$.ajax({
		url: url, 
		dataType: "html",
		type: 'POST',
		data: {
			'_token' : $('meta[name="csrf-token"]').attr('content'),
			'gallery': gallery,
			'text': text
		},
		success: function(data){
			var data = $.parseJSON(data);
			if(data.status == 'error'){alert(data.message);}
			if(data.status == 'success'){
				$(el).parent().find('.textarea').html('');
				showComment(url_show);
			}
			
		},
		error: function(){
			alert('Произошла ошибка');
		}
	});
}


/*
*	вывод комментариев
*/
function showComment(url){
	$.ajax({
		url: url, 
		dataType: "html",
		type: 'GET',
		data: {
			'_token' : $('meta[name="csrf-token"]').attr('content')
		},
		success: function(data){
			$('.wrap_comment').html(data);
		},
		error: function(){
			alert('Произошла ошибка');
		}
	});
}




/*
*	Проверка доступности даты
*/
function availabilityDate(dateDay, tarif_id, monitor_id){
	var dateShow =$('.tariffs').attr('data-dateShow');
	if(!tarif_id){
		var tarif_id = $('.tariffs').attr('data-tariff');
	}
	if(!monitor_id){
		var monitor_id = $('.tariffs').attr('data-monitor');
	}
	
	if(!dateDay){
		var dateDay = $('.tab-head.day.active').attr('data-dateday');
	}
	

	$.ajax({
		url: '/json/checkdate', 
		dataType: "html",
		type: 'GET',
		data: {
			'_token' : $('meta[name="csrf-token"]').attr('content'),
			'tarif_id' : tarif_id,
			'monitor_id' : monitor_id,
			'dateDay' : dateDay
		},
		success: function(data){
			$('.time-item.jsDeny').removeClass('deny').removeClass('jsDeny').addClass('active');
			var data = $.parseJSON(data);
			for (var dateHide in data.dates) {
				dateHideArr = dateHide.split(' ');
				dateHide = dateHideArr[1]+' '+dateHideArr[0];
				$('.time-item[data-time="'+dateHide+'"]').removeClass('active').removeClass('select').addClass('deny jsDeny');
				if(dateShow == dateHide){
					$('.tariffs').attr('data-dateShow', '');
				}
			}
		},
		error: function(){
			alert('Произошла ошибка');
		}
	});
}




/*
*	Проверка валидности даты
*/
function checkDate(){
	var nowTemp = new Date();
	var offest = nowTemp.getTimezoneOffset();
	var now = new Date(nowTemp.getTime() + offest-180)	//Приводим время по Москве

	var year = now.getFullYear();
	var day = now.getDate();
	var month = now.getMonth() + 1;
	var hour = now.getHours();

	var denyDate = hour+':00 '+day+'.'+month+'.'+year
	
	// Удалеям выбранную дату если она уже не доступна
	var dateShow = $('.tariffs').attr('data-dateShow');
	if(dateShow == denyDate){
		$('.tariffs').attr('data-dateShow', '');
	}
	
	$denyElem = $('.time-item[data-time="'+denyDate+'"]');
	$denyElem.removeClass('select');
	$denyElem.removeClass('active');
	$denyElem.addClass('deny');
	
	return denyDate;
}