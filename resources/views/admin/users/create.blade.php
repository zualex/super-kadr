@extends('admin.app')

@section('content')
<div>
	<div class="header">
		<div class="clear">
			<div class="title"><span>Добавление пользователя</span></div>
		</div>
	</div>

	@if (Session::has('message'))
		<br>
		<div class="alert alert-info">{{ Session::get('message') }}</div>
	@endif
	
	@if(count($errors) > 0)
		<div class="alert alert-info">{!! HTML::ul($errors->all()) !!}</div>
	@endif
	
	
	<div class="site-settings tab-block" style="padding: 10px 20px;">
		
		<form id="form-admin" role="form" method="POST" action="{{ route('admin.users.store') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			
			<div class="inline-block">
				<div class="line-title"><span>Имя</span></div>
				<div class="line-value">
					<input class="inputbox" type="text" name="name" value="" placeholder="">
				</div>
			</div>
			
			<div class="inline-block">
				<div class="line-title"><span>Email</span></div>
				<div class="line-value">
					<input class="inputbox" type="text" name="email" value="" placeholder="">
				</div>
			</div>
			
			<div class="inline-block">
				<div class="line-title"><span>Роль</span></div>
				<div class="line-value">
					<select name="level" class="selectbox">
						<option value="moderator">Модератор</option>
						<option value="admin">Администратор</option>
					</select>
				</div>
			</div>
			
			<div class="inline-block">
				<div class="line-title"><span>Пароль</span></div>
				<div class="line-value">
					<input class="inputbox" type="password" name="password" value="" placeholder="">
				</div>
			</div>
			
			<div class="inline-block">
				<div class="line-title"><span>Еще раз пароль</span></div>
				<div class="line-value">
					<input class="inputbox" type="password" name="password_confirmation" value="" placeholder="">
				</div>
			</div>
			
			<div class="inline-block">
				<div class="btn-group">
					<a class="btn add" onclick="$('#form-admin').submit();"><i class="fa pull-left fa-plus-square-o"></i>Создать</a>
					<a href="/admin/users/" class="btn cancel"><i class="fa pull-left fa-times-circle"></i>Отмена</a>
				</div>
			</div>
		</form>
		
	</div>
</div>
@endsection