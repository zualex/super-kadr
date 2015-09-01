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
        <a class="navbar-brand" href="{{ URL::to('admin') }}">Рабочий стол</a>
    </div>
    <ul class="nav navbar-nav">
        <li><a href="{{ URL::to('admin/users') }}">Пользователи</a>
    </ul>
</nav>


	@yield('content')

	
	
</div>
</body>
</html>
