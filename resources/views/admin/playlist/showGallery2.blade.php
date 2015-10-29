	<div class="content-block orders">
		<table class="table-list">
			<tr>
				<th colspan="6" style="text-align:center;">Заказы в очереди на генерацию нового файла плейлиста {{ $data['dateStart2'] }}</th>
			</tr>
			@if(count($data['playlistFinaly2']) > 0)
				<tr>
					<th class="col-1"></th>
					<th class="center col-2">ID</th>
					<th class="center col-2">ID блока</th>
					<th class="col-3" style="display:none">Состояние</th>
					<th class="center col-3">Изображение</th>
					<th>Ссылка</th>
					<th class="col-3">Тариф</th>
					<th class="center col-btn"  style="display:none"><i class="fa fa-th-list"></i></th>
				</tr>
			
				@foreach($data['playlistFinaly2'] as $key => $item)
					@if($item['init'] == 0)
						<tr>
							<td class="col-1"><div class="light c-{{ $item['block'] }}"></div></td>
							<td class="center col-2">{{ $item['id'] }}</td>
							<td class="center col-2">{{ $item['block'] }}</td>
							<td class="center col-3" style="display:none">
								<input id="orders-enable-2" class="toggle" name="enable" type="checkbox">
								<label for="orders-enable-2"></label>
							</td>
							<td class="center col-3"><img class="order-image" src="{{ $data['pathImages'].'/m_'.$item['name'] }}" alt=""></td>
							<td>{{ $data['folderName'].'\\'.$item['name'] }}</td>
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
					@endif
				@endforeach
			@else
			<tr>
				<td colspan="4">Нет заказов</th>
			</tr>
			@endif
		</table>
	</div>