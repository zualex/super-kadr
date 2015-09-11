@extends('admin.app')

@section('content')
<div class="transactions">
	<div class="header">
		<div class="clear">
			<div class="title"><span>Транзакции</span></div>
		</div>
	</div>

	@if(count($pay) > 0)
		<form method="post">
		<table class="table-list">
			<tr>
				<th class="center col-1"><input class="checkbox" type="checkbox" name="checkall" value=""></th>
				<th class="center col-2">ID</th>
				<th>Операция</th>
				<th class="col-4">Сумма</th>
				<th class="col-5">Дата</th>
				<th class="col-4">Состояние</th>
				<th class="center col-btn"><i class="fa fa-th-list"></i></th>
			</tr>
			@foreach($pay as $key => $value)
				<tr>
					<td class="center col-1"><input type="checkbox" name="checkelement[]" value="{{ $value->id }}"></td>
					<td class="center col-2"> {{ $value->id }} </td>
					<td>{{ $value->name }}</td>
					<td class="col-4">{{ $value->price }} руб.</td>
					<td class="col-5">{{ $value->created_at }}</td>
					<td class="col-4">
						<span class="status ok"><i class="fa pull-left fa-chevron-down"></i>Оплачено</span>
					</td>
					<td class="center col-btn">
						<div class="droplist-group">
							<i class="fa fa-ellipsis-v"></i>
							<div class="droplist" style="display: none">
								<div>
									<ul>
										<li><a onclick="r_delete({id})" class="deny"><i class="fa pull-left fa-times"></i>Удалить</a></li>
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