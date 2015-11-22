@extends('app')

@section('content')
<div class="wrapper">
	<div class="block">
		@if($head_name)
			<div class="header"><span>{{ $head_name }}</span></div>
		@else
			<div class="header"><span>Ваш заказ</span></div>
		@endif
	
		<div class="body clear">
			<div class="text status">
				<span>ОПЛАТА ПРОШЛА УСПЕШНО!  СПАСИБО !  Желаем вам хорошего настроения!</span>
				<div class="share">
					<div class="description"><span>поделитесь с друзьями:</span></div>
					<div class="buttons">
						<a 
							href="#" 
							class="icon social vk" 
							onclick="window.open('http://share.yandex.ru/go.xml?service=vkontakte&amp;url={{ route('gallery.show', $gallery->id) }}&amp;title=Голосуй за меня! С ЛЮБОВЬЮ, твой Супер-Кадр!&amp;image={{ Request::root().$pathImages.'/m_'.$gallery->src  }}');return false;">
							<i class="fa fa-vk"></i>
						</a>
						<a href="#" class="icon social in" style="display:none"><i class="fa fa-instagram"></i></a>
						<a href="#" class="icon social fb"
						onclick="window.open('http://share.yandex.ru/go.xml?service=facebook&amp;url={{ route('gallery.show', $gallery->id) }}&amp;title=Голосуй за меня! С ЛЮБОВЬЮ, твой Супер-Кадр!&amp;image={{ Request::root().$pathImages.'/m_'.$gallery->src  }}');return false;"><i class="fa fa-facebook"></i></a>
						<a href="#" class="icon social tw"
						onclick="window.open('http://share.yandex.ru/go.xml?service=twitter&amp;url={{ route('gallery.show', $gallery->id) }}&amp;title=Голосуй за меня! С ЛЮБОВЬЮ, твой Супер-Кадр!&amp;image={{ Request::root().$pathImages.'/m_'.$gallery->src  }}');return false;"><i class="fa fa-twitter"></i></a>
						<a href="#" class="icon social gg"
						onclick="window.open('http://share.yandex.ru/go.xml?service=gplus&amp;url={{ route('gallery.show', $gallery->id) }}&amp;title=Голосуй за меня! С ЛЮБОВЬЮ, твой Супер-Кадр!&amp;image={{ Request::root().$pathImages.'/m_'.$gallery->src  }}');return false;"
						><i class="fa fa-google-plus"></i></a>
					</div>
				</div>
			</div>
		</div>
	
	</div>
</div>
@endsection