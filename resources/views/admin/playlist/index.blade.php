@extends('admin.app')

@section('content')
<div id="orders">
<div class="header">
	<div class="clear">
		<div class="title"><span>Плейлисты</span></div>
	</div>
</div>


		@if (Session::has('message'))
			<br>
			<div class="alert alert-info">{{ Session::get('message') }}</div>
		@endif


<div class="playlists">
	<div class="content-block original">
		@if(count($data['initPlaylist']) > 0)
			<table class="table-list">
				<tr>
					<th class="center col-2">ID</th>
					<th class="col-3">Состояние</th>
					<th>Ссылка</th>
					<th class="col-3">Повторяется</th>
					<th class="col-4">Продолжительность</th>
					<th class="col-4" style="display:none">Низкий приоритет</th>
					<th class="col-3">IsTime</th>
					<th class="col-3">Экран</th>
					<th class="center col-btn"><i class="fa fa-th-list"></i></th>
				</tr>
		
				@foreach($data['initPlaylist'] as $key => $value)
					<tr>
						<td class="center col-2">{{ $value->id }}</td>
						<td class="center col-3">
							@if($value->enable == 1)
								<input id="original-enable-{{ $value->id }}" class="toggle" name="enable" type="checkbox" value="1" checked>
							@else
								<input id="original-enable-{{ $value->id }}" class="toggle" name="enable" type="checkbox" value="1">
							@endif
							<label for="original-enable-{{ $value->id }}"></label>
						</td>
						<td>{{ $value->name }}</td>
						<td class="col-3">{{ $value->loop_xml }} раз</td>
						<td class="col-4">{{ $value->time }} сек</td>
						<td class="col-4" style="display:none">
							<input id="original-priority-" class="toggle" name="priority" type="checkbox">
							<label for="original-priority-"></label>
						</td>
						<td class="center col-3">
							@if($value->is_time == 1)
								<input id="original-is_time-{{ $value->id }}" class="toggle" name="is_time" type="checkbox" value="1" checked>
							@else
								<input id="original-is_time-{{ $value->id }}" class="toggle" name="is_time" type="checkbox" value="1">
							@endif
							<label for="original-is_time-{{ $value->id }}"></label>
						</td>
						<td class="col-3">Экран {{ $value->monitor->number }}</td>
						<td class="center col-btn">
							<div class="droplist-group">
								<i class="fa fa-ellipsis-v"></i>
								<div class="droplist" style="display: none">
									<div>
										<ul>
											<li><a href="{{ route('admin.playlist.delete', $value->id) }}" onclick="return confirm('Вы действительно хотите удалить данную запись?')"><i class="fa pull-left fa-times"></i>Удалить</a></li>
										</ul>
									</div>
								</div>
							</div>
						</td>
					</tr>
				@endforeach
				
			</table>
		@endif
	</div>
	<div class="content-block orders">
		<table class="table-list">
			<tr>
				<th class="center col-2">ID</th>
				<th class="col-3">Состояние</th>
				<th class="center col-3">Изображение</th>
				<th>Ссылка</th>
				<th class="col-4">Тариф</th>
				<th class="center col-btn"><i class="fa fa-th-list"></i></th>
			</tr>
			<tr id="">
				<td class="center col-2"></td>
				<td class="center col-3">
					<input id="orders-enable-" class="toggle" name="enable" type="checkbox">
					<label for="orders-enable-"></label>
				</td>
				<td class="center col-3"><img class="order-image" src="" alt=""></td>
				<td></td>
				<td class="col-4"></td>
				<td class="center col-btn">
					<div class="droplist-group">
						<i class="fa fa-ellipsis-v"></i>
						<div class="droplist" style="display: none">
							<div>
								<ul>
									<li><a><i class="fa pull-left fa-times"></i>Удалить</a></li>
								</ul>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<tr id="2">
				<td class="center col-2">2</td>
				<td class="center col-3">
					<input id="orders-enable-2" class="toggle" name="enable" type="checkbox">
					<label for="orders-enable-2"></label>
				</td>
				<td class="center col-3"><img class="order-image" src="http://kadr.test32.ru/upload/gallery/thumbs/image501.jpg" alt=""></td>
				<td>C:\Изображения\Изображения\image501.jpg</td>
				<td class="col-4">Просто</td>
				<td class="center col-btn">
					<div class="droplist-group">
						<i class="fa fa-ellipsis-v"></i>
						<div class="droplist" style="display: none">
							<div>
								<ul>
									<li><a><i class="fa pull-left fa-times"></i>Удалить</a></li>
								</ul>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="content-block result">
		<table class="table-list">
			<tr>
				<th class="center col-2">ID</th>
				<th class="center col-3">Изображение</th>
				<th>Ссылка</th>
				<th class="col-3">Повторяется</th>
				<th class="col-4">Продолжительность</th>
			</tr>
			<tr id="">
				<td class="center col-2"></td>
				<td class="center col-3"><img class="order-image" src="" alt=""></td>
				<td></td>
				<td class="col-3">0 раз</td>
				<td class="col-4">10 сек</td>
			</tr>
			<tr id="2">
				<td class="center col-2">2</td>
				<td class="center col-3"><img class="order-image" src="http://kadr.test32.ru/upload/gallery/thumbs/image501.jpg" alt=""></td>
				<td>C:\Изображения\Изображения\image501.jpg</td>
				<td class="col-3">0 раз</td>
				<td class="col-4">5 сек</td>
			</tr>
		</table>
	</div>
</div>
</div>			
@endsection