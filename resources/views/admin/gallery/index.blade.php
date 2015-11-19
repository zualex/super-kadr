@extends('admin.app')

@section('content')
<link media="all" type="text/css" rel="stylesheet" href="/assets/admin/lib/jquery-ui-1.11.4.datepicker/jquery-ui.min.css">
<script src="/assets/admin/lib/jquery-ui-1.11.4.datepicker/jquery-ui.min.js"></script>
<script>
$(function() {
	$.datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: '&#x3c;Пред',
		nextText: 'След&#x3e;',
		currentText: 'Сегодня',
		monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
		'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
		monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
		'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
		dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
		dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
		dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
		weekHeader: 'Нед',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['ru']);


	$("#dateFrom").datepicker({
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		changeYear: true,
		firstDay:1,
		onClose: function( selectedDate ) {
			$("#dateTo").datepicker("option", "minDate", selectedDate);
		}
    });
    $("#dateTo").datepicker({
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		changeYear: true,
		firstDay:1,
		onClose: function( selectedDate ) {
			$("#dateFrom").datepicker("option", "maxDate", selectedDate);
		}
    });
});
</script>

<script>
$(function() {
	var ajax_from = $('#ajax_from').val();	
	var ajax_to = $('#ajax_to').val();	
	setInterval(function() {
		$.ajax({
			url: "{{ route('admin.gallery.application') }}", 
			dataType: "html",
			type: 'GET',
			cache: false,
			data: {
				'dateFrom' : ajax_from,
				'dateTo' : ajax_to
			},
			success: function(data){
				//console.log(ajax_from+' - '+ajax_to);
				$('#application').html(data);
			}
		});
	}, 15000);
});
</script>


<div id="orders">
	<div class="header">
		<div class="clear">
			<div class="title"><span>Заказы</span></div>
			<div class="controls">
				<div class="btn-group">
					<a href="#" class="btn add" onclick="actionAll('{{ route('admin.gallery.success_all') }}')"><i class="fa pull-left fa-floppy-o"></i>Одобрить</a>
					<a href="#" class="btn info" onclick="actionAll('{{ route('admin.gallery.moderation_all') }}')"><i class="fa pull-left fa-floppy-o"></i>На модерацию</a>
					<a href="#" class="btn del" onclick="if(confirm('Вы действительно хотите удалить заказ?')){actionAll('{{ route('admin.gallery.delete_all') }}')}"><i class="fa pull-left fa-trash"></i>Удалить</a>
				</div>
			</div>
		</div>
	</div>

	
	
	<div class="content-orders tab-block">
		<div class="orders tabs">
			<ul>
				<li class="active">Заявки</li>
				<li>Одобренные</li>
				<li>Отменённые</li>
			</ul>
		</div>
		
		@if (Session::has('message'))
			<br>
			<div class="alert alert-info">{{ Session::get('message') }}</div>
		@endif
		
		<input type="hidden" id="ajax_from" name="ajax_from" value="{{ $data['dateFrom'] }}">
		<input type="hidden" id="ajax_to" name="ajax_to" value="{{ $data['dateTo'] }}">
		<form method="GET" >
			<div style="padding:10px 0 0 10px">
				Поиск по дате c 
				<input type="text" class="datepicker inputbox " id="dateFrom" readonly name="dateFrom" value="{{ $data['dateFrom'] }}" style="width: 100px;"> по:
				<input type="text" class="datepicker inputbox " id="dateTo" readonly name="dateTo" value="{{ $data['dateTo'] }}"  style="width: 100px;">
				<input type="submit" class="btn add" style="margin-left:12px;float: none;display: inline-block;" value="Поиск">
			</div>
		</form>
		
		<form id="form-admin" role="form" method="POST" >
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		
		<div class="box-content">	
			<section id="application" class="visible">
				@include('admin.gallery.application')
			</section>
			
			<section id="order">	
				@if(count($data['gallerySuccess']) > 0)
					<table class="table-list">
						<thead>
							<tr>
								<th class="center col-1"><input class="checkbox" type="checkbox" name="checkall" value=""></th>
								<th class="center col-2">ID</th>
								<th class="center col-2">Пользователь</th>
								<th class="center col-3">Изображение</th>
								<th>Описание</th>
								<th class="col-4">Заказ</th>
								<th class="col-4">Тариф</th>
								<th class="col-4">Время на модерации</th>
								<th class="col-5">Статус</th>
								<th class="center col-btn"><i class="fa fa-th-list"></i></th>
							</tr>
						</thead>
						<tbody>				
						@foreach($data['gallerySuccess'] as $key => $value)
							<tr>
								<td class="center col-1">
									<input type="checkbox" name="checkelement[]" value="{{ $value->id }}">
								</td>
								<td class="center col-2">{{ $value->id }}</td>
								<td class="center col-2">
									@if($value->user_name != '')
										{{ $value->user_name }} - {{ $value->provider }}
									@else
										Анонимный пользователь
									@endif
								</td>
								<td class="center col-3">
									<a href="{{ $data['pathImages'].'/o_'.$value->src }}" rel="group2" class="modalbox">
										<img class="order-image" src="{{ $data['pathImages'].'/s_'.$value->src }}" alt="">
									</a>
								</td>
								<td>Изображение будет показано {{ $value->count_show }} раз в течение {{ $value->hours }} часа<br>Начало показа {{ $value->date_show }}</td>
								<td class="col-4">AA{{ $value->pay_id }}</td>
								<td class="col-4">{{ $value->tarif_name }}</td>
								<td class="col-4">
									{{ 
										Carbon\Carbon::parse($value->end_moderation)
										->diffInMinutes(Carbon\Carbon::parse($value->start_moderation))
									}} мин
								</td>
								<td class="col-5">
				
									@if($value->count_show == $value->hours*60*60/$value->interval_sec)
										<span class="status execute">В очереди на исполнение</span>
									@elseif($value->count_show == 0)
										<span class="status ok"><i class="fa pull-left fa-chevron-down"></i>Выполнена</span>
									@elseif($value->count_show < $value->hours*60*60/$value->interval_sec)
										<span class="status execute"><i class="fa pull-left fa-refresh fa-spin"></i>В исполнении</span>
									@endif
									
								</td>
								<td class="center col-btn">
									<div class="droplist-group">
										<i class="fa fa-ellipsis-v"></i>
										<div class="droplist" style="display: none">
											<div>
												<ul>
													<li><a href="{{ route('admin.gallery.cancel', $value->id) }}"><i class="fa pull-left fa-ban"></i>Отклонить</a></li>
													<li><a href="{{ route('admin.gallery.delete', $value->id) }}" onclick="return confirm('Вы действительно хотите удалить заказ?')"><i class="fa pull-left fa-times"></i>Удалить</a></li>
												</ul>
											</div>
										</div>
									</div>
								</td>
							</tr>
						@endforeach					
						</tbody>
					</table>
				@endif
			</section>
			<section id="offer" >
				@if(count($data['galleryCancel']) > 0)
					<table class="table-list">
						<thead>
							<tr>
								<th class="center col-1"><input class="checkbox" type="checkbox" name="checkall" value=""></th>
								<th class="center col-2">ID</th>
								<th class="center col-2">Пользователь</th>
								<th class="center col-3">Изображение</th>
								<th>Описание</th>
								<th class="col-4">Заказ</th>
								<th class="col-4">Тариф</th>
								<th class="col-4">Время на модерации</th>
								<th class="col-5">Статус</th>
								<th class="center col-btn"><i class="fa fa-th-list"></i></th>
							</tr>
						</thead>
						<tbody>
						@foreach($data['galleryCancel'] as $key => $value)
							<tr>
								<td class="center col-1">
									<input type="checkbox" name="checkelement[]" value="{{ $value->id }}">
								</td>
								<td class="center col-2">{{ $value->id }}</td>
								<td class="center col-2">
									@if($value->user_name != '')
										{{ $value->user_name }} - {{ $value->provider }}
									@else
										Анонимный пользователь
									@endif
								</td>
								<td class="center col-3">
									<a href="{{ $data['pathImages'].'/o_'.$value->src }}" rel="group3" class="modalbox">
										<img class="order-image" src="{{ $data['pathImages'].'/s_'.$value->src }}" alt="">
									</a>
								</td>
								<td>Изображение будет показано {{ $value->hours*60*60/$value->interval_sec }} раз в течение {{ $value->hours }} часа<br>Начало показа {{ $value->date_show }}</td>
								<td class="col-4">AA{{ $value->pay_id }}</td>
								<td class="col-4">{{ $value->tarif_name }}</td>
								<td class="col-4">
									{{ 
										Carbon\Carbon::parse($value->end_moderation)
										->diffInMinutes(Carbon\Carbon::parse($value->start_moderation))
									}} мин
								</td>
								<td class="col-5">
									<span class="status cancel"><i class="fa pull-left fa-ban"></i>Отменено</span>
								</td>
								<td class="center col-btn">
									<div class="droplist-group">
										<i class="fa fa-ellipsis-v"></i>
										<div class="droplist" style="display: none">
											<div>
												<ul>
													<li><a href="{{ route('admin.gallery.success', $value->id) }}"><i class="fa pull-left fa-chevron-down"></i>Принять</a></li>
													<li><a href="{{ route('admin.gallery.delete', $value->id) }}" onclick="return confirm('Вы действительно хотите удалить заказ?')"><i class="fa pull-left fa-times"></i>Удалить</a></li>
												</ul>
											</div>
										</div>
									</div>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				@endif
			</section>
		</div>
		</form>
	</div>
</div>
@endsection