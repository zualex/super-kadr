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