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
	@if($data['edit'] == 1)
		$("#date_start").datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			firstDay:1,
			onClose: function( selectedDate ) {
				$("#date_end").datepicker("option", "minDate", selectedDate);
			}
		});
	@endif
    $("#date_end").datepicker({
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		changeYear: true,
		firstDay:1,
		onClose: function( selectedDate ) {
			$("#date_start").datepicker("option", "maxDate", selectedDate);
		}
    });
	
	
	$("#start_select").datepicker({
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		changeYear: true,
		firstDay:1,
		onClose: function( selectedDate ) {
			$("#end_select").datepicker("option", "minDate", selectedDate);
		}
    });
    $("#end_select").datepicker({
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		changeYear: true,
		firstDay:1,
		onClose: function( selectedDate ) {
			$("#start_select").datepicker("option", "maxDate", selectedDate);
		}
    });
	
	
	
});
</script>

<div id="orders">
	<div class="header">
		<div class="clear">
			<div class="title"><span>Статистика</span></div>
			<div class="controls">
			</div>
		</div>
	</div>

	
	
	<div class="content-orders tab-block">		
		@if (Session::has('message'))
			<br>
			<div class="alert alert-info">{!! Session::get('message') !!}</div>
		@endif
			
		
		<div class="box-content">	
			<section id="application" class="visible" style="padding: 10px 20px">
				
				<form id="form-admin" role="form" method="POST" action="{{ route('admin.competition.save') }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				
					<div style="float:left">
						<div class="inline-block">
							<div class="line-title"><span>Конкурс:</span></div>
							<div class="line-value">
									<input 
										class="inputbox" 
										type="text" 
										name="name" 
										value="{{ $data['name'] }}" 
										placeholder="Название конкурса"
										@if($data['edit'] == 0)
											readonly
										@endif
									>
							</div>
						</div>
						
						<div class="inline-block">
							<div class="line-title"><span>Условия конкурса:</span></div>
							<div class="line-value">
								<textarea 
									class="inputbox" 
									name="text" 
									style="width:300px;height:150px;"
									@if($data['edit'] == 0)
										readonly
									@endif
								>{{ $data['text'] }}</textarea>
							</div>
						</div>
					</div>
					
					<div style="float:left">
						<div class="inline-block">
							<div class="line-title"><span>Начало конкурса:</span></div>
							<div class="line-value">
									<input 
										class="inputbox" 
										type="text" 
										name="date_start" 
										id="date_start" 
										value="{{ $data['date_start'] }}" 
										placeholder="Начало конкурса"
										@if($data['edit'] == 0)
											readonly
										@endif
									>
							</div>
						</div>
						
						<div class="inline-block">
							<div class="line-title"><span>Конец конкурса:</span></div>
							<div class="line-value">
								<input class="inputbox" type="text" name="date_end" id="date_end" value="{{ $data['date_end'] }}" placeholder="Конец конкурса">
							</div>
						</div>
						
						<div class="inline-block">
							<div class="line-title">Клиент</div>
							<div class="line-value">
								<select  class="inputbox" name="condition">
									@foreach($data['arrCondition'] as $key=>$value)
										<option @if($data['condition'] == $key) selected @endif value="{{ $key }}">{{ $value }}</option>
									@endforeach
								</select>
							</div>
						</div>
						
						<div class="inline-block">
							<div class="line-title"></div>
							<div class="line-value">
								<a href="#" class="btn add savesettings" onclick="$('#form-admin').submit();return false;">
									<i class="fa pull-left fa-floppy-o"></i>Сохранить
								</a>
								
								<a href="{{ route('admin.competition.delete') }}" class="btn del savesettings" onclick="if(!confirm('Вы действительно хотите удалить конкурс!')){return false;}">
									<i class="fa pull-left fa-trash"></i>Удалить
								</a>
							</div>
						</div>
					</div>
				
				</form>
				
				<div style="clear:both"></div>
				
				<hr>
					<h2>Текущий конкрус: 
						{{ $data['name'] }} 
							(c 
								{{ $data['date_start'] }} 
								@if($data['date_end'] != '' && $data['date_start'] != $data['date_end'])
									по {{ $data['date_end'] }} 
								@endif
							)
					</h2>
				<hr>
				<h3>Выбор запроса</h2>
				
				@if (Session::has('message2'))
					<br>
					<div class="alert alert-info">{!! Session::get('message2') !!}</div>
				@endif
				
				<form id="form-admin2" role="form" method="POST" action="{{ route('admin.competition.save_extra') }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
									
					<div class="inline-block">
						<div class="line-title"><span>Начало выборки:</span></div>
						<div class="line-value">
								<input class="inputbox" type="text" name="start_select" id="start_select" value="{{ $data['start_select'] }}" placeholder="Начало выборки">
						</div>
					</div>
					
					<div class="inline-block">
						<div class="line-title"><span>Конец выборки:</span></div>
						<div class="line-value">
							<input class="inputbox" type="text" name="end_select" id="end_select" value="{{ $data['end_select'] }}" placeholder="Конец выборки">
						</div>
					</div>
					
					<div class="inline-block">
							<div class="line-title"></div>
							<div class="line-value">
								<a href="#" class="btn add savesettings" onclick="$('#form-admin2').submit();return false;">
									<i class="fa pull-left fa-search"></i>Поиск
								</a>
							</div>
						</div>
					</div>
				
				</form>
				
				
				
				@if(count($data['gallery']) > 0)
					<table class="table-list">
						<thead>
							<tr>
								<th class="center col-1">Место</th>
								<th class="col-3">Клиент</th>
								<th class="col-3">Соц. сеть</th>
								<th class="col-5">Фото клиента</th>
							</tr>
						</thead>
						@foreach($data['gallery'] as $key => $value)
							<tr>
								<td class="center col-1">{{ $key+1 }}</td>
								<td class="col-3">{{ $value['name'] }}</td>
								<td class="col-3">{{ $value['provider'] }}</td>
								<td class="col-5">
									<table class="table-list">
										@foreach($value['item'] as $item)
											<tr>
												<td class="col-4">
													<a href="{{ $data['pathImages'].'/o_'.$item->src }}" rel="group1" class="modalbox">
														<img class="order-image" src="{{ $data['pathImages'].'/s_'.$item->src }}" alt=""  >
													</a>
												</td>
												<td class="col-4">кол-во лайков: {{ $item->like_count }}</td>
												<td></td>
											</tr>
										@endforeach
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3">ИТОГ</td>
								<td class="col-5">
									<span style="padding-right:7px;">{{ $value['count'] }} фото</span>|
									<span style="padding-left:5px;padding-right:7px;">{{ $value['all_like'] }} лайков</span>|
									<span style="padding-left:5px;padding-right:7px;">{{ $value['max_like'] }} макс. лайков</span>
								</td>
							</tr>
						@endforeach
						<tbody>
					</table>
				@endif
				
			</section>
		</div>
	</div>
</div>
@endsection