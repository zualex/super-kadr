<div class="content-block original">
	@if(count($data['initPlaylist']) > 0)
		<table class="table-list">
			<tr>
				<th class="center col-2">ID</th>
				<th class="center col-2">ID блока</th>
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
					<td class="center col-2">{{ $value->idblock }}</td>
					
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