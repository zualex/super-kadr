<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{{ $mainSetting['title'] }}</title>
<meta name="description" content="{{ $mainSetting['description'] }}" />
<meta name="keywords" content="{{ $mainSetting['keywords'] }}" />

<meta property="og:site_name" content="{{ $mainSetting['title'] }}" />
<meta property="og:title" content="{{ $mainSetting['title'] }}" />
<meta property="og:url" content="{{ Request::url() }}" />


{!! HTML::script('/assets/superkadr/js/jquery.js') !!}
<link rel="shortcut icon" href="/public/img/favicon.ico" />

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
<body>
	<header>
		<div class="clear">
			@if($mainSetting['off_site'] == 1)
				<div class="description" style="color: #FE5252"><span>Сайт выключен для пользователей!</span></div>
			@endif
			
			
			<div class="description"><span>Моментальное размещение фото на светодиодных экранах!</span></div>
			<div class="screen left">
				<div class="image"></div>
				<div class="status online"><i class="fa pull-left fa-circle"></i><span>онлайн трансляция</span></div>
				<div class="info">
					<div class="title"><span>Экран 1</span></div>
					<div class="text"><span>Советский район, ул.Красноармейская (магазин М-ВИДЕО)</span></div>
				</div>
			</div>
			<div class="screen right">
				<div class="image"></div>
				<div class="status offline"><i class="fa pull-left fa-circle"></i><span>онлайн трансляция</span></div>
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
				
					<li class="{{ Route::is('main') ? 'active' : '' }}"><a href="{{ route('main') }}">главная</a></li>
					<li class="{{ Route::is('gallery') ? 'active' : '' }} {{ Route::is('gallery.show') ? 'active' : '' }}"><a href="{{ route('gallery') }}">галерея</a></li>
					<li class="{{ Route::is('competition') ? 'active' : '' }}"><a href="{{ route('competition') }}">галерея конкурса</a></li>
					<li class="{{ Route::is('conditions') ? 'active' : '' }}"><a href="{{ route('conditions') }}">условия</a></li>
					<li class="{{ Route::is('contacts') ? 'active' : '' }}"><a href="{{ route('contacts') }}">контакты</a></li>
				</ul>
			</div>
			<div id="login">
				@if (Auth::guest())
					<div class="auth">
						<a href="{{ url('/auth/login') }}" rel="nofollow" class="sbutton login"><span><i class="fa pull-left fa-sign-in"></i>Войти</span></a>
						
						@if($mainSetting['authorization'] == 1)
							<a href="/login/vkontakte" rel="nofollow" class="sbutton social vk"><span><i class="fa fa-vk"></i></span></a>
							<a href="/login/facebook" rel="nofollow" class="sbutton social fb"><span><i class="fa fa-facebook"></i></span></a>
							<a href="/login/twitter" rel="nofollow" class="sbutton social tw"><span><i class="fa fa-twitter"></i></span></a>
							<a href="/login/odnoklassniki" rel="nofollow" class="sbutton social ok"><span><i class="fa fa-odnoklassniki"></i></span></a>
						@endif

					</div>
				@else

					<div class="profile">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							<span class="username">{{ Auth::user()->name }}<i class="fa pull-right fa-caret-down"></i></span>
						</a>
						<div class="dropdown" role="menu">
							@if (Auth::user()->level == 'admin' || Auth::user()->level == 'moderator')
								<a href="{{ route('admin') }}"><i class="fa pull-left fa-cogs"></i>Панель управления</a>
							@endif
							<a href="{{ url('/auth/logout') }}"><i class="fa pull-left fa-sign-out"></i>Выйти</a>
						</div>
					</div>
				@endif
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
	
	
	@if (Session::has('message'))
		<script>
			alert('{{ Session::get('message') }}');
		</script>
	@endif
		
		
	
	
</body>
</html>