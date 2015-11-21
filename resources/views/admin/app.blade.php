<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Панель управления</title>
	{!! HTML::style('/assets/admin/css/styles.css') !!}
	{!! HTML::style('/assets/admin/css/fonts.css') !!}
	
	<link rel="shortcut icon" href="/public/img/favicon.ico" />

	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	
	{!! HTML::style('/assets/admin/lib/fancybox/jquery.fancybox.css') !!}
	{!! HTML::style('/assets/admin/lib/fancybox/helpers/jquery.fancybox-thumbs.css') !!}
	{!! HTML::script('/assets/admin/lib/fancybox/jquery.fancybox.pack.js') !!}
	{!! HTML::script('/assets/admin/lib/fancybox/helpers/jquery.fancybox-thumbs.min.js') !!}
	
	{!! HTML::script('/assets/admin/js/script.js') !!}
	<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
	
	
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script>
	$(function(){
		$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
		});
	});
	</script>
	
</head>
<header>
	<div class="clear">
		<div id="logo">
			<a href="{{ route('admin') }}" title="На главную"><span><b>Панель</b> управления</span></a>
			
			@if($mainSetting['off_site'] == 1)
				<span class="description" style="padding-left:150px;">Сайт выключен для пользователей!</span>
			@endif
			
		</div>
		<div class="profile">
			<div class="name">
			<span><i class="fa pull-left fa-user"></i>{{ Auth::user()->name }}</span>
				<div class="controls">
					<div>
						<ul>
							<li><a href="{{ route('change_password') }}">Изменить пароль</a></li>
							<li><a href="{{ url('/auth/logout') }}">Выйти</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<div id="wrapper" class="wrapper clear">
	<div id="menu" class="menu-panel">
		<ul>
			<li class="{{ Route::is('admin.gallery.index') ? 'active' : '' }}"><a href="{{ route('admin.gallery.index') }}"><i class="fa pull-left fa-credit-card"></i>Заказы</a></li>
			<li class="{{ Route::is('admin.competition.index') ? 'active' : '' }}"><a href="{{ route('admin.competition.index') }}"><i class="fa pull-left fa-credit-card"></i>Статистика</a></li>
			@if (Auth::user()->level == 'admin')
				<li class="{{ Route::is('admin.pay.index') ? 'active' : '' }}"><a href="{{ route('admin.pay.index') }}"><i class="fa pull-left fa-exchange"></i>Транзакции</a></li>
			@endif
			<li class="{{ Route::is('admin.playlist.index') ? 'active' : '' }}"><a href="{{ route('admin.playlist.index') }}"><i class="fa pull-left fa-tasks"></i>Плейлисты</a></li>
			@if (Auth::user()->level == 'admin')
				<li class="{{ Route::is('admin.monitor.index') ? 'active' : '' }}"><a href="{{ route('admin.monitor.index') }}"><i class="fa pull-left fa-tasks"></i>Экраны</a></li>
				<li class="{{ Route::is('admin.setting.index') ? 'active' : '' }}"><a href="{{ route('admin.setting.index') }}"><i class="fa pull-left fa-wrench"></i>Настройки</a></li>
				<li class="{{ Route::is('admin.users.index') ? 'active' : '' }}"><a href="{{ route('admin.users.index') }}"><i class="fa pull-left fa-user"></i>Пользователи</a></li>
			@endif
		</ul>
	</div>
	<div class="wrap">
		<div class="content">
			<div class="clear">
			
				
				@yield('content')
				
				
			</div>
		</div>
	</div>
</div>
</body>
</html>
