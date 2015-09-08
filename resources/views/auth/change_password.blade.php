<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Смена пароля</title>
	{!! HTML::style('/assets/admin/css/styles.css') !!}
	{!! HTML::style('/assets/admin/css/fonts.css') !!}
	{!! HTML::style('/assets/admin/css/datetimepicker.css') !!}
</head>
<body class="auth">
	<div id="auth" class="login-block">
		<div>
			<div class="logo"><span><b>Панель</b>Управления</span></div>
			
			@if (count($errors) > 0)
				<div class="alert alert-danger">
					Произошла ошибка<br><br>
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
			
			
			<div class="body">
			<form role="form" method="POST" action="{{ route('change_password.save') }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					
					<div class="inline-block">
						<div class="field login">
							<input type="password"  name="password_old" placeholder="Старый пароль">
							<i class="fa fa-lock"></i>
						</div>
						<div class="field password">
							<input type="password"  name="password_new" placeholder="Новый пароль">
							<i class="fa fa-lock"></i>
						</div>
						<div class="field password">
							<input type="password"  name="password_repeat" placeholder="Повторите новый пароль">
							<i class="fa fa-lock"></i>
						</div>
					</div>
					<div class="inline-block">
						<input class="auth-submit" type="submit" name="login" value="Изменить пароль">
					</div>
				</form>
			</div>
		</div>
	</div>
	

</body>
</html>
