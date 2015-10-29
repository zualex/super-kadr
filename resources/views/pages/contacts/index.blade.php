@extends('app')

@section('content')
<div class="wrapper">
	<div class="block">
		<div class="header"><span>Контакты</span></div>
		<div class="body clear">
			<div class="text">
				<div class="contacts">
					<span>
					<p><b>Адрес:</b> г. Брянск, Гаражный переулок, д. 2</p>
					<p><b>Телефон:</b> 8-910-3333-748</p>
					<p><b>E-mail:</b> super-kadr32@mail.ru</p>
				</div>
				<div id="map"></div>
				<script type="text/javascript" charset="utf-8" src="https://api-maps.yandex.ru/services/constructor/1.0/js/?sid=g73z2Ch7pV0RE0Xgj2QX_Ow2gZr4FjJY&id=map&lang=ru_RU&sourceType=constructor"></script>
			</div>
		</div>
	</div>
</div>
@endsection