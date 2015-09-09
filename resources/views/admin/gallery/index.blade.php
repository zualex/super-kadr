@extends('admin.app')

@section('content')
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
		
		<form id="form-admin" role="form" method="POST" >
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		
		<div class="box-content">
			<section id="offer" class="visible">
				@if(count($data['galleryModeration']) > 0)
					<table class="table-list">
						<thead>
							<tr>
								<th class="center col-1"><input class="checkbox" type="checkbox" name="checkall" value=""></th>
								<th class="center col-2">ID</th>
								<th class="center col-3">Изображение</th>
								<th>Описание</th>
								<th class="col-4">Заказ</th>
								<th class="col-4">Тариф</th>
								<th class="col-5">Статус</th>
								<th class="center col-btn"><i class="fa fa-th-list"></i></th>
							</tr>
						</thead>
						<tbody>
						@foreach($data['galleryModeration'] as $key => $value)
							<tr>
								<td class="center col-1">
									<input type="checkbox" name="checkelement[]" value="{{ $value->id }}">
								</td>
								<td class="center col-2">{{ $value->id }}</td>
								<td class="center col-3">
									<a href="{{ $data['pathImages'].'/o_'.$value->src }}" rel="group1" class="modalbox">
										<img class="order-image" src="{{ $data['pathImages'].'/s_'.$value->src }}" alt="">
									</a>
								</td>
								<td>Изображение будет показано {{ $value->hours*60*60/$value->interval_sec }} раз в течение {{ $value->hours }} часа<br>Начало показа {{ $value->date_show }}</td>
								<td class="col-4">AA{{ $value->pay_id }}</td>
								<td class="col-4">{{ $value->tarif_name }}</td>
								<td class="col-5">
									<span class="status wait"><i class="fa pull-left fa-clock-o"></i>Ожидает подтверждения</span>
								</td>
								<td class="center col-btn">
									<div class="droplist-group">
										<i class="fa fa-ellipsis-v"></i>
										<div class="droplist" style="display: none">
											<div>
												<ul>
													<li><a href="{{ route('admin.gallery.success', $value->id) }}"><i class="fa pull-left fa-chevron-down"></i>Принять</a></li>
													<li><a href="{{ route('admin.gallery.cancel', $value->id) }}"><i class="fa pull-left fa-ban"></i>Отклонить</a></li>
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
			<section id="order">
				
				@if(count($data['gallerySuccess']) > 0)
					<table class="table-list">
						<thead>
							<tr>
								<th class="center col-1"><input class="checkbox" type="checkbox" name="checkall" value=""></th>
								<th class="center col-2">ID</th>
								<th class="center col-3">Изображение</th>
								<th>Описание</th>
								<th class="col-4">Заказ</th>
								<th class="col-4">Тариф</th>
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
								<td class="center col-3">
									<a href="{{ $data['pathImages'].'/o_'.$value->src }}" rel="group2" class="modalbox">
										<img class="order-image" src="{{ $data['pathImages'].'/s_'.$value->src }}" alt="">
									</a>
								</td>
								<td>Изображение будет показано {{ $value->hours*60*60/$value->interval_sec }} раз в течение {{ $value->hours }} часа<br>Начало показа {{ $value->date_show }}</td>
								<td class="col-4">AA{{ $value->pay_id }}</td>
								<td class="col-4">{{ $value->tarif_name }}</td>
								<td class="col-5">
									@if($value->status_caption == 'process')
										<span class="status execute"><i class="fa pull-left fa-refresh fa-spin"></i>{{ $value->status_name }}</span>
									@elseif($value->status_caption == 'success')
										<span class="status ok"><i class="fa pull-left fa-chevron-down"></i>{{ $value->status_name }}</span>
									@else
										<span class="status execute">{{ $value->status_name }}</span>
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
								<th class="center col-3">Изображение</th>
								<th>Описание</th>
								<th class="col-4">Заказ</th>
								<th class="col-4">Тариф</th>
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
								<td class="center col-3">
									<a href="{{ $data['pathImages'].'/o_'.$value->src }}" rel="group3" class="modalbox">
										<img class="order-image" src="{{ $data['pathImages'].'/s_'.$value->src }}" alt="">
									</a>
								</td>
								<td>Изображение будет показано {{ $value->hours*60*60/$value->interval_sec }} раз в течение {{ $value->hours }} часа<br>Начало показа {{ $value->date_show }}</td>
								<td class="col-4">AA{{ $value->pay_id }}</td>
								<td class="col-4">{{ $value->tarif_name }}</td>
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