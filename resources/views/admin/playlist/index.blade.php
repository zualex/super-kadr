@extends('admin.app')

@section('content')
<div id="orders">
<div class="header">
	<div class="clear">
		<div class="title"><span>Плейлисты</span></div>
	</div>
</div>



<div class="content-orders tab-block">
	<div class="orders tabs">
		<ul>
			<li class="active">Экран 1</li>
			<li>Экран 2</li>
		</ul>
	</div>
		
		

		@if (Session::has('message'))
			<br>
			<div class="alert alert-info">{{ Session::get('message') }}</div>
		@endif


		
		
<div class="playlists tab-block">
	<section  class="visible">	
	
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
					<th class="center col-btn"><i class="fa fa-th-list"></i></th>
				</tr>
		
				@foreach($data['initPlaylist'] as $key => $value)
					@if($value->monitor->number == 1)
					<tr>
						<td class="center col-2">{{ $value->id }}</td>
						<td class="center col-3">
							@if($value->enable == 1)
								<input id="original-enable-{{ $value->id }}" class="toggle" name="enable" type="checkbox" value="1" checked onchange="saveFieldCheckbox(this, '{{ route('admin.playlist.enable', $value->id) }}')">
							@else
								<input id="original-enable-{{ $value->id }}" class="toggle" name="enable" type="checkbox" value="1" onchange="saveFieldCheckbox(this, '{{ route('admin.playlist.enable', $value->id) }}')">
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
								<input id="original-is_time-{{ $value->id }}" class="toggle" name="is_time" type="checkbox" value="1" checked onchange="saveFieldCheckbox(this, '{{ route('admin.playlist.isTime', $value->id) }}')">
							@else
								<input id="original-is_time-{{ $value->id }}" class="toggle" name="is_time" type="checkbox" value="1" onchange="saveFieldCheckbox(this, '{{ route('admin.playlist.isTime', $value->id) }}')">
							@endif
							<label for="original-is_time-{{ $value->id }}"></label>
						</td>
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
					@endif
				@endforeach	
			</table>
		@endif
	</div>
	
	
	<div class="content-block orders">
		<table class="table-list">
			<tr>
				<th colspan="4" style="text-align:center;">Заказы в очереди на генерацию нового файла плейлиста {{ $data['dateStart1'] }}</th>
			</tr>
			@if(count($data['galleryGeneration_1']) > 0)
				<tr>
					<th class="center col-2">ID</th>
					<th class="col-3" style="display:none">Состояние</th>
					<th class="center col-3">Изображение</th>
					<th>Ссылка</th>
					<th class="col-3">Тариф</th>
					<th class="center col-btn"  style="display:none"><i class="fa fa-th-list"></i></th>
				</tr>
			
				@foreach($data['galleryGeneration_1'] as $countPlaylist => $arrItem)
					<tr>
						<th colspan="4">Набор {{ $countPlaylist+1 }} заказов</th>
					</tr>
					@foreach($arrItem as $key => $item)
						<tr>
							<td class="center col-2">{{ $item['id'] }}</td>
							<td class="center col-3" style="display:none">
								<input id="orders-enable-2" class="toggle" name="enable" type="checkbox">
								<label for="orders-enable-2"></label>
							</td>
							<td class="center col-3"><img class="order-image" src="{{ $data['pathImages'].'/m_'.$item['src'] }}" alt=""></td>
							<td>{{ $data['folderName'].'\\'.$item['src'] }}</td>
							<td class="col-3">{{ $data['tarif'][$item['tarif_id']]->name }}</td>
							<td class="center col-btn"  style="display:none">
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
					@endforeach
				@endforeach
			@else
			<tr>
				<td colspan="4">Нет заказов</th>
			</tr>
			@endif
		</table>
	</div>
	
	
		

	<div class="content-block result" style="display:none">
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
	
	
	</section>
	
	
	
	
	<section >
	
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
					<th class="center col-btn"><i class="fa fa-th-list"></i></th>
				</tr>
		
				@foreach($data['initPlaylist'] as $key => $value)
					@if($value->monitor->number == 2)
					<tr>
						<td class="center col-2">{{ $value->id }}</td>
						<td class="center col-3">
							@if($value->enable == 1)
								<input id="original-enable-{{ $value->id }}" class="toggle" name="enable" type="checkbox" value="1" checked onchange="saveFieldCheckbox(this, '{{ route('admin.playlist.enable', $value->id) }}')">
							@else
								<input id="original-enable-{{ $value->id }}" class="toggle" name="enable" type="checkbox" value="1" onchange="saveFieldCheckbox(this, '{{ route('admin.playlist.enable', $value->id) }}')">
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
								<input id="original-is_time-{{ $value->id }}" class="toggle" name="is_time" type="checkbox" value="1" checked onchange="saveFieldCheckbox(this, '{{ route('admin.playlist.isTime', $value->id) }}')">
							@else
								<input id="original-is_time-{{ $value->id }}" class="toggle" name="is_time" type="checkbox" value="1" onchange="saveFieldCheckbox(this, '{{ route('admin.playlist.isTime', $value->id) }}')">
							@endif
							<label for="original-is_time-{{ $value->id }}"></label>
						</td>
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
					@endif
				@endforeach	
			</table>
		@endif
	</div>
	
	
			<div class="content-block orders">
			<table class="table-list">
				<tr>
					<th colspan="4" style="text-align:center;">Заказы в очереди на генерацию нового файла плейлиста для Экран 2 {{ $data['dateStart2'] }}</th>
				</tr>
				@if(count($data['galleryGeneration_2']) > 0)
					<tr>
						<th class="center col-2">ID</th>
						<th class="col-3" style="display:none">Состояние</th>
						<th class="center col-3">Изображение</th>
						<th>Ссылка</th>
						<th class="col-3">Тариф</th>
						<th class="center col-btn"  style="display:none"><i class="fa fa-th-list"></i></th>
					</tr>
				
					@foreach($data['galleryGeneration_2'] as $countPlaylist => $arrItem)
						<tr>
							<th colspan="4">Набор {{ $countPlaylist+1 }} заказов</th>
						</tr>
						@foreach($arrItem as $key => $item)
							<tr>
								<td class="center col-2">{{ $item['id'] }}</td>
								<td class="center col-3" style="display:none">
									<input id="orders-enable-2" class="toggle" name="enable" type="checkbox">
									<label for="orders-enable-2"></label>
								</td>
								<td class="center col-3"><img class="order-image" src="{{ $data['pathImages'].'/m_'.$item['src'] }}" alt=""></td>
								<td>{{ $data['folderName'].'\\'.$item['src'] }}</td>
								<td class="col-3">{{ $data['tarif'][$item['tarif_id']]->name }}</td>
								<td class="center col-btn"  style="display:none">
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
						@endforeach
					@endforeach
				@else
				<tr>
					<td colspan="4">Нет заказов</th>
				</tr>
				@endif
			</table>
		</div>
	</section>
	
	
	
	
	
</div>
</div>
</div>			
@endsection