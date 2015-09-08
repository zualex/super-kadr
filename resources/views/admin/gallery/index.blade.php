@extends('admin.app')

@section('content')
<div id="orders">
							<div class="header">
								<div class="clear">
									<div class="title"><span>Заказы</span></div>
									<div class="controls">
										<div class="btn-group">
											<a href="" class="btn add"><i class="fa pull-left fa-floppy-o"></i>Одобрить</a>
											<a href="" class="btn info"><i class="fa pull-left fa-floppy-o"></i>На модерацию</a>
											<a href="" class="btn del"><i class="fa pull-left fa-trash"></i>Удалить</a>
										</div>
									</div>
								</div>
							</div>
							<div class="content-orders tab-block">
								<div class="orders tabs">
									<ul>
										<li class="active">Заявки</li>
										<li>Одобренные</li>
									</ul>
								</div>
								<div class="box-content">
									<section id="offer" class="visible">
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
												<tr>
													<td class="center col-1">
														<input type="checkbox" name="checkelement[]" value="">
													</td>
													<td class="center col-2">2</td>
													<td class="center col-3"><img class="order-image" src="http://kadr.test32.ru/upload/gallery/thumbs/image501.jpg" alt=""></td>
													<td>Изображение будет показано 12 раз в течение 1 часа<br>Начало показа 12:00 18.08.2015</td>
													<td class="col-4">AA0001</td>
													<td class="col-4">Просто</td>
													<td class="col-5">
														<span class="status wait"><i class="fa pull-left fa-clock-o"></i>Ожидает подтверждения</span>
													</td>
													<td class="center col-btn">
														<div class="droplist-group">
															<i class="fa fa-ellipsis-v"></i>
															<div class="droplist" style="display: none">
																<div>
																	<ul>
																		<li><a><i class="fa pull-left fa-chevron-down"></i>Принять</a></li>
																		<li><a><i class="fa pull-left fa-ban"></i>Отклонить</a></li>
																	</ul>
																</div>
															</div>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</section>
									<section id="order">
										<table class="table-list">
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
											<tr id="5">
												<td class="center col-1">
													<input type="checkbox" name="checkelement[]" value="">
												</td>
												<td class="center col-2">5</td>
												<td class="center col-3"><img class="order-image" src="http://kadr.test32.ru/upload/gallery/thumbs/image501.jpg" alt=""></td>
												<td>Изображение будет показано 12 раз в течение 1 часа<br>Начало показа 12:00 18.08.2015</td>
												<td class="col-4">AA0001</td>
												<td class="col-4">Просто</td>
												<td class="col-5">
													<span class="status cancel"><i class="fa pull-left fa-ban"></i>Отменено</span>
												</td>
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
											<tr id="3">
												<td class="center col-1">
													<input type="checkbox" name="checkelement[]" value="">
												</td>
												<td class="center col-2">3</td>
												<td class="center col-3"><img class="order-image" src="http://kadr.test32.ru/upload/gallery/thumbs/image501.jpg" alt=""></td>
												<td>Изображение будет показано 12 раз в течение 1 часа<br>Начало показа 12:00 18.08.2015</td>
												<td class="col-4">AA0001</td>
												<td class="col-4">Просто</td>
												<td class="col-5">
													<span class="status execute"><i class="fa pull-left fa-refresh fa-spin"></i>В исполнении</span>
												</td>
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
											<tr id="1">
												<td class="center col-1">
													<input type="checkbox" name="checkelement[]" value="">
												</td>
												<td class="center col-2">1</td>
												<td class="center col-3"><img class="order-image" src="http://kadr.test32.ru/upload/gallery/thumbs/image501.jpg" alt=""></td>
												<td>Изображение будет показано 12 раз в течение 1 часа<br>Начало показа 12:00 18.08.2015</td>
												<td class="col-4">AA0001</td>
												<td class="col-4">Просто</td>
												<td class="col-5">
													<span class="status ok"><i class="fa pull-left fa-chevron-down"></i>Выполнено</span>
												</td>
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
									</section>
								</div>
							</div>
					</div>
@endsection