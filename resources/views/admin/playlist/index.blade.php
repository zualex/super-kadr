@extends('admin.app')

@section('content')
<div id="orders">
							<div class="header">
								<div class="clear">
									<div class="title"><span>Плейлисты</span></div>
								</div>
							</div>
							<div class="playlists">
								<div class="content-block original">
									<table class="table-list">
										<tr>
											<th class="center col-2">ID</th>
											<th class="col-3">Состояние</th>
											<th>Ссылка</th>
											<th class="col-3">Повторяется</th>
											<th class="col-4">Продолжительность</th>
											<th class="col-4">Низкий приоритет</th>
											<th class="center col-btn"><i class="fa fa-th-list"></i></th>
										</tr>
										<tr id="">
											<td class="center col-2"></td>
											<td class="center col-3">
												<input id="original-enable-" class="toggle" name="enable" type="checkbox">
												<label for="original-enable-"></label>
											</td>
											<td></td>
											<td class="col-3"> раз</td>
											<td class="col-4"> сек</td>
											<td class="col-4">
												<input id="original-priority-" class="toggle" name="priority" type="checkbox">
												<label for="original-priority-"></label>
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
										<tr id="2">
											<td class="center col-2">2</td>
											<td class="center col-3">
												<input id="original-enable-2" class="toggle" name="enable" type="checkbox">
												<label for="original-enable-2"></label>
											</td>
											<td>C:\Ролики\Ролики\БКС\280_BKS_1.avi</td>
											<td class="col-3">0 раз</td>
											<td class="col-4">10 сек</td>
											<td class="col-4">
												<input id="original-priority-2" class="toggle" name="priority" type="checkbox">
												<label for="original-priority-2"></label>
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