<!DOCTYPE html>
<html>
<head>
    <title>Административная панель</title>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">

<nav class="navbar navbar-inverse">
    <div class="navbar-header">
        <a class="navbar-brand" href="{{ URL::to('/') }}">Вернуться на сайт</a>
    </div>
    <ul class="nav navbar-nav">
        <li><a href="{{ route('admin.users.index') }}">Пользователи</a>
        <li><a href="{{ route('admin.users.index') }}">Настройки</a>
        <li><a href="{{ route('admin.users.index') }}">Транзакции</a>
        <li><a href="{{ route('admin.users.index') }}">Заказы</a>
        <li><a href="{{ route('admin.users.index') }}">Плейлисты</a>
        <li><a href="{{ URL::to('auth/logout') }}">Выход</a>
    </ul>
</nav>

	
	@yield('content')

	
	
</div>
</body>
</html>
