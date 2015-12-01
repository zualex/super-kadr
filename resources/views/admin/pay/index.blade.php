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

<div class="transactions">
	<div class="header">
		<div class="clear">
			<div class="title"><span>Транзакции</span></div>
			
			<div class="controls">
				<div class="btn-group">
					<a href="#" class="btn add" onclick="actionAll('{{ route('admin.pay.paid_all') }}')"><i class="fa pull-left fa-floppy-o"></i>Оплачено</a>
					<a href="#" class="btn info" onclick="actionAll('{{ route('admin.pay.wait_all') }}')"><i class="fa pull-left fa-floppy-o"></i>Ожидает оплаты</a>
					<a href="#" class="btn del" onclick="actionAll('{{ route('admin.pay.cancel_all') }}')"><i class="fa pull-left fa-floppy-o"></i>Отмена</a>
					<a href="#" class="btn del" onclick="if(confirm('Вы действительно хотите удалить транзакцию?')){actionAll('{{ route('admin.pay.hide_all') }}')}"><i class="fa pull-left fa-trash"></i>Удалить</a>
				</div>
			</div>
			
		</div>
	</div>

	
	@if (Session::has('message'))
		<br>
		<div class="alert alert-info">{{ Session::get('message') }}</div>
	@endif
	
	<form id="sort-items" method="GET" >
		<div class="sort-block">
			<div class="inline-block">
				<div class="title">Поиск по дате</div>
			</div>
			<div class="inline-block">
				<div class="inline-title">C</div>
				<div class="inline-value">
					<input type="text" class="datepicker inputbox " id="dateFrom" readonly name="dateFrom" value="{{ $data['dateFrom'] }}">
				</div>
				<div class="inline-title">По</div>
				<div class="inline-value">
					<input type="text" class="datepicker inputbox " id="dateTo" readonly name="dateTo" value="{{ $data['dateTo'] }}">
				</div>
			</div>
			<div class="inline-block">
				<div class="btn-group">
					<a onclick="$('#sort-items').submit();" class="btn add"><i class="fa pull-left fa-search"></i>Найти</a>
				</div>
			</div>
		</div>
	</form>
		
		
	@if(count($pay) > 0)
		<form id="form-admin" role="form" method="POST" >
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="extra_field" id="extra_field" value="">
		
		<table class="table-list">
			<tr>
				<th class="center col-check"><input class="checkbox" type="checkbox" name="checkall" value=""></th>
				<th class="center col-2">ID</th>
				<th>Операция</th>
				<th class="col-4">Сумма</th>
				<th class="col-5">Дата</th>
				<th class="col-4">Состояние</th>
				<th class="center col-btn"><i class="fa fa-th-list"></i></th>
			</tr>
			@foreach($pay as $key => $value)
				<tr>
					<td class="center col-check"><input type="checkbox" name="checkelement[]" value="{{ $value->id }}"></td>
					<td class="center col-2"> {{ $value->id }} </td>
					<td>
						@if($value->user_name != '')
							Пользователь: {{ $value->user_name }} - {{ $value->provider }} сделал заказ
						@else
							Анонимный пользователь сделал заказ
						@endif
						<a href="{{ $pathImages.'/o_'.$value->src }}" rel="group1" class="modalbox">(подробнее)</a>
					</td>
					<td class="col-4">{{ $value->price }} руб.</td>
					<td class="col-5">{{ $value->created_at }}</td>
					<td class="col-4">
						@if($value->status_caption == 'paid')
							<span class="status ok"><i class="fa pull-left fa-chevron-down"></i>
						@elseif($value->status_caption == 'wait')
							<span class="status wait"><i class="fa pull-left fa-clock-o"></i>
						@elseif($value->status_caption == 'Error')
							<span class="status cancel"><i class="fa pull-left fa-ban"></i>
						@elseif($value->status_caption == 'cancelUser')
							<span class="status cancel"><i class="fa pull-left fa-ban"></i>
						@elseif($value->status_caption == 'cancelAdmin')
							<span class="status cancel"><i class="fa pull-left fa-ban"></i>
						@endif
						
						{{ $value->status_name }}</span>
						
						
						
					</td>
					<td class="center col-btn">
						<div class="droplist-group">
							<i class="fa fa-ellipsis-v"></i>
							<div class="droplist" style="display: none">
								<div>
									<ul>
										<li><a href="{{ route('admin.pay.hide', $value->id) }}" onclick="return confirm('Вы действительно хотите удалить транзакцию?')"><i class="fa pull-left fa-times"></i>Удалить</a></li>
										
										<!--<li><a onclick="r_delete({id})" class="deny"><i class="fa pull-left fa-times"></i>Удалить</a></li>-->
									</ul>
								</div>
							</div>
						</div>
					</td>
				</tr>
			@endforeach
		</table>
		</form>
	@endif
</div>
		
@endsection