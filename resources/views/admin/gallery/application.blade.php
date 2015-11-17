@if(count($data['galleryModeration']) > 0)
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
								<td class="center col-2">
									@if($value->user_name != '')
										{{ $value->user_name }} - {{ $value->provider }}
									@else
										Анонимный пользователь
									@endif
								</td>
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