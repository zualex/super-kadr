@extends('admin.app')

@section('content')
<div class="transactions">
	<div class="header">
		<div class="clear">
			<div class="title"><span>Пользователи</span></div>			
		</div>
	</div>
	
	@if (Session::has('message'))
		<br>
		<div class="alert alert-info">{{ Session::get('message') }}</div>
	@endif
	
	@if(count($errors) > 0)
		<div class="alert alert-info">{!! HTML::ul($errors->all()) !!}</div>
	@endif
	
	<table class="table-list">
		<tr>
			<th class="center col-2">ID</th>
			<th>Имя</th>
			<th class="col-5">E-mail</th>
			<th class="col-4">Роль</th>
			<th class="center col-btn"><i class="fa fa-th-list"></i></th>
		</tr>
		 @foreach($users as $key => $value)
				<tr>
					<td>{{ $value->id }}</td>
					<td>{{ $value->name }}</td>
					<td >{{ $value->email }}</td>
					<td>{{ $value->level }}</td>
					<td class="center col-btn">
						<div class="droplist-group">
							<i class="fa fa-ellipsis-v"></i>
							<div class="droplist" style="display: none">
								<div>
									<ul>
										<li><a href="{{ route('admin.users.edit', $value->id) }}"><i class="fa pull-left fa-pencil"></i>Изменить</a></li>
										<li><a href="{{ route('admin.users.destroy', $value->id) }}" onclick="return confirm('Вы действительно хотите удалить пользователя?')"><i class="fa pull-left fa-times"></i>Удалить</a></li>
									</ul>
								</div>
							</div>
						</div>
					</td>
				</tr>
		@endforeach
	</table>
</div>
@endsection