@extends('admin.app')

@section('content')
<div class="transactions">
							<div class="header">
								<div class="clear">
									<div class="title"><span>Транзакции</span></div>
								</div>
							</div>


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
										<tr id="">
											<td class="center col-1">
												<input type="checkbox" name="checkelement[]" value="">
											</td>
											<td class="center col-2"></td>
											<td>User01 оплатил заказ AA0001 (Подробнее)</td>
											<td class="col-4">150 руб.</td>
											<td class="col-5">11:24 16.08.2015</td>
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
										<tr id="">
											<td class="center col-1">
												<input type="checkbox" name="checkelement[]" value="">
											</td>
											<td class="center col-2"></td>
											<td>User02 оплатил заказ AA0002 (Подробнее)</td>
											<td class="col-4">150 руб.</td>
											<td class="col-5">22:17 18.08.2015</td>
											<td class="col-4">
												<span class="status cancel"><i class="fa pull-left fa-exclamation-triangle"></i>Ошибка</span>
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
										<tr id="">
											<td class="center col-1">
												<input type="checkbox" name="checkelement[]" value="">
											</td>
											<td class="center col-2"></td>
											<td>User03 оплатил заказ AA0003 (Подробнее)</td>
											<td class="col-4">300 руб.</td>
											<td class="col-5">11:30 19.08.2015</td>
											<td class="col-4">
												<span class="status cancel"><i class="fa pull-left fa-times-circle"></i>Отклонено</span>
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
									</table>
									
								</form>
							</div>
		
@endsection