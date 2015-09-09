<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Панель управления</title>
	{!! HTML::style('/assets/admin/css/styles.css') !!}
	{!! HTML::style('/assets/admin/css/fonts.css') !!}
	
	

	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	{!! HTML::script('/assets/admin/js/script.js') !!}
	<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
</head>
<header>
	<div class="clear">
		<div id="logo"><a href="{{ route('admin') }}" title="На главную"><span><b>Панель</b> управления</span></a></div>
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
			<li class="{{ Route::is('admin.tarif.index') ? 'active' : '' }}"><a href="{{ route('admin.tarif.index') }}"><i class="fa pull-left fa-rub"></i>Тарифы</a></li>
			<li class="{{ Route::is('admin.pay.index') ? 'active' : '' }}"><a href="{{ route('admin.pay.index') }}"><i class="fa pull-left fa-exchange"></i>Транзакции</a></li>
			<li class="{{ Route::is('admin.playlist.index') ? 'active' : '' }}"><a href="{{ route('admin.playlist.index') }}"><i class="fa pull-left fa-tasks"></i>Плейлисты</a></li>
			<li class="{{ Route::is('admin.setting.index') ? 'active' : '' }}"><a href="{{ route('admin.setting.index') }}"><i class="fa pull-left fa-wrench"></i>Настройки</a></li>
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
