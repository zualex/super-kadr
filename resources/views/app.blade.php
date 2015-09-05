<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />
{!! HTML::script('/assets/superkadr/js/jquery.js') !!}
<link rel="shortcut icon" href="images/favicon.ico" />

{!! HTML::style('/assets/superkadr/css/style.css') !!}
{!! HTML::style('/assets/superkadr/css/fonts.css') !!}
{!! HTML::style('/assets/superkadr/css/croppic.css') !!}
{!! HTML::style('/assets/superkadr/css/datetimepicker.css') !!}

{!! HTML::script('/assets/superkadr/js/script.js') !!}
{!! HTML::script('/assets/superkadr/js/jquery.datetimepicker.js') !!}
{!! HTML::script('/assets/superkadr/js/croppic.js') !!}
{!! HTML::script('/assets/superkadr/js/jquery.mousewheel.min.js') !!}
{!! HTML::script('/assets/superkadr/js/bootstrap.min.js') !!}

<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<ul>

@if (Auth::guest())
	<li><a href="{{ url('/auth/login') }}">Login</a></li>
	<li><a href="/login/vkontakte">Login in with vkontakte</a></li>
	<li><a href="/login/facebook">Login in with facebook</a></li>
	<li><a href="/login/twitter">Login in with twitter</a></li>
	<li><a href="/login/odnoklassniki">Login in with odnoklassniki</a></li>
@else
	@if (Auth::user()->level == 'admin')
		<li><a href="{{ route('admin') }}">Admin panel</a></li>
	@endif

	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="{{ url('/auth/logout') }}">Logout</a></li>
		</ul>
	</li>
@endif

</ul>


	<header>
		<div class="clear">
			<div class="description"><span>Моментальное размещение фото на светодиодных экранах!</span></div>
			<div class="screen left">
				<div class="image"></div>
				<div class="info">
					<div class="title"><span>Экран 1</span></div>
					<div class="text"><span>Советский район, ул.Красноармейская (магазин М-ВИДЕО)</span></div>
				</div>
			</div>
			<div class="screen right">
				<div class="image"></div>
				<div class="info">
					<div class="title"><span>Экран 2</span></div>
					<div class="text"><span>Бежицкий район, ул.Ульянова (ТЦ "Тимошковых")</span></div>
				</div>
			</div>
			<div class="logo">
				<a href="{{ route('main') }}" title="Супер Кадр"><img src="/img/logo.png" alt=""></a>
			</div>
		</div>
	</header>
	<div id="menu">
		<div class="clear">
			<div class="menu">
				<ul>
					<li class="active"><a href="{{ route('main') }}">Главная</a></li>
					<li><a href="{{ route('gallery') }}">Галерея</a></li>
					<li><a href="#">Условия</a></li>
					<li><a href="#">Контакты</a></li>
				</ul>
			</div>
			<div id="search">
				<form>
					<input type="search" class="inputsearch" tabindex="1" maxlength="120" placeholder="Поиск">
				</form>
			</div>
		</div>
	</div>
	<div class="wrapper">
	
	
		@yield('content')
		
		
	</div>
	<footer>
		<div class="sirene">
			<a href="http://sirene.ru/" title="Интерне-агенство СИРЕНА"><span>Разработка сайта СИРЕНА AGENCY</span><img src="/img/sirene.svg" alt="Интерне-агенство СИРЕНА"></a>
		</div>
	</footer>
</body>
</html>